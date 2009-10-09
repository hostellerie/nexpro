<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.2.0 for the nexPro Portal Server                        |
// | Oct 6, 2009                                                               |
// +---------------------------------------------------------------------------+
// | autouninstall.php                                                         |
// |                                                                           |
// | This file provides helper functions for the automatic plugin install.     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - randy@nextide.ca                                 |
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

global $_TABLES,$_DB_table_prefix;
require_once ($_CONF['path'] . 'plugins/nexlist/nexlist.php');

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_nexlist () {
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('nexlist','nexlistfields','nexlistitems'),
        /* give the full name of the group, as in the db */
        'groups' => array('nexlist Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('nexlist.edit'),
        /* give the full name of the block, including 'phpblock_', etc */
        //'php_blocks' => array(''),
        /* give all vars with their name */
        'vars'=> array()
    );
    return $out;
}
?>