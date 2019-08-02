INSERT INTO `Config` (`Name`, `Value`) VALUES
('cron_lastrun', ''),
('events_keep', '1440'),
('event_lastid', ''),
('hostname', 'localhost'),
('macvendors_token', NULL),
('mail_from_mail', 'nidan@localhost'),
('mail_from_name', 'Nidan'),
('mail_server_host', 'localhost'),
('mail_server_port', '25'),
('mail_template', '<style>\r\np {\r\n    text-align: justify;\r\n}\r\n\r\ntable { border-collapse: collapse; }\r\nth { border-bottom: 1px solid #CCC; border-top: 1px solid #CCC; background-color: #EEE; padding: 0.5em 0.8em; text-align: center; font-weight:bold; }\r\ntd { border-bottom: 1px solid #CCC;padding: 0.2em 0.8em; }\r\ntd+td { border-left: 1px solid #CCC;text-align: center; }\r\n</style>\r\n<div style=\'padding: 5px;\'>\r\n%body%\r\n</div>\r\n<div style=\'width:100%; border-top: 1px solid #ccc; background-color: #eee; padding: 5px; text-align: center;\'>\r\n<b>Nidan</b> @ %host%</div>'),
('version', '0.0.3');

INSERT INTO `Groups` (`ID`, `Name`, `ACL`) VALUES
(1, 'Default', 'a:9:{s:8:\"canLogin\";b:1;s:11:\"manageUsers\";b:0;s:12:\"manageSystem\";b:0;s:14:\"manageNetworks\";b:0;s:12:\"manageAgents\";b:0;s:14:\"manageTriggers\";b:1;s:12:\"manageGroups\";b:0;s:8:\"editHost\";b:0;s:13:\"addHostEvents\";b:1;}'),
(2, 'Administrators', 'a:9:{s:8:\"canLogin\";b:1;s:11:\"manageUsers\";b:1;s:12:\"manageSystem\";b:1;s:14:\"manageNetworks\";b:1;s:12:\"manageAgents\";b:1;s:14:\"manageTriggers\";b:1;s:12:\"manageGroups\";b:1;s:8:\"editHost\";b:1;s:13:\"addHostEvents\";b:1;}'),
(3, 'Power Users', 'a:11:{s:8:\"canLogin\";b:1;s:11:\"manageUsers\";b:1;s:12:\"manageSystem\";b:0;s:14:\"manageNetworks\";b:1;s:12:\"manageAgents\";b:1;s:14:\"manageTriggers\";b:1;s:12:\"manageGroups\";b:1;s:8:\"editHost\";b:1;s:13:\"addHostEvents\";b:1;i:1;b:0;i:0;b:0;}');

INSERT INTO `Users` (`ID`, `Name`, `Password`, `eMail`, `Alias`, `addDate`, `lastLogin`) VALUES
(1, 'admin@localhost', PASSWORD('admin'), '', '', NOW(), '');


INSERT INTO `UserGroups` (`userId`, `groupId`, `addDate`) VALUES
(1, 2, NOW());
