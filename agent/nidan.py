#!/usr/bin/env python
#
# Nidan
# 0.0.1rc7
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

import daemon
import lockfile

import urllib3
# Disable invalid SSL certs warning
urllib3.disable_warnings()

import ConfigParser

from Nidan.nidan import *
from Nidan.config import Config
from Nidan.restclient import RESTClient


is_run = True

# Stop gracefully
def handler_stop_signals(signum, frame):
    global is_run

    Config.client.post("/agent/stop",{"reason":signum})
    Config.log.info("Agent stop");

    is_run = False

if __name__ == '__main__':

    Config.config_file = ['/etc/nidan/nidan.cfg', os.path.expanduser('~/.nidan.cfg')]

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
    
    Config.agent_version = "0.0.1rc7"

    Config.agent_hostname = socket.gethostname()
    Config.agent_apikey = conf.get('Agent','apiKey')
    Config.server_url = conf.get('Agent','serverUrl')
    Config.pid_file = conf.get('Agent','PIDfile')
    Config.log_file = conf.get('Agent','LOGfile')
    Config.daemonize = conf.get('Agent','daemonize')

    logging.basicConfig(level=logging.DEBUG, format="%(asctime)s;%(levelname)s;%(message)s")
    flh = logging.FileHandler(Config.log_file, "w")
    Config.log = logging.getLogger(__file__)
    Config.log.addHandler(flh)

    # Setup client connection with Nidan server
    Config.client = RESTClient(Config.agent_apikey, Config.server_url)

    if os.path.isfile(Config.pid_file):
	print "PID file exists"
	sys.exit()

    if os.geteuid() != 0:
	print "You are running nidan as non-privileged user: that's ok *but* remember i should use non-root scanning methods and some info, like MAC, will be missing..."
	Config.net_scan_args = '-sP -PE -PA21,23,80,3389'
	Config.host_scan_args = ''
    else:
	print "You are running nidan as root: well, we try using stealth scanning..."
	Config.net_scan_args = '-sP -PE -PA21,23,80,3389'
	Config.host_scan_args = '-sS'

    # Catch program termination
    signal.signal(signal.SIGINT, handler_stop_signals)
    signal.signal(signal.SIGTERM, handler_stop_signals) # CTRL+C
    signal.signal(signal.SIGTSTP, handler_stop_signals) # CTRL+Z

    # Check for NMap...
    try:
	nm = nmap.PortScanner() # instantiate nmap.PortScanner object
    except:
	print "NMap not found - please install nmap and python-nmap to use network scanner capabilities"
	pass

    req = Config.client.post("/agent/start",{"hostname":Config.agent_hostname,"version":Config.agent_version})
    if req == True:
	print "Successfully registered to Nidan server %s !"%(Config.server_url)
	Config.log.info("Agent registered to Nidan controller %s"%(Config.server_url));
    else:
	print "Unable to connect to Nidan server %s: %s"%(Config.server_url,Config.client.error())
	Config.log.error("Unable to connect to Nidan controller %s"%(Config.server_url));
	sys.exit(2)

    # Daemonize...
    if Config.daemonize == 1:
	context = daemon.DaemonContext(
	    pidfile=lockfile.FileLock(Config.pid_file)
	)

	with context:
	    Nidan()
    # ...or not !
    else:
	NidanLoop()

        schedule.every(1).minutes.do(NidanLoop)

	while is_run:
	    schedule.run_pending()
	    time.sleep(1)
