<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | mysql_install-without_testsuite.php                                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
// | Eric de la Chevrotiere - Eric DOT delaChevrotiere AT nextide DOT ca       |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//

$_SQL[] = "CREATE TABLE {$_TABLES['nf_appgroups']} (
  `id` int(11) NOT NULL auto_increment,
  `AppGroup` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nf_appgroups']} VALUES (1, 'Sample AppGroup 1');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_appgroups']} VALUES (2, 'Testsuite');";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_handlers']} (
  `id` int(11) NOT NULL auto_increment,
  `handler` varchar(255) NOT NULL default '',
  `nf_handlerTypeID` int(11) NOT NULL default '0',
  `description` longtext,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nf_handlers']} (`id`, `handler`, `nf_handlerTypeID`) VALUES (1, 'nexflow/batchhandler1.php', 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_handlers']} (`id`, `handler`, `nf_handlerTypeID`) VALUES (2, 'testsuite/yes-no.php', 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_ifoperators']} (
  `id` int(11) NOT NULL default '0',
  `operator` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nf_ifoperators']} (`id`, `operator`) VALUES (1, '=');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_ifoperators']} (`id`, `operator`) VALUES (2, '>');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_ifoperators']} (`id`, `operator`) VALUES (3, '<');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_ifoperators']} (`id`, `operator`) VALUES (4, '!=');";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_ifprocessarguments']} (
  `id` int(11) NOT NULL default '0',
  `label` varchar(200) NOT NULL default '',
  `logicalEntry` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nf_ifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (1, 'Last Task Status is Success', 'lasttasksuccess');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_ifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (2, 'Last Task Status is Cancel', 'lasttaskcancel');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_ifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (3, 'Last Task Status is Hold', 'lasttaskhold');";
$_SQL[] = "INSERT INTO {$_TABLES['nf_ifprocessarguments']} (`id`, `label`, `logicalEntry`) VALUES (4, 'Last Task Status is Aborted', 'lasttaskaborted');";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_notifications']} (
  `queueID` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `notification_sent` tinyint(1) NOT NULL default '0',
  KEY `queueID` (`queueID`),
  KEY `uid` (`uid`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_process']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `customFlowName` varchar(255) NOT NULL default '',
  `complete` int(11) default NULL,
  `initiator_uid` int(11) default NULL,
  `pid` int(11) default NULL,
  `initiatedDate` date default NULL,
  `completedDate` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_processvariables']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_processID` int(11) NOT NULL default '0',
  `variableValue` varchar(255) NOT NULL default '',
  `nf_templateVariableID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_processID` (`nf_processID`),
  KEY `nf_templateVariableID` (`nf_templateVariableID`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_productionassignments']} (
  `id` int(11) NOT NULL auto_increment,
  `task_id` int(8) NOT NULL default '0',
  `uid` int(8) NOT NULL default '0',
  `nf_processVariable` mediumint(8) NOT NULL default '0',
  `assignBack_uid` mediumint(8) NOT NULL default '0',
  `last_updated` int(11) NOT NULL default '0',
  `security_hash` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `task_id` (`task_id`),
  KEY `nf_processVariable` (`nf_processVariable`),
  KEY `assignBack_uid` (`assignBack_uid`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_queue']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_processID` int(11) NOT NULL default '0',
  `nf_templateDataID` int(11) default '0',
  `status` int(11) default NULL,
  `uid` int(11) default NULL,
  `archived` int(11) default NULL,
  `prePopulate` int(11) default NULL,
  `createdDate` datetime default NULL,
  `completedDate` datetime default NULL,
  `nextReminderTime` datetime default NULL,
  `startedDate` datetime default NULL,
  `numRemindersSent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_processID` (`nf_processID`),
  KEY `nf_templateDataID` (`nf_templateDataID`),
  KEY `status` (`status`),
  KEY `archived` (`archived`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_queuefrom']} (
  `id` int(11) NOT NULL auto_increment,
  `queueID` int(11) default NULL,
  `fromQueueID` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_steptype']} (
  `id` int(11) NOT NULL default '0',
  `stepType` varchar(50) NOT NULL default '',
  `flexField` varchar(100) default NULL,
  `is_interactiveStepType` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (1, 'Manual Web', NULL, 1);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (2, 'And', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (4, 'Batch', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (5, 'If', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (6, 'batch function', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (7, 'interactive function', NULL, 1);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (8, 'nexform', NULL, 1);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (9, 'Start', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (10, 'End', NULL, 0);";
$_SQL[] = "INSERT INTO {$_TABLES['nf_steptype']} (`id`, `stepType`, `flexField`, `is_interactiveStepType`) VALUES (11, 'Set Process Variable', NULL, 0);";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_template']} (
  `id` int(11) NOT NULL auto_increment,
  `templateName` varchar(100) NOT NULL default '',
  `useProject` int(11) NOT NULL default '0',
  `AppGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_templateassignment']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateDataID` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `nf_processVariable` int(11) NOT NULL default '0',
  `nf_prenotifyVariable` int(11) NOT NULL default '0',
  `nf_postnotifyVariable` int(11) NOT NULL default '0',
  `nf_remindernotifyVariable` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_templateDataID` (`nf_templateDataID`),
  KEY `nf_processVariable` (`nf_processVariable`),
  KEY `nf_prenotifyVariable` (`nf_prenotifyVariable`),
  KEY `nf_postnotifyVariable` (`nf_postnotifyVariable`),
  KEY `nf_remindernotifyVariable` (`nf_remindernotifyVariable`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_templatedata']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `logicalID` int(11) default NULL,
  `nf_stepType` tinyint(1) NOT NULL default '0',
  `nf_handlerId` int(11) NOT NULL default '0',
  `firstTask` tinyint(1) NOT NULL default '0',
  `taskname` varchar(150) default NULL,
  `assignedByVariable` tinyint(1) NOT NULL default '0',
  `argumentVariable` varchar(255) default NULL,
  `argumentProcess` varchar(255) default NULL,
  `operator` varchar(10) default NULL,
  `ifValue` varchar(255) default NULL,
  `regenerate` tinyint(1) NOT NULL default '0',
  `regenAllLiveTasks` tinyint(1) default '0',
  `isDynamicForm` tinyint(1) NOT NULL default '0',
  `dynamicFormVariableID` int(11) NOT NULL default '0',
  `isDynamicTaskName` tinyint(1) NOT NULL default '0',
  `dynamicTaskNameVariableID` int(11) NOT NULL default '0',
  `function` varchar(255) NOT NULL default '',
  `formid` mediumint(8) NOT NULL default '0',
  `fieldid` mediumint(8) NOT NULL default '0',
  `varValue` varchar(255) default NULL,
  `incValue` mediumint(8) NOT NULL default '0',
  `varToSet` int(11) NOT NULL default '0',
  `optionalParm` varchar(64) NOT NULL default '',
  `reminderInterval` tinyint(1) NOT NULL default '0',
  `reminderIntervalVariable` int(11) unsigned NOT NULL default '0',
  `subsequentReminderInterval` tinyint(1) NOT NULL default '0',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `prenotify_subject` varchar(127) default NULL,
  `postnotify_subject` varchar(127) default NULL,
  `reminder_subject` varchar(127) default NULL,
  `prenotify_message` varchar(255) NOT NULL default '',
  `postnotify_message` varchar(255) NOT NULL default '',
  `reminder_message` varchar(255) NOT NULL default '',
  `numReminders` tinyint(1) NOT NULL default '0',
  `escalateVariableID` int(11) NOT NULL default '0',
  `offsetLeft` int(4) unsigned NOT NULL default '0',
  `offsetTop` int(4) unsigned NOT NULL default '0',
  `surpressFirstNotification` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_templatedatanextstep']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateDataFrom` int(11) NOT NULL default '0',
  `nf_templateDataTo` int(11) default '0',
  `nf_templateDataToFalse` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_templatevariables']} (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `nf_variableTypeID` int(11) NOT NULL default '0',
  `variableName` varchar(100) NOT NULL default '',
  `variableValue` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`),
  KEY `nf_variableTypeID` (`nf_variableTypeID`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_useraway']} (
  `uid` mediumint(8) NOT NULL default '0',
  `away_start` int(11) NOT NULL default '0',
  `away_return` int(11) NOT NULL default '0',
  `reassign_uid` mediumint(8) NOT NULL default '0',
  `reason` varchar(255) NOT NULL default '',
  `is_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projectapprovals']} (
  `id` int(11) NOT NULL auto_increment,
  `process_id` mediumint(8) NOT NULL default '0',
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `date_updated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `form_id` (`form_id`),
  KEY `uid` (`uid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projectcomments']} (
  `id` int(11) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `task_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `comment` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projectforms']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `trial_id` mediumint(8) NOT NULL default '0',
  `form_id` mediumint(8) NOT NULL default '0',
  `formtype` varchar(32) NOT NULL default '',
  `results_id` mediumint(8) NOT NULL default '0',
  `created_by_taskid` mediumint(8) NOT NULL default '0',
  `created_by_uid` mediumint(8) NOT NULL default '0',
  `is_locked_by_uid` mediumint(8) NOT NULL default '0',
  `is_trial_project` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `form_id` (`form_id`),
  KEY `results_id` (`results_id`),
  KEY `created_by_taskid` (`created_by_taskid`),
  KEY `trial_id` (`trial_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projecttaskhistory']} (
  `id` int(11) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `process_id` mediumint(8) NOT NULL default '0',
  `task_id` mediumint(8) NOT NULL default '0',
  `assigned_uid` mediumint(8) NOT NULL default '0',
  `date_assigned` int(11) NOT NULL default '0',
  `date_started` int(11) NOT NULL default '0',
  `date_completed` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `assigned_uid` (`assigned_uid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projecttimestamps']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `project_formid` mediumint(8) NOT NULL default '0',
  `process_id` mediumint(8) NOT NULL default '0',
  `statusmsg` varchar(255) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_formid` (`project_formid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projectattachments']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `task_id` mediumint(8) NOT NULL,
  `project_id` mediumint(8) NOT NULL,
  `fieldname` varchar(64) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `task_id` (`task_id`),
  KEY `project_id` (`project_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projectdatafields']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `fieldname` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projectdataresults']} (
  `id` int(11) NOT NULL auto_increment,
  `field_id` mediumint(8) NOT NULL,
  `project_id` mediumint(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `textdata` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `field_id` (`field_id`,`project_id`,`task_id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nf_projects']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `wf_process_id` mediumint(8) NOT NULL default '0',
  `wf_task_id` mediumint(8) NOT NULL default '0',
  `project_num` varchar(12) NOT NULL default '',
  `originator_uid` mediumint(8) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `prev_status` tinyint(1) NOT NULL default '0',
  `related_processes` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `resultsid` (`project_num`),
  KEY `wf_templateid` (`wf_process_id`),
  KEY `originator_uid` (`originator_uid`),
  KEY `status` (`status`)
) TYPE=MyISAM;";

?>