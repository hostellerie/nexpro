<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.2 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | autouninstall.php                                                         |
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
function plugin_autouninstall_nexfile ()
{

    global $_TABLES,$_CONF;
    require_once( $_CONF['path_system'] . 'nexpro/classes/tagcloud.class.php' );

    $query = DB_query("SELECT itemid FROM {$_TABLES['tagworditems']} WHERE type = 'nexfile'");
    if (DB_numRows($query) > 0) {
        $tagcloud = new nexfileTagCloud();
        while ($A = DB_fetchArray($query)) {
            $tagcloud->clear_tags($A['itemid']);
        }
    }

    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(
            'nxfile_access',
            'nxfile_categories',
            'nxfile_files',
            'nxfile_filedetail',
            'nxfile_fileversions',
            'nxfile_notifications',
            'nxfile_filesubmissions',
            'auditlog',
            'nxfile_favorites',
            'nxfile_recentfolders',
            'nxfile_downloads',
            'nxfile_usersettings',
            'nxfile_notificationlog',
            'nxfile_import_queue',
            'nxfile_export_queue'),

        /* give the full name of the group, as in the db */
        'groups' => array('nexfile Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('nexfile.admin','nexfile.edit', 'nexfile.user'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array('phpblock_nexfile_latestfiles'),
        /* give all vars with their name */
        'vars'=> array('nexfile_admin')
    );

    return $out;
}

?>