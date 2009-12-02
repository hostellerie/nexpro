<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                          |
// | Date: Sept. 23, 2009                                                        |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | install.php - nexTime main installation script                              |
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

require_once ('../../../lib-common.php');
require_once ($_CONF['path'] . 'plugins/nextime/nextime.php');
require_once ($_CONF['path'] . 'plugins/nextime/functions.inc');


// Plugin information
$pi_display_name = 'nexTime';
$pi_name         = 'nextime';
$pi_version      = $CONF_NEXTIME['version'];
$gl_version      = '1.6.1';
$pi_url          = 'http://www.nextide.ca/';

// name of the Admin group
$pi_admin        = $pi_display_name . ' Admin';
$pi_user         = $pi_display_name . ' USER';
$pi_supervisor   = $pi_display_name . ' Supervisors';
$pi_finance      = $pi_display_name . ' Finance';

// the plugin's groups - assumes first group to be the Admin group
$GROUPS = array();
$GROUPS[$pi_admin] = 'Has full access to ' . $pi_name . ' features';
$GROUPS[$pi_user] =  'Has User access to ' . $pi_name . ' with NO admin features';
$GROUPS[$pi_supervisor] =  'Users who are deemed Supervisors within the  ' . $pi_name . ' plugin.';
$GROUPS[$pi_finance] =  'Users who are deemed as a Finance user within the  ' . $pi_name . ' plugin.';


$FEATURES = array();
$FEATURES['nextime.admin']       = 'Access to ' .$pi_name. ' Admin Abilites';

$MAPPINGS = array();
$MAPPINGS['nextime.admin']         = array ($pi_admin);



/**
* Checks the requirements for this plugin and if it is compatible with this
* version of Geeklog.
*
* @return   boolean     true = proceed with install, false = not compatible
*
*/
function plugin_compatible_with_this_geeklog_version ()
{
    if (function_exists ('COM_showPoll') || function_exists ('COM_pollVote')) {
        // if these functions exist, then someone's trying to install the
        // plugin on Geeklog 1.3.11 or older - sorry, but that won't work
        return false;
    }

    if (!function_exists ('SEC_getGroupDropdown')) {
        return false;
    }

    return true;
}

/**
* When the install went through, give the plugin a chance for any
* plugin-specific post-install fixes
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_postinstall ()
{
    return true;
}

// The code below should be the same for most plugins and usually won't
// require modifications.

$base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';
$langfile = $base_path . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    require_once ($langfile);
} else {
    require_once ($base_path . 'language/english.php');
}
require_once ($base_path . 'config.php');
require_once ($base_path . 'functions.inc');
require_once $_CONF['path_system'] . 'classes/config.class.php';

// Only let Root users access this page
if (!SEC_inGroup ('Root')) {
    // Someone is trying to illegally access this page
    COM_accessLog ("Someone has tried to illegally access the {$pi_display_name} install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);

    $display = COM_siteHeader ('menu', $LANG_ACCESS['accessdenied'])
             . COM_startBlock ($LANG_ACCESS['accessdenied'])
             . $LANG_ACCESS['plugin_access_denied_msg']
             . COM_endBlock ()
             . COM_siteFooter ();

    echo $display;
    exit;
}



/**
* Loads the configuration records for the GL Online Config Manager
*
* @return   boolean     true = proceed with install, false = an error occured
*
*/
function plugin_load_configuration()
{
    global $_CONF, $base_path,$_NEXPRO_DEFAULT;

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';
    return plugin_initconfig_nextime();

}



function show_prerequisites() {
    global $_CONF, $_TABLES, $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
    $disabled = '';

    $p = new Template($_CONF['path'] . 'plugins/' . $pi_name . '/templates/');
    $p->set_file('prereq_form', 'prereq_form.thtml');

    $p->set_var('layout_url', $_CONF['layout_url']);
    $p->set_var('site_admin_url', $_CONF['site_admin_url']);
    $p->set_var('pi_name', $pi_name);
    $p->set_var('pi_display_name', $pi_display_name);
    $p->set_var('pi_image', PLG_getIcon($pi_name));

    $content_function = 'plugin_getinstallcontent_' . $pi_name;
    if (function_exists($content_function)) {
        $content = $content_function();
    }
    else if (function_exists('plugin_getinstallcontent_nexpro')) {
        $content = plugin_getinstallcontent_nexpro();
    }
    else {
        $content = '';
    }
    $p->set_var('content', $content);

    //test if nexpro plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexpro' && pi_enabled=1") != '') ? 'icon_check.png':'icon_fail.png';
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: nexPro is not installed or enabled");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> nexPro plugin is installed and enabled', true);

    //test if nexlist plugin is installed
    $image = (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexlist' && pi_enabled=1") != '') ? 'icon_check.png':'icon_fail.png';
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: nexList is not installed or enabled");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> nexList plugin is installed and enabled', true);

    //test if data directory has write permissions
    $fp = @fopen($_CONF['path_html'] . $pi_name . '/reports/output/test.txt', 'w');
    if ($fp != NULL) {
        fclose($fp);
        unlink($_CONF['path_html'] . $pi_name . '/reports/output/test.txt');
        $image = 'icon_check.png';
    }
    else {
        $image = 'icon_fail.png';
    }
    if ($image == 'icon_fail.png') {
        $disabled = ' disabled="disabled"';
        COM_errorLog("Cannot install $pi_name: Cannot write to \"{$_CONF['path_html']}{$pi_name}/reports/output/\"");
    }
    $p->set_var('prereqs', '<li style="list-style-image: url('.$_CONF['layout_url'].'/'.$pi_name.'/images/admin/'.$image.');"> Write permissions on "'.$_CONF['path_html'].$pi_name.'/reports/output/"', true);


    $p->set_var('disabled', $disabled);

    $p->parse('output', 'prereq_form');

    return $p->finish($p->get_var('output'));
}




/**
* Puts the datastructures for this plugin into the Geeklog database
*
*/
function plugin_install_now()
{
    global $_CONF, $_TABLES, $_USER, $_DB_dbms,
           $GROUPS, $FEATURES, $MAPPINGS, $DEFVALUES, $base_path,
           $pi_name, $pi_display_name, $pi_version, $gl_version, $pi_url;
    COM_errorLog ("Attempting to install the $pi_display_name plugin", 1);



    if (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexpro'") == '') {     //install nexpro first
        COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing', 1);
        echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=2&plugin='.$pi_name);
        exit(0);
    }

    if(!function_exists("nexlistValue")){
        COM_errorLog ('The nexList plugin is not installed.  Please install it before continuing', 1);
        echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=1&plugin='.$pi_name);
        exit(0);
    }


     // Load the online configuration records
    if (function_exists('plugin_load_configuration')) {
        if (!plugin_load_configuration()) {
            PLG_uninstall($pi_name);
            return false;
        }
    }



    // create the plugin's groups
    $admin_group_id = 0;
    foreach ($GROUPS as $name => $desc) {
        COM_errorLog ("Attempting to create $name group", 1);

        $grp_name = addslashes ($name);
        $grp_desc = addslashes ($desc);
        $res=DB_query ("INSERT INTO {$_TABLES['groups']} (grp_name, grp_descr) VALUES ('$grp_name', '$grp_desc')",1);

        if($name==$pi_display_name . ' Supervisors'){
            $supGrpId=DB_insertId();
        }
        if (DB_error ()) {
            PLG_uninstall($pi_name);
            return false;
        }

        // replace the description with the new group id so we can use it later
        $GROUPS[$name] = DB_insertId ();
        DB_query ("INSERT INTO {$_TABLES['group_assignments']} VALUES "
              . "({$GROUPS[$name]}, NULL, 1)");
        // assume that the first group is the plugin's Admin group
        if ($admin_group_id == 0) {
            $admin_group_id = $GROUPS[$name];
        }
    }

    // Create the Plugins Tables
    require_once($_CONF['path'] . 'plugins/nextime/sql/nextime_'. $pi_version . '_' . $_DB_dbms . '_install.php');

    for ($i = 1; $i <= count($_SQL); $i++) {
        $progress .= "executing " . current($_SQL) . "<br>\n";
        COM_errorLOG("executing " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("Error Creating $table table",1);
            PLG_uninstall($pi_name);
            return false;
            exit;
        }
        next($_SQL);
    }

    // Add the plugin's features
    COM_errorLog ("Attempting to add $pi_display_name feature(s)", 1);

    foreach ($FEATURES as $feature => $desc) {
        $ft_name = addslashes ($feature);
        $ft_desc = addslashes ($desc);
        DB_query ("INSERT INTO {$_TABLES['features']} (ft_name, ft_descr) "
                  . "VALUES ('$ft_name', '$ft_desc')", 1);
        if (DB_error ()) {
            PLG_uninstall($pi_name);

            return false;
        }

        $feat_id = DB_insertId ();

        if (isset ($MAPPINGS[$feature])) {
            foreach ($MAPPINGS[$feature] as $group) {
                COM_errorLog ("Adding $feature feature to the $group group", 1);
                DB_query ("INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ($feat_id, {$GROUPS[$group]})");
                if (DB_error ()) {
                    PLG_uninstall($pi_name);

                    return false;
                }
            }
        }
    }


    // Finally, register the plugin with Geeklog
    COM_errorLog ("Registering $pi_display_name plugin with Geeklog", 1);
    // silently delete an existing entry
    DB_delete ($_TABLES['plugins'], 'pi_name', $pi_name);

    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) VALUES "
        . "('$pi_name', '$pi_version', '$gl_version', '$pi_url', 1)");


    if (DB_error ()) {
        PLG_uninstall($pi_name);

        return false;
    }

    //install the nexlist lists
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
    $c->add('supervisor_group_id', $supGrpId,'text',0, 0, 0, 150, true, 'nextime');
    $c->add('sg_list', NULL, 'subgroup', 1, 0, NULL, 0, true, 'nextime');
    $c->add('nxtime_list', NULL, 'fieldset', 1, 1, NULL, 0, true, 'nextime');

    $c->add('nexlist_timesheet_tasks', $tasksList,'text',1, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_nextime_activities', $activityList,'text',1, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_nextime_projects', $projectList,'text',1, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_employee_to_supervisor', $empSupList,'text',1, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_user_locations', $empLocList,'text',1, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_manager_to_supervisor', $managerSupList,'text',1, 1, 0, 150, true, 'nextime');
    $c->add('nexlist_employee_to_delegate', $delegateList,'text',1, 1, 0, 150, true, 'nextime');

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

    // give the plugin a chance to perform any post-install operations
    if (function_exists ('plugin_postinstall')) {
        if (!plugin_postinstall ()) {
            PLG_uninstall($pi_name);

            return false;
        }
    }

    COM_errorLog ("Successfully installed the $pi_display_name plugin!", 1);

    return true;
}


// MAIN
$action = $_REQUEST['action'];
$display = '';

switch ($action) {
case 'install':     //display page to check for requirements
    if (DB_count ($_TABLES['plugins'], 'pi_name', $pi_name) == 0) {
        $display .= COM_siteHeader ('menu', $LANG01[77])
                 . show_prerequisites()
                 . COM_siteFooter();
    } else {
        // plugin already installed
        $display .= COM_siteHeader ('menu', $LANG01[77])
                 . COM_startBlock ($LANG32[6])
                 . '<p>' . $LANG32[7] . '</p>'
                 . COM_endBlock ()
                 . COM_siteFooter();
    }
    break;

case 'uninstall': //uninstall the plugin
    if ($_REQUEST['action'] == 'uninstall') {
        $uninstall_plugin = 'plugin_uninstall_' . $pi_name;
        if ($uninstall_plugin ()) {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=45');
        } else {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=73');
        }
    }

case 'install_now': //install the plugin
    if (plugin_compatible_with_this_geeklog_version ()) {
        if (plugin_install_now ()) {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=44');
        } else {
            $display = COM_refresh ($_CONF['site_admin_url']
                                    . '/plugins.php?msg=72');
        }
    } else {
        // plugin needs a newer version of Geeklog
        $display .= COM_siteHeader ('menu', $LANG32[8])
                 . COM_startBlock ($LANG32[8])
                 . '<p>' . $LANG32[9] . '</p>'
                 . COM_endBlock ()
                 . COM_siteFooter ();
    }
    break;
}



echo $display;





?>