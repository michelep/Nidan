-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Ott 13, 2017 alle 15:50
-- Versione del server: 10.0.32-MariaDB-0+deb8u1
-- PHP Version: 5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nidan`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Agents`
--

DROP TABLE IF EXISTS `Agents`;
CREATE TABLE IF NOT EXISTS `Agents` (
`ID` int(11) NOT NULL,
  `Name` varchar(16) NOT NULL,
  `apiKey` varchar(64) NOT NULL,
  `Description` text NOT NULL,
  `IP` varchar(32) DEFAULT NULL,
  `Hostname` varchar(64) DEFAULT NULL,
  `Version` varchar(16) DEFAULT NULL,
  `isEnable` tinyint(1) NOT NULL DEFAULT '0',
  `isOnline` tinyint(1) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `lastSeen` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Config`
--

DROP TABLE IF EXISTS `Config`;
CREATE TABLE IF NOT EXISTS `Config` (
  `Name` varchar(16) NOT NULL,
  `Value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Config`
--

INSERT INTO `Config` (`Name`, `Value`) VALUES
('events_keep', '1440'),
('mail_from_mail', 'nidan@localhost'),
('mail_from_name', 'Nidan'),
('mail_server_host', 'localhost'),
('mail_server_port', '25'),
('mail_template', '<style>\r\np {\r\n    text-align: justify;\r\n}\r\n\r\ntable { border-collapse: collapse; }\r\nth { border-bottom: 1px solid #CCC; border-top: 1px solid #CCC; background-color: #EEE; padding: 0.5em 0.8em; text-align: center; font-weight:bold; }\r\ntd { border-bottom: 1px solid #CCC;padding: 0.2em 0.8em; }\r\ntd+td { border-left: 1px solid #CCC;text-align: center; }\r\n</style>\r\n<div style=''padding: 5px;''>\r\n%body%\r\n</div>\r\n<div style=''width:100%; border-top: 1px solid #ccc; background-color: #eee; padding: 5px; text-align: center;''>\r\n<b>Nidan</b> @ %host%</div>'),
('version', '0.0.1');

-- --------------------------------------------------------

--
-- Struttura della tabella `EventsLog`
--

DROP TABLE IF EXISTS `EventsLog`;
CREATE TABLE IF NOT EXISTS `EventsLog` (
`ID` int(11) NOT NULL,
  `addDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agentId` int(11) NOT NULL,
  `jobId` int(11) DEFAULT NULL,
  `Event` varchar(16) NOT NULL,
  `Args` text
) ENGINE=InnoDB AUTO_INCREMENT=92771 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Hosts`
--

DROP TABLE IF EXISTS `Hosts`;
CREATE TABLE IF NOT EXISTS `Hosts` (
`ID` int(11) NOT NULL,
  `netId` int(11) NOT NULL,
  `agentId` int(11) DEFAULT NULL,
  `IP` varchar(32) NOT NULL,
  `MAC` varchar(64) NOT NULL,
  `Vendor` varchar(64) NOT NULL,
  `Hostname` varchar(64) NOT NULL,
  `Note` text NOT NULL,
  `State` varchar(16) NOT NULL,
  `isOnline` tinyint(1) NOT NULL DEFAULT '0',
  `isIgnore` tinyint(1) NOT NULL DEFAULT '0',
  `lastCheck` datetime DEFAULT NULL,
  `scanTime` mediumint(9) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `stateChange` datetime DEFAULT NULL,
  `checkCycle` int(6) DEFAULT NULL,
  `chgDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2829 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `JobsQueue`
--

DROP TABLE IF EXISTS `JobsQueue`;
CREATE TABLE IF NOT EXISTS `JobsQueue` (
`ID` int(11) NOT NULL,
  `Job` varchar(16) NOT NULL,
  `itemId` mediumint(9) NOT NULL,
  `agentId` int(11) NOT NULL,
  `Args` text,
  `Cache` text,
  `timeElapsed` decimal(10,3) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `scheduleDate` datetime DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=40463 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Log`
--

DROP TABLE IF EXISTS `Log`;
CREATE TABLE IF NOT EXISTS `Log` (
  `addDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Context` varchar(16) NOT NULL,
  `Message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Networks`
--

DROP TABLE IF EXISTS `Networks`;
CREATE TABLE IF NOT EXISTS `Networks` (
`ID` int(11) NOT NULL,
  `Network` varchar(32) NOT NULL,
  `Description` text,
  `Prefs` text,
  `isEnable` tinyint(1) NOT NULL DEFAULT '0',
  `agentId` int(11) NOT NULL DEFAULT '0',
  `scanTime` int(11) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `chgDate` datetime DEFAULT NULL,
  `lastCheck` datetime DEFAULT NULL,
  `checkCycle` int(6) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Services`
--

DROP TABLE IF EXISTS `Services`;
CREATE TABLE IF NOT EXISTS `Services` (
`ID` int(11) NOT NULL,
  `hostId` int(11) NOT NULL,
  `Port` int(11) NOT NULL,
  `Proto` varchar(3) NOT NULL,
  `State` varchar(16) NOT NULL,
  `Banner` text,
  `isIgnore` tinyint(1) NOT NULL DEFAULT '0',
  `addDate` datetime NOT NULL,
  `lastSeen` datetime DEFAULT NULL,
  `chgDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=11318 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `SessionMessages`
--

DROP TABLE IF EXISTS `SessionMessages`;
CREATE TABLE IF NOT EXISTS `SessionMessages` (
`ID` int(11) NOT NULL,
  `sessionId` varchar(64) NOT NULL,
  `Type` varchar(16) NOT NULL,
  `Message` text NOT NULL,
  `addDate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Sessions`
--

DROP TABLE IF EXISTS `Sessions`;
CREATE TABLE IF NOT EXISTS `Sessions` (
  `ID` varchar(64) NOT NULL,
  `IP` varchar(32) NOT NULL,
  `lastAction` datetime DEFAULT NULL,
  `userId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Stats`
--

DROP TABLE IF EXISTS `Stats`;
CREATE TABLE IF NOT EXISTS `Stats` (
  `addDate` datetime NOT NULL,
  `Item` varchar(16) NOT NULL,
  `itemId` int(11) DEFAULT NULL,
  `Value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Triggers`
--

DROP TABLE IF EXISTS `Triggers`;
CREATE TABLE IF NOT EXISTS `Triggers` (
`ID` int(11) NOT NULL,
  `agentId` int(11) DEFAULT NULL,
  `Event` varchar(16) NOT NULL,
  `Action` varchar(16) NOT NULL,
  `Priority` varchar(16) NOT NULL,
  `Args` text,
  `userId` mediumint(9) NOT NULL,
  `isEnable` tinyint(1) NOT NULL DEFAULT '0',
  `raisedCount` int(11) NOT NULL,
  `lastRaised` datetime DEFAULT NULL,
  `lastProcessed` datetime DEFAULT NULL,
  `addDate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE IF NOT EXISTS `Users` (
`ID` int(11) NOT NULL,
  `Name` varchar(32) NOT NULL,
  `Password` varchar(64) NOT NULL,
  `eMail` varchar(64) DEFAULT NULL,
  `Alias` varchar(32) NOT NULL,
  `ACL` text NOT NULL,
  `addDate` datetime NOT NULL,
  `lastLogin` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO `Users` (`ID`, `Name`, `Password`, `eMail`, `Alias`, `ACL`, `addDate`, `lastLogin`) VALUES
(1, 'admin@localhost', '*4ACFE3202A5FF5CF467898FC58AAB1D615029441', '', '', 'a:6:{s:8:"canLogin";b:1;s:11:"manageUsers";b:1;s:12:"manageSystem";b:1;s:14:"manageNetworks";b:1;s:12:"manageAgents";b:1;s:14:"manageTriggers";b:1;}', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Agents`
--
ALTER TABLE `Agents`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Config`
--
ALTER TABLE `Config`
 ADD PRIMARY KEY (`Name`);

--
-- Indexes for table `EventsLog`
--
ALTER TABLE `EventsLog`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Hosts`
--
ALTER TABLE `Hosts`
 ADD PRIMARY KEY (`ID`), ADD FULLTEXT KEY `Hostname` (`Hostname`,`Note`,`Vendor`);

--
-- Indexes for table `JobsQueue`
--
ALTER TABLE `JobsQueue`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Networks`
--
ALTER TABLE `Networks`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Services`
--
ALTER TABLE `Services`
 ADD PRIMARY KEY (`ID`), ADD FULLTEXT KEY `Banner` (`Banner`);

--
-- Indexes for table `SessionMessages`
--
ALTER TABLE `SessionMessages`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Sessions`
--
ALTER TABLE `Sessions`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Triggers`
--
ALTER TABLE `Triggers`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
 ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Agents`
--
ALTER TABLE `Agents`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `EventsLog`
--
ALTER TABLE `EventsLog`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `Hosts`
--
ALTER TABLE `Hosts`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `JobsQueue`
--
ALTER TABLE `JobsQueue`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `Networks`
--
ALTER TABLE `Networks`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `Services`
--
ALTER TABLE `Services`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `SessionMessages`
--
ALTER TABLE `SessionMessages`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `Triggers`
--
ALTER TABLE `Triggers`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
