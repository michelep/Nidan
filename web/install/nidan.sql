-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Mag 23, 2018 alle 10:52
-- Versione del server: 10.1.26-MariaDB-0+deb9u1
-- Versione PHP: 7.0.27-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nidan`
--
CREATE DATABASE IF NOT EXISTS `nidan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `nidan`;

-- --------------------------------------------------------

--
-- Struttura della tabella `Agents`
--

DROP TABLE IF EXISTS `Agents`;
CREATE TABLE `Agents` (
  `ID` int(11) NOT NULL,
  `Name` varchar(16) NOT NULL,
  `apiKey` varchar(64) NOT NULL,
  `Description` text NOT NULL,
  `IP` varchar(32) DEFAULT NULL,
  `Hostname` varchar(64) DEFAULT NULL,
  `Version` varchar(16) DEFAULT NULL,
  `Plugins` text,
  `isEnable` tinyint(1) NOT NULL DEFAULT '0',
  `isOnline` tinyint(1) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `startDate` datetime DEFAULT NULL,
  `stopDate` datetime DEFAULT NULL,
  `lastSeen` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Config`
--

DROP TABLE IF EXISTS `Config`;
CREATE TABLE `Config` (
  `Name` varchar(16) NOT NULL,
  `Value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `EventsLog`
--

DROP TABLE IF EXISTS `EventsLog`;
CREATE TABLE `EventsLog` (
  `ID` int(11) NOT NULL,
  `addDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jobId` int(11) DEFAULT NULL,
  `Event` varchar(16) NOT NULL,
  `Args` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Groups`
--

DROP TABLE IF EXISTS `Groups`;
CREATE TABLE `Groups` (
  `ID` int(11) NOT NULL,
  `Name` varchar(16) NOT NULL,
  `ACL` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Hosts`
--

DROP TABLE IF EXISTS `Hosts`;
CREATE TABLE `Hosts` (
  `ID` int(11) NOT NULL,
  `netId` int(11) NOT NULL,
  `agentId` int(11) DEFAULT NULL,
  `IP` varchar(32) NOT NULL,
  `MAC` varchar(64) NOT NULL,
  `Vendor` varchar(64) NOT NULL,
  `Hostname` varchar(64) NOT NULL,
  `Note` text NOT NULL,
  `State` varchar(16) DEFAULT NULL,
  `Type` varchar(16) DEFAULT NULL,
  `isOnline` tinyint(1) NOT NULL DEFAULT '0',
  `isIgnore` tinyint(1) NOT NULL DEFAULT '0',
  `lastSeen` datetime DEFAULT NULL,
  `lastCheck` datetime DEFAULT NULL,
  `scanTime` mediumint(9) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `stateChange` datetime DEFAULT NULL,
  `checkCycle` int(6) DEFAULT NULL,
  `chgDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `HostsLog`
--

DROP TABLE IF EXISTS `HostsLog`;
CREATE TABLE `HostsLog` (
  `ID` int(11) NOT NULL,
  `hostId` int(11) NOT NULL,
  `Priority` smallint(6) NOT NULL,
  `Description` text NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `addDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Inbox`
--

DROP TABLE IF EXISTS `Inbox`;
CREATE TABLE `Inbox` (
  `ID` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `Title` text NOT NULL,
  `Content` text NOT NULL,
  `isRead` tinyint(1) NOT NULL,
  `isImportant` tinyint(1) NOT NULL,
  `addDate` datetime NOT NULL,
  `readDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `JobsQueue`
--

DROP TABLE IF EXISTS `JobsQueue`;
CREATE TABLE `JobsQueue` (
  `ID` int(11) NOT NULL,
  `Job` varchar(16) NOT NULL,
  `itemId` mediumint(9) NOT NULL,
  `agentId` int(11) NOT NULL,
  `Args` text,
  `Cache` blob,
  `timeElapsed` decimal(10,3) DEFAULT NULL,
  `addDate` datetime NOT NULL,
  `scheduleDate` datetime DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Log`
--

DROP TABLE IF EXISTS `Log`;
CREATE TABLE `Log` (
  `addDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Context` varchar(16) NOT NULL,
  `Message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Networks`
--

DROP TABLE IF EXISTS `Networks`;
CREATE TABLE `Networks` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Services`
--

DROP TABLE IF EXISTS `Services`;
CREATE TABLE `Services` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `SessionMessages`
--

DROP TABLE IF EXISTS `SessionMessages`;
CREATE TABLE `SessionMessages` (
  `ID` int(11) NOT NULL,
  `sessionId` varchar(64) NOT NULL,
  `Type` varchar(16) NOT NULL,
  `Message` text NOT NULL,
  `addDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Sessions`
--

DROP TABLE IF EXISTS `Sessions`;
CREATE TABLE `Sessions` (
  `ID` varchar(64) NOT NULL,
  `IP` varchar(32) NOT NULL,
  `lastAction` datetime DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `nonce` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Snapshots`
--

DROP TABLE IF EXISTS `Snapshots`;
CREATE TABLE `Snapshots` (
  `ID` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `itemType` varchar(16) NOT NULL,
  `itemData` blob NOT NULL,
  `addDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Triggers`
--

DROP TABLE IF EXISTS `Triggers`;
CREATE TABLE `Triggers` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `UserGroups`
--

DROP TABLE IF EXISTS `UserGroups`;
CREATE TABLE `UserGroups` (
  `userId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `addDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `ID` int(11) NOT NULL,
  `Name` varchar(32) NOT NULL,
  `Password` varchar(64) NOT NULL,
  `eMail` varchar(64) DEFAULT NULL,
  `Alias` varchar(32) NOT NULL,
  `addDate` datetime NOT NULL,
  `lastLogin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Agents`
--
ALTER TABLE `Agents`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Config`
--
ALTER TABLE `Config`
  ADD PRIMARY KEY (`Name`);

--
-- Indici per le tabelle `EventsLog`
--
ALTER TABLE `EventsLog`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Groups`
--
ALTER TABLE `Groups`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Hosts`
--
ALTER TABLE `Hosts`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `Hosts` ADD FULLTEXT KEY `Hostname` (`Hostname`,`Note`,`Vendor`);

--
-- Indici per le tabelle `HostsLog`
--
ALTER TABLE `HostsLog`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Inbox`
--
ALTER TABLE `Inbox`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `JobsQueue`
--
ALTER TABLE `JobsQueue`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Networks`
--
ALTER TABLE `Networks`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Services`
--
ALTER TABLE `Services`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `Services` ADD FULLTEXT KEY `Banner` (`Banner`);

--
-- Indici per le tabelle `SessionMessages`
--
ALTER TABLE `SessionMessages`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Sessions`
--
ALTER TABLE `Sessions`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Snapshots`
--
ALTER TABLE `Snapshots`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Triggers`
--
ALTER TABLE `Triggers`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Agents`
--
ALTER TABLE `Agents`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT per la tabella `EventsLog`
--
ALTER TABLE `EventsLog`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1739221;
--
-- AUTO_INCREMENT per la tabella `Groups`
--
ALTER TABLE `Groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `Hosts`
--
ALTER TABLE `Hosts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT per la tabella `HostsLog`
--
ALTER TABLE `HostsLog`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT per la tabella `Inbox`
--
ALTER TABLE `Inbox`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=540;
--
-- AUTO_INCREMENT per la tabella `JobsQueue`
--
ALTER TABLE `JobsQueue`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159799;
--
-- AUTO_INCREMENT per la tabella `Networks`
--
ALTER TABLE `Networks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT per la tabella `Services`
--
ALTER TABLE `Services`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
--
-- AUTO_INCREMENT per la tabella `SessionMessages`
--
ALTER TABLE `SessionMessages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT per la tabella `Snapshots`
--
ALTER TABLE `Snapshots`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165900;
--
-- AUTO_INCREMENT per la tabella `Triggers`
--
ALTER TABLE `Triggers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `Users`
--
ALTER TABLE `Users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
