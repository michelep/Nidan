#!/usr/bin/env python
#
# Nidan
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

import urllib3
urllib3.disable_warnings()

import ConfigParser

from Nidan.nidan import *
from Nidan.config import Config
from Nidan.restclient import RESTClient

logging.basicConfig(level=logging.DEBUG, format="%(message)s")
Config.log = logging.getLogger(__file__)

conf = ConfigParser.ConfigParser()
conf.read('nidan.cfg')

Config.agent_hostname = socket.gethostname()
Config.agent_apikey = conf.get('Agent','apiKey')
Config.server_url = conf.get('Agent','serverUrl')

################################################################################### CONFIGURATION START
# Default values

# Server properties
#Config.agent_apikey = "ac0365a9173ffa491e05815c5d0e50fd"

################################################################################### CONFIGURATION END

# Setup client connection with Nidan server
Config.client = RESTClient(Config.agent_apikey, Config.server_url)

is_run = True

# Stop gracefully
def handler_stop_signals(signum, frame):
    global is_run

    Config.client.post("/agent/stop",{"reason":signum})

    is_run = False

if __name__ == '__main__':
    try:
	opts, args = getopt.getopt(sys.argv[1:],"hs:",["server:"])
    except getopt.GetoptError:
	print 'nidan.py [-h for help] [-s Nidan server]'
	sys.exit(2)
    for opt, arg in opts:
	if opt == '-h':
	    print 'nidan.py [-h for help] [-s Nidan server]'
	    sys.exit()
	elif opt in ("-s", "--server"):
	    Config.server_url = arg

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

    req = Config.client.post("/agent/start",{"hostname":Config.agent_hostname})
    if req == True:
	print "Successfully registered to Nidan server %s !"%(Config.server_url)
    else:
	print "Unable to connect to Nidan server %s: %s"%(Config.server_url,Config.client.error())
	sys.exit(2)

    Nidan()

    schedule.every(1).minutes.do(Nidan)

    while is_run:
        schedule.run_pending()
        time.sleep(1)

