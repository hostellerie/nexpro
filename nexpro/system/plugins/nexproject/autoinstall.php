<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.1.0 for the nexPro Portal Server                     |
// | Date: Oct. 6, 2009                                                        |
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

include_once ($_CONF['path'] . 'plugins/nexproject/autouninstall.php');

if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* Autoinstall API functions for the nexList plugin
*
* @package nexProject
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nexproject($pi_name)
{

    global $_TABLES, $_CONF;
    $pi_name         = 'nexproject';
    $pi_display_name = 'nexProject';
    $pi_admin        = $pi_display_name . ' Admin';

    $install_flag=true;
    //  so lets test out to see if the nexPro,nexFile, nexList and forum plugins are installed.  If not, bail out with an error
    $nxpro=intval(DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'"));
    if ($nxpro==0) {     //install nexpro first
        if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'") == '0') {     //nexpro disabled?
            COM_errorLog ('The nexpro plugin must be enabled for nexProject to work.  Please enable the nexpro it before continuing to install nexProject', 1);
        }else{
            COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing to install nexProject', 1);
        }
        $install_flag=false;
    }

    if(!function_exists("nexlistValue")){
        COM_errorLog ('The nexList plugin is not installed.  Please install it before continuing to install nexProject', 1);
        $install_flag=false;
    }


    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '2.1.0',
        'pi_gl_version'   => '1.6.0',
        'pi_homepage'     => 'http://www.nextide.ca/'
    );

    $groups = array(
        $pi_admin       => 'Has full access to ' . $pi_display_name . ' features',
    );

    $features = array(
        $pi_name . '.admin'    => 'Plugin Admin'
    );

    $mappings = array(
        $pi_name . '.admin'  => array($pi_admin)
    );

    $tables = array(
        'nxprj_category',
        'nxprj_department',
        'nxprj_filters',
        'nxprj_location',
        'nxprj_objective',
        'nxprj_permissions',
        'nxprj_projects',
        'nxprj_projperms',
        'nxprj_sessions',
        'nxprj_sorting',
        'nxprj_statuslog',
        'nxprj_tasks',
        'nxprj_taskusers',
        'nxprj_users',
        'nxprj_lockcontrol',
        'nxprj_tasksemaphore'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    if($install_flag){
        return $inst_parms;
    }else{
        return false;
    }

}

/**
* Load plugin configuration from database
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true on success, otherwise false
* @see      plugin_initconfig_nexproject
*
*/
function plugin_load_configuration_nexproject($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexproject();
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
function plugin_postinstall_nexproject($pi_name)
{
    global $_DB_dbms, $_CONF, $_DB_table_prefix, $_TABLES ;
    require_once ($_CONF['path'] . 'plugins/nexproject/nexproject.php');

    // fix nexproject block group ownership
    $blockAdminGroup = DB_getItem ($_TABLES['groups'], 'grp_id',
                                   "grp_name = 'Block Admin'");
    if ($blockAdminGroup > 0) {
        // set the block's permissions
        $A = array ();
        SEC_setDefaultPermissions ($A, $_CONF['default_permissions_block']);

        // ... and make it the last block on the right side
        $result = DB_query ("SELECT MAX(blockorder) FROM {$_TABLES['blocks']} WHERE onleft = 0");
        list($order) = DB_fetchArray ($result);
        $order += 10;

        DB_query ("UPDATE {$_TABLES['blocks']} SET group_id = $blockAdminGroup, blockorder = $order, perm_owner = {$A['perm_owner']}, perm_group = {$A['perm_group']}, perm_members = {$A['perm_members']}, perm_anon = {$A['perm_anon']} WHERE (type = 'phpblock') AND (phpblockfn = 'phpblock_nexproject')");

    }

    $nexfile = true;
    if(!function_exists("fm_createCategory")){
        //COM_errorLog ('The nexFile plugin is not installed.  Please install it before continuing', 1);
        //echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=2&plugin='.$pi_name);
        //exit(0);
        $nexfile = false;
    }
    $forum = true;
    if(!function_exists("forum_addForum")){
        //COM_errorLog ('The forum plugin is not installed.  Please install it before continuing', 1);
        //echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=4&plugin='.$pi_name);
        //exit(0);
        $forum = false;
    }



    //And now, install the lookup lists and add nxprj config values to house the nexlist items

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values (    'all','nexPro',    'Locations',    'List of locations', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $locID= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values ('all','nexPro','Departments','List of Departments', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $deptID= DB_insertId();

    $sql = "insert into {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    values ('all','nexPro', 'Categories','List of Categories', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $catID= DB_insertId();

    $sql = "INSERT INTO {$_TABLES['nexlist']} (plugin, category, name, description, listfields, edit_perms, view_perms, active)
    VALUES ('all', 'nexPro', 'Objectives', 'List of Project Objectives', 1, 1, 2, 1);";
    $res=DB_query($sql);
    $objID= DB_insertId();

    /* create lookuplist Fields for list definitions */
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$locID}','Location' )";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$deptID}','Department' )";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$catID}','Department' )";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistfields']} (lid, fieldname) values('{$objID}','Objective' )";

    /* create lookuplist list records for each definition */
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 10, 'Toronto',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 20, 'Hong Kong',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 30, 'Brisbane',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 40, 'Tokyo',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 50, 'New York',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 60, 'San Fransisco',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$locID}', 70, 'London',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 10, 'Sales',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 20, 'Information Technology',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 30, 'Marketing',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 40, 'Finance',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 50, 'Operations',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 60, 'Legal',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$deptID}', 70, 'Revenue',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 10, 'Revenue',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 20, 'Safety',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 30, 'Environment',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 40, 'Training',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 50, 'Product Development',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 60, 'Branding',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 70, 'Investment',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) values ('{$catID}', 80, 'Capital Expenditure',1)";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) VALUES ('{$objID}', 90, 'Business Growth', 1);";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) VALUES ('{$objID}', 100, 'Product Development', 1);";
    $_PRJSQL[] = "insert into {$_TABLES['nexlistitems']} (lid, itemorder, value, active) VALUES ('{$objID}', 110, 'Objective 3', 1);";

    foreach ($_PRJSQL as $sql) {
            DB_query ($sql);
            if (DB_error ()) {
                $err=1;
            }
        }
    $c = config::get_instance();
    $c->add('prj_list', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nexproject');
    $c->add('nexlist_locations', $locID,'text',0, 1, 0, 150, true, 'nexproject');
    $c->add('nexlist_departments', $deptID,'text',0, 1, 0, 160, true, 'nexproject');
    $c->add('nexlist_category', $catID,'text',0, 1, 0, 170, true, 'nexproject');
    $c->add('nexlist_objective', $objID,'text',0, 1, 0, 180, true, 'nexproject');

    //we are assuming that nexfile and the forum are installed here.  We cannot get this far if they werent!
    //the first thing we do is create a new nexFile category which will be used as the base category ID to dump files into for projects

    if ($nexfile) {
        $arr=fm_createCategory(0,'nexProject Category','This base category is used by the nexProject plugin to create document repositories for each project.',true);
        //config parms for this
        $c->add('prj_file', NULL, 'fieldset', 0, 2, NULL, 0, true, 'nexproject');
        $c->add('nexfile_parent', $arr[0],'text',0, 2, 0, 190, true, 'nexproject');
    }
    else {
      //config parms for this
        $c->add('prj_file', NULL, 'fieldset', 0, 2, NULL, 0, true, 'nexproject');
        $c->add('nexfile_parent', 0,'text',0, 2, 0, 190, true, 'nexproject');
    }

    //and now, we create a new forum category and dump that into the config database
    if ($forum) {
        $sql ="INSERT INTO {$_TABLES['gf_categories']} (cat_order,cat_name,cat_dscp) values (0,'nexProject Category','This base category is used by the nexProject plugin to create forum repositories for each project.') ";
        DB_query($sql);
        $catid=DB_insertId();
        $c->add('prj_forum', NULL, 'fieldset', 0, 3, NULL, 0, true, 'nexproject');
        $c->add('forum_parent', $catid,'text',0, 3, 0, 200, true, 'nexproject');
    }
    else {
        $c->add('prj_forum', NULL, 'fieldset', 0, 3, NULL, 0, true, 'nexproject');
        $c->add('forum_parent', 0,'text',0, 3, 0, 200, true, 'nexproject');
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
function plugin_compatible_with_this_version_nexproject($pi_name)
{
    global $_CONF, $_DB_dbms;

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        return false;
    }

    return true;
}

?>