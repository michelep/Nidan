import os
from setuptools import setup

# Utility function to read the README file.
# Used for the long_description.  It's nice, because now 1) we have a top level
# README file and 2) it's easier to type in the README file than to put a raw
# string in below ...
def read(fname):
    return open(os.path.join(os.path.dirname(__file__), fname)).read()

setup(
    name = "Nidan Agent",
    version = "0.0.1rc8",
    author = "Michele Pinassi",
    author_email = "o-zone@zerozone.it",
    description = ("Agent for Nidan network monitoring system"),
    license = "MIT License",
    keywords = "nidan network monitoring scanning nmap",
    url = "http://nidan.tk",
    scripts=['nidan.py'],
    packages=['Nidan'],
    long_description=read('../README.md'),
    install_requires=['python-nmap>=0.6.1','schedule','jsonpickle','requests'],
    classifiers=[
        "Development Status :: 2 - Pre-Alpha",
	"Topic :: System :: Networking :: Monitoring"
	"Environment :: Console",
	"Intended Audience :: Information Technology",
	"Programming Language :: Python",
        "License :: OSI Approved :: MIT License",
    ],
)
