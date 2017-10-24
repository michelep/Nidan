Nidan
=====

Introduction
------------

Nidan is a personal network scanning tool, for use in public and private networks. Nidan continuosly checks for new hosts and new services in specified networks, and tries grabbing TCP/UDP port banners. All data is saved into a MySQL/MariaDB database engine.

How Nidan works
---------------

Nidan consists in a scanning agent, written in Python, and a web controller, written in PHP. Nidan scanning agent uses a REST interface to fetch jobs from the controller, so you can install it into a different server (also behind a NAT !). In most case, both are on the same server.

Nidan agent can be run also as a non-privileged user, but scanning tecniques will be not accurate as running as root.

## Prerequisites 

For Web frontend and REST server:
* PHP 5.x or 7.x
* Apache 2.4.x
* MySQL 5.x or MariaDB

For Agents:
* NMap 6.x
* Python-nmap module, at least 0.6.1 (pip install python-nmap - https://pypi.python.org/pypi/python-nmap)
* Python schedule (pip install schedule - https://schedule.readthedocs.io/en/stable/)
* Python jsonpickle (pip install jsonpickle)
* Python requests (pip install requests)

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

## Troubleshoting

If a job failed with "Unexpected error: (<type 'exceptions.AttributeError'>, AttributeError("'module' object has no attribute 'PortScanner'",)" you are most likely missing the python-nmap module. Run "sudo pip install python-nmap" to fix this.

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
