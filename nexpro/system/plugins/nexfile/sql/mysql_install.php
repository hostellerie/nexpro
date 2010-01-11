<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.1 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
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


$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_access']} (
  `accid` mediumint(9) NOT NULL auto_increment,
  `catid` mediumint(9) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `grp_id` mediumint(9) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `upload` tinyint(1) NOT NULL default '0',
  `upload_direct` tinyint(1) NOT NULL default '0',
  `upload_ver` tinyint(1) NOT NULL default '0',
  `approval` tinyint(1) NOT NULL default '0',
  `admin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`accid`),
  KEY `catid` (`catid`)
) TYPE=MyISAM COMMENT='nexfile Access Rights - for user or group access to category' AUTO_INCREMENT=1;";



$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_categories']} (
  `cid` mediumint(9) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `folderorder` smallint(5) NOT NULL default '0',
  `image` varchar(255) default NULL,
  `auto_create_notifications` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `pid` (`pid`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1 ;";



$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_filedetail']} (
  `fid` mediumint(8) NOT NULL default '0',
  `description` longtext,
  `hits` mediumint(9) NOT NULL default '0',
  `rating` tinyint(4) NOT NULL default '0',
  `votes` tinyint(4) unsigned NOT NULL default '0',
  `comments` tinyint(4) unsigned NOT NULL default '0',
  KEY `fid` (`fid`)
) TYPE=MyISAM;";



$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_files']} (
  `fid` mediumint(8) NOT NULL auto_increment,
  `cid` mediumint(8) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `title` varchar(128) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '1',
  `ftype` varchar(16) NOT NULL default '',
  `size` mediumint(9) NOT NULL default '0',
  `mimetype` varchar(255) NOT NULL default '',
  `extension` varchar(8) NOT NULL default '',
  `submitter` mediumint(8) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `date` int(8) NOT NULL default '0',
  `version_ctl` tinyint(1) NOT NULL default '0',
  `status_changedby_uid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`fid`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=1;";



$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_fileversions']} (
  `id` mediumint(9) NOT NULL auto_increment,
  `fid` mediumint(9) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `ftype` varchar(16) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '0',
  `size` mediumint(9) NOT NULL default '0',
  `notes` longtext NOT NULL,
  `date` int(11) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;";




$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_notifications']} (
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
) TYPE=MyISAM AUTO_INCREMENT=1 ;";


$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_filesubmissions']} (
 `id` mediumint(8) NOT NULL auto_increment,
  `fid` mediumint(8) NOT NULL default '0',
  `cid` mediumint(8) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `tempname` varchar(255) NOT NULL default '',
  `title` varchar(128) NOT NULL default '',
  `ftype` varchar(16) NOT NULL default '',
  `description` longtext NOT NULL,
  `tags` varchar(255) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '1',
  `version_note` longtext NOT NULL,
  `size` mediumint(9) NOT NULL default '0',
  `mimetype` varchar(255) NOT NULL default '',
  `extension` varchar(8) NOT NULL default '',
  `submitter` mediumint(8) NOT NULL default '0',
  `date` int(8) NOT NULL default '0',
  `version_ctl` tinyint(1) NOT NULL default '0',
  `notify` tinyint(1) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=1;";


$_SQL[] = "CREATE TABLE {$_TABLES['auditlog']} (
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `script` varchar(255) NOT NULL default '',
  `logentry` varchar(255) NOT NULL default '',
  KEY `uid` (`uid`),
  KEY `date` (`date`),
  KEY `script` (`script`)
) TYPE=MyISAM;";



$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_favorites']} (
  `uid` mediumint(8) NOT NULL,
  `fid` int(11) NOT NULL,
  KEY `topic_id` (`fid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";


$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_recentfolders']} (
  `id` int(11) NOT NULL auto_increment,
  `uid` mediumint(8) NOT NULL,
  `cid` mediumint(8) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";


$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_downloads']} (
  `uid` mediumint(8) NOT NULL default '0',
  `fid` int(11) NOT NULL default '0',
  `remote_ip` varchar(15) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `uid` (`uid`),
  KEY `date` (`date`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";


$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_import_queue']} (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `orig_filename` VARCHAR( 150 ) NOT NULL ,
    `queue_filename` varchar(255) NOT NULL,
    `timestamp` INT NOT NULL,
    `uid` mediumint(9) NOT NULL DEFAULT '0',
    `mimetype` varchar(128) default NULL,
    `size` mediumint(8) NOT NULL default '0',
    `description` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_export_queue']} (
    `id` INT(11) NOT NULL auto_increment ,
    `orig_filename` VARCHAR( 150 ) NOT NULL ,
    `token` VARCHAR( 20 ) NOT NULL ,
    `extension` VARCHAR( 10 ) NOT NULL ,
    `timestamp` INT NOT NULL ,
    `uid` MEDIUMINT NOT NULL DEFAULT '0',
    `fid` int(11) NOT NULL,
    PRIMARY KEY ( `id` )
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_usersettings']} (
  `uid` mediumint(8) NOT NULL,
  `notify_newfile` tinyint(1) NOT NULL default '1',
  `notify_changedfile` tinyint(1) NOT NULL default '1',
  `allow_broadcasts` tinyint(1) NOT NULL default '1',
  `allowable_view_folders` text NOT NULL,
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$_SQL[] = "CREATE TABLE {$_TABLES['nxfile_notificationlog']} (
  `target_uid` mediumint(8) NOT NULL,
  `submitter_uid` mediumint(8) NOT NULL,
  `notification_type` tinyint(1) NOT NULL,
  `fid` mediumint(8) NOT NULL default '0',
  `cid` mediumint(8) NOT NULL default '0',
  `datetime` int(11) NOT NULL,
  KEY `target_uid` (`target_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

?>