# Nidan
#
# (C) 2017 Michele <o-zone@zerozone.it> Pinassi

import sys
import time
import socket
import jsonpickle
import Queue

from config import Config
from scapy.all import *

try:
    from pysnmp.entity.rfc3413.oneliner import cmdgen
except Exception, e:
    print("You need to download pysnmp and pyasn1", e)
    sys.exit(1)

class Scanner:
    def __init__(self):
	pass
    
    def host_scan(self, job_id, args):

	host_ip = args['host_addr']
	scan_method = len(args['scan_method']) and args['scan_method'] or None

	Config.log.debug('Start scanning of host %s with %s' % (host_ip,scan_method))

	starttime = time.time();

	self.get_snmp(host_ip, job_id)

	src_port = RandShort()
	dst_ports = [1,5,7,18,20,21,22,23,25,29,37,42,43,49,53,69,70,79,80,103,108,109,110,115,118,119,137,139,143,150,156,161,179,190,194,197,389,396,443,444,445,458,546,547,563,569,1080,3182,8000,8001,8080]
	dst_timeout = 5

	for dst_port in dst_ports:
	    banner = ''
	    proto = 'tcp'
	    # Stealth TCP scan
	    stealth_scan_resp = sr1(IP(dst=host_ip)/TCP(sport=src_port,dport=dst_port,flags="S"),timeout=dst_timeout,verbose=False)
	    if(str(type(stealth_scan_resp))=="<type 'NoneType'>"):
		state = 'filtered'
	    elif(stealth_scan_resp.haslayer(TCP)):
		if(stealth_scan_resp.getlayer(TCP).flags == 0x12):
		    send_rst = sr(IP(dst=host_ip)/TCP(sport=src_port,dport=dst_port,flags="R"),timeout=dst_timeout,verbose=False)
		    state = 'open'
		    try:
			banner = self.get_banner(host_ip,dst_port)
		    except:
			banner = ''
		elif (stealth_scan_resp.getlayer(TCP).flags == 0x14):
		    state = None
	        elif(stealth_scan_resp.haslayer(ICMP)):
		    if(int(stealth_scan_resp.getlayer(ICMP).type)==3 and int(stealth_scan_resp.getlayer(ICMP).code) in [1,2,3,9,10,13]):
			# print "Port "+str(dst_port)+"/TCP: Filtered"
			state = 'filtered'
	    
	    if state is not None:
		Config.log.debug('port: %s\tstate: %s\tbanner: %s' % (dst_port,state,banner))
		# Send service details to Nidan controller
		Config.client.post('/service/add',{"job_id": job_id, "ip": host_ip, "port": dst_port, "proto": proto, "state": state, "banner": banner})

	scantime = time.time() - starttime;

	Config.log.debug('Scan END in %d secs' % (scantime))

	return scantime

    def get_banner(self, ip, port):
	try:
	    s = socket.socket()
	    s.settimeout(5.0)
	    s.connect((ip,port))
	    # Connection done !
	    # s.send("\r\n");
	    banner = str(s.recv(1024))
    	    return banner
	except socket.error as e:
	    Config.log.debug('Banner error: %s' % (e))
	    return None
	except socket.timeout:
	    Config.log.debug('Banner timeout')
	    return None

    def get_snmp(self, ip, job_id):
	try:
	    cmdGen = cmdgen.CommandGenerator()

	    errorIndication, errorStatus, errorIndex, varBinds = cmdGen.getCmd(
		cmdgen.CommunityData('public'),
		cmdgen.UdpTransportTarget(('ip', 161)),
		cmdgen.MibVariable('SNMPv2-MIB', 'sysDescr', 0),
    	        lookupNames=True, lookupValues=True
	    )

	    # Check for errors and print out results
	    if errorIndication:
	        Config.log.debug('SNMP Get error: %s'%errorIndication)
	    else:
		if errorStatus:
		    Config.log.debug('SNMP Get error %s at %s' % (errorStatus.prettyPrint(), errorIndex and varBinds[int(errorIndex)-1] or '?'))
	        else:
		    for name, val in varBinds:
        		Config.log.debug('%s = %s' % (oid.prettyPrint(), val.prettyPrint()))
			Config.client.post('/snmp/get',{"job_id": job_id, "ip": ip, "sysDesc": val.prettyPrint()})
	except:
	    Config.log.debug('SNMP get timeout')
	    return None
	
    def snmp_get(self, job_id, args):
	host_ip = args['host_addr']

	Config.log.debug('Start SNMP get for host %s' % (host_ip))

	starttime = time.time();

	snmp_get = IP(dst=host_ip)/UDP(sport=161)/SNMP(community="public",PDU=SNMPget(varbindlist=[SNMPvarbind(oid=ASN1OID("1.3.6.1"))]))

	snmp_resp = sr1(snmp_get, verbose=0, timeout=10)

	if snmp_resp:
	    Config.client.post('/snmp/get',{"job_id": job_id, "ip": host_ip, "snmp": snmp_resp})
	else:
	    pass
	    
	scantime = time.time() - starttime;

	Config.log.debug('SNMP get END in %d secs' % (scantime))
    
	return scantime


class scannerJob:
    def __init__(self):
	res = Config.client.post('/job/get')

	if res == True:
	    json = jsonpickle.decode(Config.client.req.text)

	    # [success] should be > 0 if there's a job for me...
	    if int(json['success']) > 0:
		job_id = json['job_id']
		job = json['job_type']
		args = json['job_args']
	
		scanner = Scanner()
		Config.log.debug("Starting JOB %s..."%job_id);

	        try:
		    method = getattr(Scanner, job)
		    ret = method(scanner,job_id,args)
		    Config.client.post("/job/set/%s"%(job_id),{"status": "complete", "scantime": ret})
		except:
	    	    Config.log.error("JOB %s - Unexpected error: %s"%(job_id,sys.exc_info()))
	    	    Config.client.post("/job/set/%s"%(job_id),{"status": "error", "reason": sys.exc_info()})

	    else:
		# No jobs yet
		pass
	else:
	    pass

