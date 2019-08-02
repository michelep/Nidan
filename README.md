Nidan
=====

Introduction
------------

Nidan is a personal network scanning tool, for use in public and private networks. Nidan continuosly checks for new hosts and new services in specified networks, and tries grabbing TCP/UDP port banners. All data is saved into a MySQL/MariaDB database engine.

How Nidan works
---------------

Nidan consists in a scanning agent, written in Python, and a web controller, written in PHP. Nidan scanning agent uses a REST interface to fetch jobs from the controller, so you can install it into a different server (also behind a NAT !). In most case, both are on the same server.

Nidan agent needs to be run as root to be able to fetch ARP requests from interfaces.

## Prerequisites 

For Web frontend and REST server:
* PHP 5.x or 7.x
* Apache 2.4.x 
* MySQL 5.x or MariaDB

For Agents:
* Python schedule (pip install schedule - https://schedule.readthedocs.io/en/stable)
* Python jsonpickle (pip install jsonpickle)
* Python requests (pip install requests)
* Python Scapy (pip install scapy - https://scapy.net)

## Install Web frontend and REST server

![Summary page](assets/screenshot_4.jpg "Summary page")

Create a new database ('nidan' ?) and a user with rights to create and use the tables in it. If you need help, please check [How To Create a New User and Grant Permissions in MySQL](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql)

Now prepare the web folder, copying all /web content to the web server root folder (usually /var/www/) or wherever you want to store PHP pages.
Enable apache2's mod-rewrite ('a2enmod rewrite' as root) and check/change the db access configuration in config.inc.php:

    $CFG["db_host"] = "localhost";
    $CFG["db_port"] = "3306";
    $CFG["db_user"] = "nidan";
    $CFG["db_password"] = "nidan";
    $CFG["db_name"] = "nidan";

Then enable "AllowOverride All" in the nidan virtual host instance, like so:

    <VirtualHost _default_:80>
	ServerAdmin webmaster@localhost

	DocumentRoot /var/www/html

	ErrorLog ${APACHE_LOG_DIR}/nidan-error.log
	CustomLog ${APACHE_LOG_DIR}/nidan-access.log combined

	<Directory /var/www/html>
    	    AllowOverride All
	</Directory>
    </VirtualHost>

If you want to use SSL, remember to enable the ssl module ('a2enmod ssl' as root) and change the VirtualHost block as follow:

    <IfModule mod_ssl.c>
	<VirtualHost _default_:443>
	    ServerAdmin webmaster@localhost

	    <Directory /home/web/default>
		Require all granted
	    </Directory>

	    DocumentRoot /home/web/default

	    ErrorLog ${APACHE_LOG_DIR}/nidan-error.log
	    CustomLog ${APACHE_LOG_DIR}/nidan-access.log combined

	    SSLEngine on

	    SSLCertificateFile /etc/ssl/certs/apache-selfsigned.crt
            SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key

            <FilesMatch "\.(cgi|shtml|phtml|php)$">
                SSLOptions +StdEnvVars
            </FilesMatch>
            <Directory /usr/lib/cgi-bin>
                SSLOptions +StdEnvVars
            </Directory>

            <Directory /home/web/default>
		AllowOverride All
	    </Directory>

            BrowserMatch "MSIE [2-6]" \
                   nokeepalive ssl-unclean-shutdown \
                   downgrade-1.0 force-response-1.0
	</VirtualHost>
    </IfModule>

You can use self-signed SSL certs ([How To Create a Self-Signed SSL Certificate for Apache](https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-apache-in-ubuntu-16-04)) 
or use [Let's Encrypt](https://letsencrypt.org/) to get a free, valid certificate.

Now you should add a new cron instance, usually with 'crontab -e', that runs the cron.php script every 5 minutes. An example configuration can be the following:

    # m h  dom mon dow   command
    */5 * * * * php /var/www/html/cron.php &> /dev/null

Open a browser and go to your web server: you should be redirected to the /install page, where you can check the database connection and install the tables and some default data.

Finally, you will be able to log in with the default username "admin@localhost" and password "admin".

![Web signin page](assets/screenshot_2.jpg "Web signin page")

## Install Agents

Firstly, add a new Agent from the "Agents" page and write down the API key. Now you can use setuptools to install Nidan Agents, so run:

    python setup.py build
    python setup.py install

then copy nidan.cfg file to /etc/nidan/nidan.cfg or ~/.nidan.cfg. Edit the file and fill the apiKey field with the API key you wrote down earlier:

    [Agent]
    api_key=*[this agent API key]*
    server_url=*[URL of the server - i.e. https://localhost/rest]*
    pid_file=/tmp/nid_agent.pid
    log_file=/var/log/nidan.log
    threads_max=5
    sleep_time=10

remember to change serverUrl if needed! Then save and run nidan.py.

Please note that if you use "https", the agent will try to connect using the default SSL port (TCP 443).

![Agent at work](assets/screenshot_1.jpg "Agent at work")

## ChangeLog

0.0.4 - 02 Aug 2019
* [Web Gui] Improved install procedure and prevent accidental re-installation
* [Web Gui] Update Bootstrap framework to 4.3.1
* [Web Gui] Minor fixes and cleanups
* [Agent] Moved to only one agent based on Scapy (https://scapy.net/)
* [Agent] Added net_scan method
* [Agent] Some improvements and bug fixes

0.0.3 - 22 May 2018
* [Web Gui] Start internationalization
* [Web Gui] Move to Boostrap 4.1 and Charts.js 2.7.1
* [Web Gui] Added user groups management
* [Web Gui] Improved host page
* [Web Gui] Adding custom events to hosts, like issue, changes and so on
* [Web Gui] Minor fixed and cleanups
* [Agent Arp] Added hostname resolution for detected hosts
* [DB] Added Groups table
* [DB] Added UserGroups table
* [DB] Added HostsLog table for host related events

0.0.2 - 22 Dec 2017 - Merry Xmas !
* [Web Gui] Added Inbox for event notifications (added in trigger too)
* [Web Gui] Some improvements with search by IP
* [Web Gui] Hostname defined in configuration
* [Web Gui] Lot of changes and bug fixes
* [DB] Changes in Agents table
* [Agent] Some minor cleanups
* [Agent] Added arp sniffer agent (beta)
* [Agent] Start working on plugin capabilities for agents

0.0.1 - 18 Oct 2017
* [Web Gui] Installer improved
* [Web Gui] Some minor fixes and improvements
* [Agent] Added null logging handler

0.0.1rc9 - 13 Oct 2017
* [Web Gui] Added cron watchdog
* [Web Gui] Fix pagination bug
* [Web Gui] Some improvements in cron script
* [Web Gui] Miscellaneous minor bugs fixed and some improvements
* [DB] Added primary key ID in EventsLog table

0.0.1rc8 - 02 Oct 2017
* [Web Gui] Fix notification and some other minor bugs
* [Web Gui] Added cleanup for old events
* [Web Gui] Fix some bugs in users management
* [Web Gui] Version check
* [Agent] Added multithreading parallel scanning (define threads_max in config file)
* [Agent] Removed daemonizing support, because of signaling issue
* [DB] Added new fields in Config and JobsQueue tables

0.0.1rc7 - 28 Sep 2017
* [Web Gui] Fixed some minor bugs
* [Web Gui] Some improvements in installer
* [Web Gui] Added "send test e-mail" in configuration page
* [Agent] Added setup.py for easy building of agents

0.0.1pre6 - 25 Sep 2017
* [Web Gui] Fixed a bug on DB version comparison
* [Web Gui] Added support for PHP Console debugging tool (https://github.com/barbushin/php-console)
* [Web Gui] Fix a typo in login procedure
* [DB] Some changes on DB structure, move to 0.0.1pre6

0.0.1pre5 - 18 Sep 2017
* [Web Gui] Minor cleanups and fixes
* [Agent] Add support for daemonizing,logging and version check

0.0.1pre4 - 15 Sep 2017
* [Web Gui] Timeout check for agents and new trigger 'agent_offline'
* [Web Gui] Some improvements in trigger handling
* [Web Gui] Implemented trigger deletion
* [Web Gui] Updated phpmailer to latest release
* [Web Gui] Some configuration moved to DB table
* [Web Gui] Added users rights support
* [Agent] Added support for dynamic scanning method, passing custom args on job

0.0.1pre3 - 11 Sep 2017
* [Web Gui] Fix an issue in search result page
* [Web Gui] Minor fixes
* [Web Gui] Fixed a bug in net and host comparison
* [Agent] Disabled invalid SSL Certificate warning

0.0.1pre2 - 5 Sep 2017
* [Web Gui] Added pagination in Hosts page
* [Web Gui] Nidan summary with cards in first page
* [Web Gui] Search for hosts and services now work
* [Agent] Basic banner grabbing now works. Needs more improvements, like retry on timeout and service detection: added in TODO list
* [Agent] Other minor changes

0.0.1pre1 - 4 Sep 2017
* First public pre-release


## Troubleshoting

If an agent failed to connect with "requests.exceptions.ConnectionError: HTTPSConnectionPool(host='localhost', port=443): Max retries exceeded with url: /rest/agent/start (Caused by NewConnectionError('<urllib3.connection.VerifiedHTTPSConnection object at 0x7f43258d1150>: Failed to establish a new connection: [Errno 111] Connection refused',))" you should double-check your apache2 SSL configuration.

If an agent raise an exception with this error: "Unexpected error: (<class 'nmap.nmap.PortScannerError'>, PortScannerError exception nmap program was not found in path." you may need to install nmap with 'apt install nmap'.

## Need support?

Join our public ML [nidan-users-ml](https://groups.google.com/forum/#!forum/nidan-users-ml "Nidan users ML") to help and receive support from users and developers.

## Want to support Nidan developement? Get a shirt!

Developing Nidan costs time and money. Please support us buying a [Nidan t-shirts](https://shop.spreadshirt.it/Nidan/) or [donate via PayPal](https://PayPal.Me/MichelePinassi)

## Author

Nidan was written by Michele <o-zone@zerozone.it> Pinassi

## License

Nidan is under MIT license and provided without any kind of warranty. Please use responsibly.
