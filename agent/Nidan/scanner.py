# Nidan
#
# (C) 2017 Michele <o-zone@zerozone.it> Pinassi

import sys
import nmap
import time
import socket
from config import Config

class Scanner:
    def __init__(self):
	pass
    
    def net_scan(self, job_id, args):
	net_addr = args['net_addr']
	
	Config.log.debug('Start scanning of network %s' % (net_addr))

	starttime = time.time();

	nm = nmap.PortScanner() # instantiate nmap.PortScanner object
	nm.scan(hosts=net_addr, arguments=Config.net_scan_args)

	for host_ip in nm.all_hosts():
	    Config.log.debug('Host %s (%s) is %s' % (host_ip, nm[host_ip].hostname(), nm[host_ip].state()))

	    if 'mac' in nm[host_ip]['addresses']:
		host_mac = nm[host_ip]['addresses']
		host_vendor = nm[host_ip]['vendor']
	    else:
		host_mac = ''
		host_vendor = ''

	    # Send host discovery to Nidan controller
	    Config.client.post('/host/add',{"job_id": job_id, "ip": host_ip, "hostname": nm[host_ip].hostname(), "mac": host_mac, "vendor": host_vendor, "state": nm[host_ip].state()})
	
	scantime = time.time() - starttime;

	Config.log.debug('Scan END in %d secs' % (scantime))

	return scantime

    def host_scan(self, job_id, args):

	host_addr = args['host_addr']

	Config.log.debug('Start scanning of host %s' % (host_addr))

	starttime = time.time();

	nm = nmap.PortScanner() # instantiate nmap.PortScanner object
	nm.scan(hosts=host_addr, arguments=Config.host_scan_args) # scan host

	for host_ip in nm.all_hosts():
	    for proto in nm[host_ip].all_protocols():
		Config.log.debug('Protocol : %s' % proto)

		lport = nm[host_ip][proto].keys()
		lport.sort()
		for port in lport:
		    # Try getting banner...
		    try:
			banner = self.get_banner(host_ip,port)
		    except:
			banner = ''

		    Config.log.debug('port: %s\tstate: %s\tbanner: %s' % (port, nm[host_ip][proto][port]['state'], banner))

		    # Send service details to Nidan controller
		    Config.client.post('/service/add',{"job_id": job_id, "ip": host_ip, "port": port, "proto": proto, "state": nm[host_ip][proto][port]['state'], "banner": banner})

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