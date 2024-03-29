<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.1.1 for the nexPro Portal Server                     |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexproject.php                                                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
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
require_once $_CONF['path_system'] . 'classes/config.class.php';

$_PRJCONF=array();
$_PRJCONF['version'] = '2.1.1';
$_PRJCONF['pi_name'] = 'nexproject';
$_PRJCONF['pi_display_name'] = 'nexProject';
$_PRJCONF['pi_gl_version'] = '1.6.1';




// Database Definitions
$_TABLES['prj_category']             = $_DB_table_prefix . 'nxprj_category';
$_TABLES['prj_department']           = $_DB_table_prefix . 'nxprj_department';
$_TABLES['prj_location']             = $_DB_table_prefix . 'nxprj_location';
$_TABLES['prj_objective']            = $_DB_table_prefix . 'nxprj_objective';
$_TABLES['prj_permissions']          = $_DB_table_prefix . 'nxprj_permissions';
$_TABLES['prj_users']                = $_DB_table_prefix . 'nxprj_users';
$_TABLES['prj_projects']             = $_DB_table_prefix . 'nxprj_projects';
$_TABLES['prj_sorting']              = $_DB_table_prefix . 'nxprj_sorting';
$_TABLES['prj_task_users']           = $_DB_table_prefix . 'nxprj_taskusers';
$_TABLES['prj_tasks']                = $_DB_table_prefix . 'nxprj_tasks';
$_TABLES['prj_statuslog']            = $_DB_table_prefix . 'nxprj_statuslog';
$_TABLES['prj_session']              = $_DB_table_prefix . 'nxprj_sessions';
$_TABLES['prj_filters']              = $_DB_table_prefix . 'nxprj_filters';
$_TABLES['prj_lockcontrol']          = $_DB_table_prefix . 'nxprj_lockcontrol';
$_TABLES['prj_projPerms']            = $_DB_table_prefix . 'nxprj_projperms';
$_TABLES['prj_taskSemaphore']        = $_DB_table_prefix . 'nxprj_tasksemaphore';

$nexproject_config = config::get_instance();
$_PRJCONF_2 = $nexproject_config->get_config('nexproject');
if(is_array($_PRJCONF_2)) $_PRJCONF=@array_merge($_PRJCONF_2,$_PRJCONF);
//we have some globals and defines set.. Time to create those:
if(is_array($_PRJCONF_2)){
    $subTaskImg=$_PRJCONF['subTaskImg'];
    $subTaskOrderImg=$_PRJCONF['subTaskOrderImg'];
    define('THEME',$_PRJCONF['THEME']);
    define('ROWLIMIT',$_PRJCONF['ROWLIMIT']);
    define("TTF_DIR",$_PRJCONF['TTF_DIR']);
}
?>