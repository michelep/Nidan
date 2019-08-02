# Nidan
#
# (C) 2017 Michele <o-zone@zerozone.it> Pinassi
import jsonpickle
import requests

from config import Config

class RESTClient:
    def __init__(self,api_key,url):
	self.api_key = api_key
	self.url = url

    def getHeaders(self):
	headers = {'user-agent': 'nidan/'+Config.agent_version, 'X-Authentication-Key': self.api_key}
	return headers

    def error(self):
	return self.req.text

    def post(self, method, args=None):
	self.req = requests.post(self.url+method, data=args, headers=self.getHeaders(), verify=False)
	if self.req.status_code == requests.codes.ok:
	    return True

    def get(self, method, args=None):
	self.req = requests.get(self.url+method, data=args, headers=self.getHeaders(), verify=False)
	if self.req.status_code == requests.codes.ok:
	    return True
