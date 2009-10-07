<?php
$_SQL=array();

$_SQL[]="CREATE TABLE  {$_TABLES['nxtime_timesheet_entry']}(
  `id` int(11) NOT NULL auto_increment,
  `uid` mediumint(9) NOT NULL default '0',
  `nextime_activity_id` int(11) default '0',
  `task_id` int(11) default '0',
  `project_id` int(11) default '0',
  `regular_time` decimal(10,2) default '0.00',
  `time_1_5` decimal(10,2) default '0.00',
  `time_2_0` decimal(10,2) default '0.00',
  `evening_hours` decimal(10,2) default '0.00',
  `standby` decimal(10,2) default '0.00',
  `adjustment` decimal(10,2) NOT NULL default '0.00',
  `vacation_time_used` decimal(10,2) default '0.00',
  `stat_time` decimal(10,2) default '0.00',
  `floater` decimal(10,2) default '0.00',
  `sick_time` decimal(10,2) default '0.00',
  `bereavement` decimal(10,2) default '0.00',
  `jury_duty` decimal(10,2) default '0.00',
  `unpaid_hrs` decimal(10,2) default '0.00',
  `other` decimal(10,2) default '0.00',
  `comment` text character set utf8,
  `datestamp` int(11) NOT NULL default '0',
  `locked` tinyint(1) default '0',
  `approved` tinyint(1) default '0',
  `rejected` tinyint(1) default '0',
  `rejected_comment` text character set utf8,
  `modified_by_uid` int(11) NOT NULL default '0',
  `rejected_by_uid` mediumint(9) NOT NULL default '0',
  `ack_modified` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";


$_SQL[]="CREATE TABLE IF NOT EXISTS {$_TABLES['nxtime_locked_timesheets']} (
  `uid` mediumint(9) NOT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  KEY `IX_startdate` (`startdate`),
  KEY `IX_enddate` (`enddate`),
  KEY `IX_uid` (`uid`)
) TYPE=MyISAM;";




$_SQL[]="CREATE TABLE {$_TABLES['nxtime_extra_user_data']} (
  `uid` mediumint(9) NOT NULL,
  `tech_number` varchar(10) default '0',
  `region` varchar(250) default NULL,
  `special_exclusion` tinyint(4) NOT NULL
) TYPE=MyISAM;";


?>