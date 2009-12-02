<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
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

function upgrade_221() {
    global $_TABLES;

    COM_errorLog("Success - Completed nexFile plugin version 2.2.1 update",1);
    return 0;
}


function upgrade_300() {
    global $_CONF,$_TABLES,$_FMCONF;

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $plugin_path = $_CONF['path'] . 'plugins/nexfile/';

    $_FMCONF['storage_path'] = $_CONF['path_html'] . 'nexfile/data/';   // Existing data repository

    require_once $plugin_path . 'install_defaults.php';

    if (plugin_initconfig_nexfile()) {
        COM_errorLog('Nexfile 3.0 upgrade: Successfully initialized online config settings');
    } else {
        COM_errorLog('Nexfile 3.0 upgrade error: Error during online config initialization');
        return 1;
    }

    $_SQL = array();
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_categories']} CHANGE `catorder` `folderorder` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_filedetail']} DROP `platform`";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_filesubmissions']} DROP `thumbnail`";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_filesubmissions']} DROP `thumbtype`";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_filesubmissions']} ADD `mimetype` VARCHAR( 255 ) NOT NULL AFTER `size`, ADD `extension` VARCHAR( 8 ) NOT NULL AFTER `mimetype` ";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_filesubmissions']} ADD `tags` VARCHAR( 255 ) NOT NULL AFTER `description`";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_files']} DROP `thumbnail` ";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_files']} DROP `thumbtype` ";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_files']} ADD `mimetype` VARCHAR( 255 ) NOT NULL AFTER `title`, ADD `extension` VARCHAR( 8 ) NOT NULL AFTER `mimetype` ";
    $_SQL[] = "ALTER TABLE {$_TABLES['nxfile_files']} CHANGE `status_changedby_uid` `status_changedby_uid` MEDIUMINT( 8 ) NOT NULL DEFAULT '0'";
    $_SQL[] = "DROP TABLE {$_TABLES['nxfile_notifications']} ";
    $_SQL[] = "UPDATE {$_TABLES['groups']} SET `grp_name` = 'nexfile Admin' WHERE `grp_name` = 'nexFile Admin' LIMIT 1 ;";

    // Set the file status field on all files to 1 - online.
    $_SQL[] = "UPDATE {$_TABLES['nxfile_files']} SET `status` = 1 ;";

    $_SQL[] =  "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_export_queue']} (
      `id` int(11) NOT NULL auto_increment,
      `orig_filename` varchar(150) NOT NULL,
      `token` varchar(20) NOT NULL,
      `extension` varchar(10) NOT NULL,
      `timestamp` int(11) NOT NULL,
      `uid` mediumint(9) NOT NULL default '0',
      `fid` int(11) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";


    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_favorites']} (
      `uid` mediumint(8) NOT NULL,
      `fid` int(11) NOT NULL,
      KEY `topic_id` (`fid`),
      KEY `uid` (`uid`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_import_queue']} (
      `id` int(11) NOT NULL auto_increment,
      `orig_filename` varchar(150) NOT NULL,
      `queue_filename` varchar(255) NOT NULL,
      `timestamp` int(11) NOT NULL,
      `uid` mediumint(9) NOT NULL default '0',
      `mimetype` varchar(128) default NULL,
      `size` mediumint(8) NOT NULL default '0',
      `description` varchar(255) default NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";


    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_notificationlog']} (
      `target_uid` mediumint(8) NOT NULL,
      `submitter_uid` mediumint(8) NOT NULL,
      `notification_type` tinyint(1) NOT NULL,
      `fid` mediumint(8) NOT NULL default '0',
      `cid` mediumint(8) NOT NULL default '0',
      `datetime` int(11) NOT NULL,
      KEY `target_uid` (`target_uid`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_notifications']} (
      `id` mediumint(9) NOT NULL auto_increment,
      `fid` mediumint(9) NOT NULL default '0',
      `ignore_filechanges` tinyint(1) NOT NULL default '0',
      `cid` mediumint(9) NOT NULL default '0',
      `cid_newfiles` tinyint(1) NOT NULL default '0',
      `cid_changes` tinyint(1) NOT NULL default '0',
      `uid` mediumint(9) NOT NULL default '0',
      `date` int(11) NOT NULL default '0',
      PRIMARY KEY  (`id`),
      KEY `fid` (`fid`),
      KEY `cid` (`cid`),
      KEY `uid` (`uid`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; ";


    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_recentfolders']} (
      `id` int(11) NOT NULL auto_increment,
      `uid` mediumint(8) NOT NULL,
      `cid` mediumint(8) NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `uid` (`uid`),
      KEY `cid` (`cid`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;";

    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['nxfile_usersettings']} (
      `uid` mediumint(8) NOT NULL,
      `notify_newfile` tinyint(1) NOT NULL default '1',
      `notify_changedfile` tinyint(1) NOT NULL default '1',
      `allow_broadcasts` tinyint(1) NOT NULL default '1',
      `allowable_view_folders` text NOT NULL,
      KEY `uid` (`uid`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";


    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("Nexfile 3.0 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during nexfile 3.0 plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    COM_errorLog("Success - Completed nexfile 3.0 update",1);
    return 0;


}


?>