<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.1 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
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

function nexflow_upgrade_300() {
    global $_TABLES;

    $_SQL = array();

    //use this script to upgrade nexflow to 3.0.0
    //require_once ('../../../lib-common.php');

    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD fieldid MEDIUMINT(8) DEFAULT 0 NOT NULL AFTER formid";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD varValue VARCHAR(255) NULL AFTER fieldid";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD incValue MEDIUMINT(8) DEFAULT 0 NOT NULL AFTER varValue";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD varToSet INT(11) DEFAULT 0 NOT NULL AFTER incValue";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD offsetLeft INT(4) UNSIGNED DEFAULT 0 NOT NULL";
    $_SQL[] = "ALTER TABLE {$_TABLES['nftemplatedata']} ADD offsetTop INT(4) UNSIGNED DEFAULT 0 NOT NULL";
    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("nexFlow Plugin 3.0.0 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during nexFlow plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    DB_query("UPDATE {$_TABLES['nftemplatedata']} SET firstTask=0;");
    $res = DB_query("SELECT id FROM {$_TABLES['nftemplate']};");
    while (list ($id) = DB_fetchArray($res)) {
        //still need to set the logical id
        DB_query("INSERT INTO {$_TABLES['nftemplatedata']} (nf_templateID, nf_stepType, taskname, firstTask) VALUES ($id, 9, 'Start', 1);");
        DB_query("INSERT INTO {$_TABLES['nftemplatedata']} (nf_templateID, nf_stepType, taskname) VALUES ($id, 10, 'End');");
    }
    DB_query("INSERT INTO {$_TABLES['nfsteptype']} (id, stepType, is_interactiveStepType) VALUES (9, 'Start', 0);");
    DB_query("INSERT INTO {$_TABLES['nfsteptype']} (id, stepType, is_interactiveStepType) VALUES (10, 'End', 0);");
    DB_query("INSERT INTO {$_TABLES['nfsteptype']} (id, stepType, is_interactiveStepType) VALUES (11, 'Set Process Variable', 0);");

    COM_errorLog("Success - Completed nexFlow plugin version 3.0.0 update",1);
    return 0;
}

function nexflow_upgrade_310() {
    global $_CONF,$_TABLES,$_DB_table_prefix;

    $_SQL = array();
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


    $_SQL[] = "ALTER TABLE {$_TABLES['nf_templatedata']} ADD reminderIntervalVariable INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER reminderInterval";
    $_SQL[] = "ALTER TABLE {$_TABLES['nf_templatedata']} ADD prenotify_subject VARCHAR(127) DEFAULT NULL AFTER last_updated";
    $_SQL[] = "ALTER TABLE {$_TABLES['nf_templatedata']} ADD postnotify_subject VARCHAR(127) DEFAULT NULL AFTER prenotify_subject";
    $_SQL[] = "ALTER TABLE {$_TABLES['nf_templatedata']} ADD reminder_subject VARCHAR(127) DEFAULT NULL AFTER postnotify_subject";
    $_SQL[] = "ALTER TABLE {$_TABLES['nf_templatedata']} ADD surpressFirstNotification TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'";


    // Rename tables that have been changed to be more consistent.
	$_XTABLES = array();
	$_XTABLES['nfprojects']              = $_DB_table_prefix . 'nfprojects';
	$_XTABLES['nfproject_forms']         = $_DB_table_prefix . 'nfproject_forms';
	$_XTABLES['nfproject_timestamps']    = $_DB_table_prefix . 'nfproject_timestamps';
	$_XTABLES['nfproject_comments']      = $_DB_table_prefix . 'nfproject_comments';
	$_XTABLES['nfproject_taskhistory']   = $_DB_table_prefix . 'nfproject_taskhistory';
	$_XTABLES['nfproject_approvals']     = $_DB_table_prefix . 'nfproject_approvals';

    $_SQL[] = "ALTER TABLE {$_XTABLES['nfprojects']} RENAME TO {$_TABLES['nf_projects']};";
    $_SQL[] = "ALTER TABLE {$_XTABLES['nfproject_forms']} RENAME TO {$_TABLES['nf_projectforms']};";
    $_SQL[] = "ALTER TABLE {$_XTABLES['nfproject_timestamps']} RENAME TO {$_TABLES['nf_projecttimestamps']};";
    $_SQL[] = "ALTER TABLE {$_XTABLES['nfproject_comments']} RENAME TO {$_TABLES['nf_projectcomments']};";
    $_SQL[] = "ALTER TABLE {$_XTABLES['nfproject_taskhistory']} RENAME TO {$_TABLES['nf_projecttaskhistory']};";
    $_SQL[] = "ALTER TABLE {$_XTABLES['nfproject_approvals']} RENAME TO {$_TABLES['nf_projectapprovals']};";

    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLog("nexFlow Plugin 3.1.0 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during nexFlow plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    COM_errorLog("Success - Completed updating nexFlow tables for version 3.1.0 update",1);
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $plugin_path = $_CONF['path'] . 'plugins/nexflow/';
    require_once $plugin_path . 'install_defaults.php';
    if (file_exists($plugin_path . 'config.php')) {
        global $_DB_table_prefix, $CONF_NF;
        require_once $plugin_path . 'config.php';
    }
    if (plugin_initconfig_nexflow()) {
        COM_errorLog("Success - Completed updating adding online config data for nexFlow version 3.1.0 update",1);
        return 0;
    } else {
        COM_errorLog('nexflow_upgrade_310: There was an error upgrading the nexflow plugin');
        return 1;
    }

}


?>