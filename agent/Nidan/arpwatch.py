from __future__ import print_function
from scapy.all import *

def arp_display(pkt):
    if pkt[ARP].op == 1:  # who-has (request)
        return 'Request: {} is asking about {}'.format(pkt[ARP].psrc, pkt[ARP].pdst)
    if pkt[ARP].op == 2:  # is-at (response)
        return '*Response: {} has address {}'.format(pkt[ARP].hwsrc, pkt[ARP].psrc)
 
sniff(prn=arp_display, filter='arp', store=0)
