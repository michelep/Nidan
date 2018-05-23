#!/usr/bin/env python
#
# Nidan
# 0.0.1
#
# (C) 2017 Michele <o-zone@zerozone.it> Pinassi

import os
import sys
import getopt
import signal
import socket
import logging

import time
import schedule
import jsonpickle
import threading

from collections import deque

import urllib3
# Disable invalid SSL certs warning
urllib3.disable_warnings()

import ConfigParser

from Nidan.scanner import *
from Nidan.config import Config
from Nidan.restclient import RESTClient

# Import Scapy
from scapy.all import *

mac_queue = deque([],maxlen=10)

Config.is_run = True

# Stop handler
def handler_stop_signals(signum, frame):
    Config.client.post("/agent/stop",{"reason":signum})
    Config.log.info("Agent stop");

    Config.is_run = False

# Scanner thread
class scannerThread(threading.Thread):
    def __init__(self, name):
	threading.Thread.__init__(self)
	self.name = name

    def run(self):
        scannerJob()

# ARP Sniffer thread
class snifferThread(threading.Thread):
    def __init__(self, name):
	threading.Thread.__init__(self)
	self.name = name
	self.mac_queue = deque([],maxlen=10)

    def run(self):
	# Run ARP sniffer
	sniff(prn=self.arp_monitor_callback, filter="arp", store=0)

    # ARP monitor callback
    def arp_monitor_callback(self,pkt):
	if ARP in pkt and pkt[ARP].op in (1,2): #who-has or is-at
	    host_mac = pkt.sprintf("%ARP.hwsrc%")
	    host_ip = pkt.sprintf("%ARP.psrc%")
	    host_name = ''

	    try:
                host_names = socket.gethostbyaddr(host_ip)
		host_name = host_names[0]
            except socket.herror:
		pass

	    if self.mac_queue.count(host_mac) == 0:
		# Send HOST to Nidan controller
		Config.log.debug('Host %s (Name: %s MAC: %s)' % (host_ip, host_name, host_mac))
	        Config.client.post('/host/add',{"job_id": 0, "ip": host_ip, "hostname": host_name, "mac": host_mac})
		self.mac_queue.append(host_mac)

# MAIN()
if __name__ == '__main__':
    Config.config_file = ['/etc/nidan/nidan.cfg', os.path.expanduser('~/.nidan.cfg'), 'nidan.cfg']

    try:
	opts, args = getopt.getopt(sys.argv[1:],"hs:c:",["server:config:"])
    except getopt.GetoptError:
	print 'nidan.py [-h for help] [-s Nidan server] [-c Config file]'
	sys.exit(2)
    for opt, arg in opts:
	if opt == '-h':
	    print 'nidan.py [-h for help] [-s Nidan server] [-c Config file]'
	    sys.exit()
	elif opt in ("-s", "--server"):
	    Config.server_url = arg
	elif opt in ("-c", "--config"):
	    Config.config_file = arg

    conf = ConfigParser.ConfigParser()
    conf.read(Config.config_file)
    
    Config.agent_version = "0.0.1"
    Config.plugins = "arp_sniffer,host_scan,snmp_get"

    Config.agent_hostname = socket.gethostname()
    Config.agent_apikey = conf.get('Agent','api_key')
    Config.server_url = conf.get('Agent','server_url')
    Config.pid_file = conf.get('Agent','pid_file')
    Config.log_file = conf.get('Agent','log_file')
    Config.threads_max = int(conf.get('Agent','threads_max'))
    Config.sleep_time = int(conf.get('Agent','sleep_time'))

    if Config.log_file:
	logging.basicConfig(level=logging.DEBUG, format="%(asctime)s;%(levelname)s;%(message)s")
	flh = logging.FileHandler(Config.log_file, "w")
	Config.log = logging.getLogger(__file__)
	Config.log.addHandler(flh)
    else:
	flh = logging.NullHandler()
	Config.log = logging.getLogger(__name__)

    # Setup client connection with Nidan server
    Config.client = RESTClient(Config.agent_apikey, Config.server_url)

    if os.path.isfile(Config.pid_file):
	print "PID file exists"
	sys.exit()

    if os.geteuid() != 0:
	print "This agent need to be run as root. Exiting."
	sys.exit()

    # Catch program termination
    signal.signal(signal.SIGINT, handler_stop_signals)
    signal.signal(signal.SIGTERM, handler_stop_signals) # CTRL+C
    signal.signal(signal.SIGTSTP, handler_stop_signals) # CTRL+Z

    req = Config.client.post("/agent/start",{"hostname":Config.agent_hostname,"version":Config.agent_version,"plugins":Config.plugins})
    if req == True:
	print "Successfully registered to Nidan server %s !"%(Config.server_url)
	Config.log.info("Agent registered to Nidan controller %s"%(Config.server_url));
    else:
	print "Unable to connect to Nidan server %s: %s"%(Config.server_url,Config.client.error())
	Config.log.error("Unable to connect to Nidan controller %s"%(Config.server_url));
	sys.exit(2)

    threads = []

    # Run ARP sniffer thread
    t = snifferThread("snifferThread")
    t.start()

    # Now run scanner threads
    while Config.is_run:
	for cnt in range(1,Config.threads_max):
	    t = scannerThread("scannerThread-%c"%cnt)
	    t.start()
    
	    threads.append(t)

	for t in threads:
    	    t.join()

	time.sleep(Config.sleep_time)
