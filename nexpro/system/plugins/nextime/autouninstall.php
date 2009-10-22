<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                          |
// | Date: Sept. 23, 2009                                                        |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | uninstall_defaults - nexTime plugin uninstall file                          |
// +-----------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                                |
// | Randy Kolenko          - randy.kolenko@nextide.ca                           |
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
global $_TABLES,$_DB_table_prefix,$CONF_NEXTIME;


if (strpos(strtolower($_SERVER['PHP_SELF']), 'autouninstall.php') !== false) {
    die('This file can not be used on its own!');
}

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
function plugin_autouninstall_nextime ()
{

    global $CONF_NEXTIME, $_TABLES;
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('nextime_timesheet_entry','nextime_extra_user_data','nextime_locked_timesheets','nextime_vars'),
        /* give the full name of the group, as in the db */
        'groups' => array('NexTime Admin','NexTime Finance','NexTime Supervisors','NexTime USER'),
        /* give the full name of the feature, as in the db */
        'features' => array('nextime.admin', 'nextime.user'),
        /* give the full name of the block, including 'phpblock_', etc */
        //'php_blocks' => array(''),
        /* give all vars with their name */
        'vars'=> array()
    );

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_timesheet_tasks']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_timesheet_tasks']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_timesheet_tasks']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_nextime_activities']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_nextime_activities']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_nextime_activities']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_nextime_projects']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_nextime_projects']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_nextime_projects']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_employee_to_supervisor']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_employee_to_supervisor']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_employee_to_supervisor']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_user_locations']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_user_locations']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_user_locations']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_manager_to_supervisor']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_manager_to_supervisor']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_manager_to_supervisor']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$CONF_NEXTIME['nexlist_employee_to_delegate']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$CONF_NEXTIME['nexlist_employee_to_delegate']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$CONF_NEXTIME['nexlist_employee_to_delegate']}");

    return $out;
}

?>