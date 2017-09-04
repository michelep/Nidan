Nidan
=====

Introduction
------------

Nidan is a personal network scanning tool, for use in public and private networks. Nidan continuosly checks for new hosts and new services in 
specified networks, try grabbing TCP/UDP port banners. All data is saved into a MySQL/MariaDB database engine.

How Nidan works
---------------

Nidan consists in a scanning agent, written in Python, and a GUI, written in PHP. Nidan scanning agent use MySQL/MariaDB DBMS, so you can install 
it into a different server (ensure to setup correctly connection to the DBMS). In most case, both are on the same server.

Nidan agent can be run also as non-privileged users, but scanning tecniques will be not accurate as running as root

Installation
------------

## Prerequisites 

For Web frontend and REST server:
* Python 2.x
* PHP 5.x
* Apache 2.4.x
* MySQL 5.x or MariaDB

For Agents:
* NMap 6.x
* Python-nmap module, at least 0.6.1 (pip install python-nmap - https://pypi.python.org/pypi/python-nmap)
* Python schedule (pip install schedule - https://schedule.readthedocs.io/en/stable/)
* Python jsonpickle (pip install jsonpickle)

![Hosts detected](./assets/screenshot_3.png)

## Install Web frontend and REST server

Create a new database ('nidan' ?) and use /sql/nidan.sql to recreate tables. Copy all /web content to web server root folder (usually /var/www/), 
enable apache2 mod-rewrite if not ('a2enmod rewrite' as root) and check/change db access configuration in common.inc.php

Open a browser and go to your web server. Default username:password is "admin@localhost:admin". 

![Login page](./assets/screenshot_2.png)

## Install Agents

Copy all files under 'agent' folder where you want to run an agent (also on the same machine as frontend). Open nidan.cfg with a text editor ('nano' is ok) and configure:

`[Agent]
apiKey=*[this agent API key]*
serverUrl=*[URL of the server - i.e. https://localhost/rest]*`

then save and run nidan.py

![Agent while scanning](./assets/screenshot_1.png)

## Troubleshoting

If an job failed with "Unexpected error: (<type 'exceptions.AttributeError'>, AttributeError("'module' object has no attribute 'PortScanner'",)" maybe you have to install python-nmap module. Run "sudo pip install python-nmap" to fix.

## Changelog

v0.0.1pre1 (4 Sep 2017) - First alpha-stage public release - Still WIP !

## Author

Nidan was written by Michele <o-zone@zerozone.it> Pinassi

## License

Nidan is under MIT license. No any warranty. Please use responsibly.

