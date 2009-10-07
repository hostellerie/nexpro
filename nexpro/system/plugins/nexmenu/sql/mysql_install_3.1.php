<?php
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.1 for the nexPro Portal Server                          |
// | Nov 1, 2008                                                               |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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


$_SQL[] = "CREATE TABLE {$_TABLES['nexmenu']} (
  `id` mediumint(8) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `menutype` tinyint(3) NOT NULL default '0',
  `location` varchar(16) NOT NULL default 'block',
  `menuorder` mediumint(8) NOT NULL default '0',
  `label` varchar(64) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `grp_access` mediumint(8) NOT NULL default '2',
  `is_enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `menuorder` (`menuorder`)
) TYPE=MyISAM COMMENT='NexMenu Main table to setup menus' AUTO_INCREMENT=1 ;";

$_SQL[] = "CREATE TABLE {$_TABLES['nexmenu_language']} (
    `id` int(11) NOT NULL auto_increment,
    `menuitem` mediumint(8) NOT NULL default '0',
    `language` tinyint(1) NOT NULL default '0',
    `label` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`id`),
    KEY `menuitem` (`menuitem`),
    KEY `language` (`language`)
) TYPE=MyISAM ;";


$_SQL[] = "CREATE TABLE {$_TABLES['nexmenu_config']} (
  `theme` varchar(64) NOT NULL,
  `header_style` varchar(32) default NULL,
  `block_style` varchar(32) default NULL,
  `multilanguage` tinyint(1) default '0',
  `targetfeatures` varchar(255) default NULL,
  `blockmenu_style` varchar(64) default NULL,
  `blocksubmenu_style` varchar(64) default NULL,
  `headermenu_style` varchar(64) default NULL,
  `headersubmenu_style` varchar(64) default NULL,
  `headermenu_properties` varchar(255) default NULL,
  `blockmenu_properties` varchar(255) default NULL,
  `headerbg` varchar(8) default NULL,
  `headerfg` varchar(8) default NULL,
  `blockbg` varchar(8) default NULL,
  `blockfg` varchar(8) default NULL,
  `onhover_headerbg` varchar(8) default NULL,
  `onhover_headerfg` varchar(8) default NULL,
  `onhover_blockbg` varchar(8) default NULL,
  `onhover_blockfg` varchar(8) default NULL,
  `headersubmenufg` varchar(8) default NULL,
  `headersubmenubg` varchar(8) default NULL,
  `onhover_headersubmenufg` varchar(8) default NULL,
  `onhover_headersubmenubg` varchar(8) default NULL,
  `blocksubmenufg` varchar(8) default NULL,
  `blocksubmenubg` varchar(8) default NULL,
  `onhover_blocksubmenufg` varchar(8) default NULL,
  `onhover_blocksubmenubg` varchar(8) default NULL
) TYPE=MyISAM ;";


$_SQL[] = "INSERT INTO {$_TABLES['nexmenu_config']}  (`theme`, `header_style`, `block_style`, `multilanguage`, `targetfeatures`, `blockmenu_style`, `blocksubmenu_style`, `headermenu_style`, `headersubmenu_style`, `headermenu_properties`, `blockmenu_properties`, `headerbg`, `headerfg`, `blockbg`, `blockfg`, `onhover_headerbg`, `onhover_headerfg`, `onhover_blockbg`, `onhover_blockfg`, `headersubmenufg`, `headersubmenubg`, `onhover_headersubmenufg`, `onhover_headersubmenubg`, `blocksubmenufg`, `blocksubmenubg`, `onhover_blocksubmenufg`, `onhover_blocksubmenubg`) VALUES
('default', 'CSS', 'CSS', 1, 'targetfeatures=width=700,height=600,left=50,top=50,scrollbars=yes;', 'menuStyle1', 'menuStyle1', 'corpMenuStyle', 'corpSubmenuStyle', 'menuwidth=\"100%\";', '', '#10377C', '#FFFFFF', '#FFFFFF', '#335EA8', '#335EA8', '#FFFFFF', '#FFFFFF', '#296DC1', '#F4F3F7', '#237536', '#FFFFFF', '#237536', '#172BB0', '#BBC9EE', '#FCFCFC', '#145FF5'),('professional', 'Milonic', 'CSS', 1, 'targetfeatures=width=700,height=600,left=50,top=50,scrollbars=yes;', 'menuStyle1', 'menuStyle1', 'corpMenuStyle', 'corpSubmenuStyle', 'menuwidth=\"100%\";', '', '#10377C', '#FFFFFF', '#52565B', '#FFFFFF', '#335EA8', '#FFFFFF', '#145BEB', '#FFFFFF', '#F4F3F7', '#237536', '#FFFFFF', '#237536', '#172BB0', '#BBC9EE', '#FCFCFC', '#145FF5'),('nexpro', 'CSS', 'CSS', 1, 'targetfeatures=width=700,height=600,left=50,top=50,scrollbars=yes;', 'menuStyle1', 'menuStyle1', 'corpMenuStyle', 'corpSubmenuStyle', 'menuwidth=\"100%\";', '', '#10377C', '#FFFFFF', '#FFFFFF', '#335EA8', '#335EA8', '#FFFFFF', '#FFFFFF', '#296DC1', '#335EA8', '#FFFFFF', '#296DC1', '#FFFFFFF', '#FFFFFF', '#10377C', '#FFFFFF', '#335EA8');";


$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (12, 0, 1, 'block', 20, 'Home', '', '[siteurl]/index.php', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (2, 0, 3, 'block', 30, 'User Menu', '', '', 13, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (3, 0, 3, 'block', 40, 'Admin Menu', '', '', 13, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (4, 0, 4, 'block', 50, 'My Static Pages', '', 'spmenu', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (6, 0, 1, 'block', 70, 'PortalParts', '', 'http://www.portalparts.com', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (7, 0, 1, 'block', 80, 'geeklog.net', '', 'http://www.geeklog.net', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (8, 2, 4, 'block', 10, 'menuitems', '', 'usermenu', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (9, 3, 4, 'block', 10, 'menuitems', '', 'adminmenu', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (10, 0, 1, 'block', 90, 'Menu Admin', '', '[siteadminurl]/plugins/nexmenu/index.php', 1, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (11, 0, 1, 'block', 100, 'Logout', '', '[siteurl]/users.php?mode=logout', 13, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (13, 0, 1, 'header', 10, 'Contribute', '', '[siteurl]/submit.php', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (14, 0, 4, 'header', 20, 'plugin_menuitems', '', 'pluginmenu', 2, 1);";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (15, 0, 3, 'block', 40, 'Links', '', '', 2, 1); ";
$_SQL[]= "INSERT INTO {$_TABLES['nexmenu']} VALUES (16, 15, 4, 'block', 10, 'Link Items', '', 'linksmenu', 2, 1); ";


$_SQL[]= "INSERT INTO {$_TABLES['blocks']} (is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon) VALUES ('1','nexMenu','phpblock','Site Menu','all',0,1,'phpblock_nexmenu',2,2,3,3,2,2);";


?>