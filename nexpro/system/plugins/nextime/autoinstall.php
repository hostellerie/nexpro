<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Oct. 5, 2009                                                        |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
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

include_once ($_CONF['path'] . 'plugins/nextime/autouninstall.php');
require_once ($_CONF['path'] . 'plugins/nextime/nextime.php');

if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* Autoinstall API functions for the nexList plugin
*
* @package nexTime
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nextime($pi_name)
{

    global $_CONF,$CONF_NEXTIME;
    @require ($_CONF['path'] . 'plugins/nextime/nextime.php');
    $pi_name         = $CONF_NEXTIME['pi_name'];
    $pi_display_name = $CONF_NEXTIME['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';
    $pi_user         = $pi_display_name . ' USER';
    $pi_supervisor   = $pi_display_name . ' Supervisors';
    $pi_finance      = $pi_display_name . ' Finance';

    $info = array(
        'pi_name'         => $CONF_NEXTIME['pi_name'],
        'pi_display_name' => $CONF_NEXTIME['pi_display_name'],
        'pi_version'      => $CONF_NEXTIME['version'],
        'pi_gl_version'   => $CONF_NEXTIME['gl_version'],
        'pi_homepage'     => 'http://www.nextide.ca/'
    );

    $groups = array(
        $pi_admin       => 'Has full access to ' . $pi_display_name . ' features',
        $pi_user        => 'Has User access to ' . $pi_name . ' with NO admin features',
        $pi_supervisor  => 'Users who are deemed Supervisors within the  ' . $pi_name . ' plugin.',
        $pi_finance     => 'Users who are deemed as a Finance user within the  ' . $pi_name . ' plugin.'
    );

    $features = array(
        $pi_name . '.admin'    => 'Plugin Admin',
        $pi_name . '.user'     => 'Plugin User'
    );

    $mappings = array(
        $pi_name . '.admin'  => array($pi_admin),
        $pi_name . '.user'   => array($pi_admin),
        $pi_name . '.user'   => array($pi_supervisor),
        $pi_name . '.user'   => array($pi_user),
        $pi_name . '.user'   => array($pi_finance)
    );

    $tables = array(
        'nxtime_timesheet_entry',
        'nxtime_locked_timesheets',
        'nxtime_extra_user_data'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    return $inst_parms;
}

/**
* Load plugin configuration from database
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true on success, otherwise false
* @see      plugin_initconfig_nextime
*
*/
function plugin_load_configuration_nextime($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nextime();
}

/**
* Plugin postinstall
*
* We're inserting our default data here since it depends on other stuff that
* has to happen first ...
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_postinstall_nextime($pi_name)
{
    global $_DB_dbms, $_CONF, $_DB_table_prefix, $_TABLES ;
    require_once ($_CONF['path'] . 'plugins/nextime/nextime.php');
    //  install the nexlist lists
    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Timesheet Activities/Clients',    'Maintains a list of Activities/Clients that projects and tasks are attached to', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $activityList= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Timesheet Projects',    'Maintains a list of Projects that are attached to the activities/clients list', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $projectList= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Timesheet Tasks',    'Maintains a list of Tasks that are attached to the activities/clients list', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $tasksList= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Employee to Supervisor List',    'Links employees to supervisors', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $empSupList= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Employee Locations',    'Locations for your employees', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $empLocList= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Manager To Supervisor List',    'Links Managers to their supervisors', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $managerSupList= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Approval Delegates List',    'This list maintains the manager to delegate linkage', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $delegateList= DB_insertId();
    //now create the list definitions

    //activities list
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$activityList}','Activity' )";

    //projects list
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$projectList}','Project #' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$projectList}','Title' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$projectList}','Budgeted Hours' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$projectList}','Work Centre' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$projectList}','Activity','[list:{$activityList},0]' )";
    //tasks list
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$tasksList}','Task Code' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$tasksList}','Description' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$tasksList}','Activity','[list:{$activityList},0]' )";

    //employee to supervisor list
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$empSupList}','Employee','nexlistGetUsers' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$empSupList}','Supervisor','nexlistGetUsers' )";

    //employee locations
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$empLocList}','Location' )";

    //manager to supervisor list
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$managerSupList}','Manager','nexlistGetUsers' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$managerSupList}','Supervisor','nexlistGetUsers' )";

    //delegates list
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$delegateList}','Employee','nexlistGetUsers' )";
    $_LISTSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname,value_by_function) values('{$delegateList}','Delegate','nexlistGetUsers' )";



    //now insert all of the above
    foreach ($_LISTSQL as $sql) {
        DB_query ($sql);
        if (DB_error ()) {
            $err=1;
        }
    }


    $c = config::get_instance();
    $c->add('supervisor_group_id', $supGrpId,'text',0, 0, 0, 110, true, 'nextime');
    //$c->add('sg_list', NULL, 'subgroup', 1, 0, NULL, 0, true, 'nextime');
    $c->add('nxtime_list', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nextime');

    $c->add('nexlist_timesheet_tasks', $tasksList,'text',0, 1, 0, 120, true, 'nextime');
    $c->add('nexlist_nextime_activities', $activityList,'text',0, 1, 0, 130, true, 'nextime');
    $c->add('nexlist_nextime_projects', $projectList,'text',0, 1, 0, 140, true, 'nextime');
    $c->add('nexlist_employee_to_supervisor', $empSupList,'text',0, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_user_locations', $empLocList,'text',0, 1, 0, 160, true, 'nextime');
    $c->add('nexlist_manager_to_supervisor', $managerSupList,'text',0, 1, 0, 170, true, 'nextime');
    $c->add('nexlist_employee_to_delegate', $delegateList,'text',0, 1, 0, 180, true, 'nextime');

    //sample data inserts:
    $sql = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$activityList}', 10, 'Sample Activity #1',1)";
    $res=DB_query($sql);
    $activityID= DB_insertId();

    $sql = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$projectList}', 10, 'Project #1,Project #1,500,Work Centre #1,{$activityID}',1)";
    DB_query($sql);
    $sql = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$tasksList}', 10, 'Task #1,Task #1,{$activityID}',1)";
    DB_query($sql);

    //sample employee to supervisor settings
    $sql = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$empSupList}', 10, '2,2',1)";
    DB_query($sql);
    if($_USER['uid']!=2){
        $sql = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$empSupList}', 10, '{$_USER['uid']},{$_USER['uid']}',1)";
        DB_query($sql);
    }

    return true;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_nextime($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES, $CONF_NEXTIME;

    $install_flag=true;

    if(!function_exists('NXCOM_normalizeVersionNumbers')){
        $install_flag=false;
        COM_errorLog ('The nexpro plugin must be installed and enabled for nexTime to work.  Please install and enable the nexpro plugin before continuing to install nexTime');
    }else{
        foreach($CONF_NEXTIME['dependent_plugins'] as $plugin=>$required_version){
            $sql="SELECT pi_enabled, pi_version from {$_TABLES['plugins']} WHERE pi_name='{$plugin}'";
            $res=DB_query($sql);
            list($pi_enabled, $pi_version)=DB_fetchArray($res);
            $pi_enabled=intval($pi_enabled);
            $pi_version=floatval($pi_version);

            $arr=array($pi_version,$required_version);
            $arr=NXCOM_normalizeVersionNumbers($arr);

            if(!version_compare($arr[0], $arr[1],">=")){
                COM_errorLog ('The ' . $plugin . ' plugin must have a minimum of ' . $required_version . ' for nexTime to work.  Please install and enable the '. $plugin .' v'.$required_version.' plugin before continuing to install nexTime');
                $install_flag=false;
            }
            if($pi_enabled==0){
               COM_errorLog ('The ' . $plugin . ' plugin must be installed/enabled for nexTime to work.  Please install and enable the '. $plugin .' v'.$required_version.' plugin before continuing to install nexTime');
               $install_flag=false;
            }
        }
    }
    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        $install_flag=false;
    }

    return $install_flag;
}



?>