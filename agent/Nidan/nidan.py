# Nidan
#
# (C) 2017 Michele <o-zone@zerozone.it> Pinassi

import socket
import struct
import sys
import jsonpickle

from config import Config

from scanner import *

class Nidan:
    def __init__(self):
	res = Config.client.post('/job/get')

	if res == True:
	    print Config.client.req.text

# {
#    "success": "1",
#    "job_id": "1",
#    "job_type": "net_scan",
#    "job_args": {
#        "network": "127.0.0.0\/24"
#    }
# }

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
	    	    print "JOB %s - Unexpected error: %s"%(job_id,sys.exc_info())
	    	    Config.client.post("/job/set/%s"%(job_id),{"status": "error", "reason": sys.exc_info()})

	    else:
		print "No jobs for me...yet !"
	else:
	    pass
