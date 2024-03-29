<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.1.0 for the nexPro Portal Server                         |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
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


$_SQL[] = "CREATE TABLE {$_TABLES['tagwords']} (
  `id` int(11) NOT NULL auto_increment,
  `tagword` varchar(32) NOT NULL,
  `displayword` varchar(32) default NULL,
  `metric` smallint(5) NOT NULL default '1',
  `last_updated` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `tagword` (`tagword`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['tagword_items']} (
  `itemid` varchar(128) NOT NULL,
  `type` varchar(32) NOT NULL,
  `tags` text,
  KEY `itemid` (`itemid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$_SQL[] = "CREATE TABLE {$_TABLES['tagword_metrics']} (
  `tagid` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `uid` mediumint(8) default NULL,
  `grpid` mediumint(8) default NULL,
  `metric` smallint(5) NOT NULL,
  `last_updated` datetime NOT NULL,
  KEY `tagid` (`tagid`),
  KEY `type` (`type`),
  KEY `uid` (`grpid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; ";

?>