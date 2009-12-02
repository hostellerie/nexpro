<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexScan v1.0.0 - CSS Scan Plugin for nexPro                                 |
// | Date: March 1, 2008                                                         |
// +-----------------------------------------------------------------------------+
// | nexscan_mysql_install_1.0.0.sql - MySQL installation script                 |
// +-----------------------------------------------------------------------------+
// | Copyright (C) 2008 by the following authors:                                |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                    |
// +-----------------------------------------------------------------------------+
// |                                                                             |
// | This program is licensed under the terms of the GNU General Public License  |
// | as published by the Free Software Foundation; either version 2              |
// | of the License, or (at your option) any later version.                      |
// |                                                                             |
// | This program is part of the Nextide nexPro Suite and is licensed under      |
// | The GNU license and is OpenSource but released under closed distribution.   |
// | You are freely able to modify the source code to meet your needs but you    |
// | are not free to distribute the original or modified code without permission |
// | Refer to the license.txt file or contact nextide if you have any questions  |
// |                                                                             |
// | This program is distributed in the hope that it will be useful, but         |
// | WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY  |
// | or FITNESS FOR A PARTICULAR PURPOSE.                                        |
// | See the GNU General Public License for more details.                        |
// |                                                                             |
// | You should have received a copy of the GNU General Public License           |
// | along with this program; if not, write to the Free Software Foundation,     |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             |
// |                                                                             |
// +-----------------------------------------------------------------------------+
//

$_SQL[] = "CREATE TABLE {$_TABLES['nxscan_cssscan']} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `scan_id` int(11) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `css_file` varchar(255) NOT NULL,
  `classname` varchar(128) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `found_in_file` varchar(255) NOT NULL,
  `line_number` mediumint(4) unsigned NOT NULL default '0',
  `type` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$_SQL[] = "CREATE TABLE {$_TABLES['nxscan_options']} (
  `option_id` int(11) unsigned NOT NULL auto_increment,
  `scan_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `css_files_to_scan` TEXT NOT NULL,
  `directories_to_scan` TEXT NOT NULL,
  `file_filter` varchar(128) NOT NULL,
  `only_show_unused` tinyint(1) unsigned NOT NULL,
  `fuzzy_filter` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`option_id`)
) TYPE=MyISAM;";

?>