<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.2.0 for the nexPro Portal Server                        |
// | Oct 1, 2009                                                               |
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

require_once ($_CONF['path'] . 'plugins/nexlist/autouninstall.php');

/**
* Autoinstall API functions for the nexList plugin
*
* @package nexList
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nexlist($pi_name)
{

    $pi_name         = 'nexlist';
    $pi_display_name = 'nexList';
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '2.2.0',
        'pi_gl_version'   => '1.6.0',
        'pi_homepage'     => 'http://www.nextide.ca/'
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.edit'    => 'Plugin nexList Admin'
    );

    $mappings = array(
        $pi_name . '.edit'   => array($pi_admin)
    );

    $tables = array(
        'nxlist',
        'nxlist_fields',
        'nxlist_items'
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
* @see      plugin_initconfig_nexfile
*
*/
function plugin_load_configuration_nexlist($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexlist();
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
function plugin_postinstall_nexlist($pi_name)
{
    global $_DB_dbms, $_CONF, $_DB_table_prefix, $_TABLES ;
    require_once ($_CONF['path'] . 'plugins/nexlist/nexlist.php');
    $DEFVALUES = array();
    $DEFVALUES[] = " INSERT INTO {$_TABLES['nexlist']} (id, plugin, category, name, description, listfields, edit_perms, view_perms, active) VALUES (1, 'all', 'Testing', 'Example List', 'This is an example list definition that has 2 fields. The one field is using a function to provide the dropdown list options. This function could be obtaining the list of options from a list maintained by this plugin - so it can build on itself.', 'User Name', 2, 2, 1);";
    $DEFVALUES[] = "INSERT INTO {$_TABLES['nexlistfields']} (id, lid, fieldname, value_by_function) VALUES (1, 1, 'Username', 'nexlistGetUsers');";
    $DEFVALUES[] = " INSERT INTO {$_TABLES['nexlistfields']} (id, lid, fieldname, value_by_function) VALUES (2, 1, 'Location', '');";
    $DEFVALUES[] = "INSERT INTO {$_TABLES['nexlistitems']} (id, lid, value, active, itemorder) VALUES (1, 1, '1', 1, 10);";

    if ($_DB_dbms != 'mssql') {
        COM_errorLog ('Inserting default data', 1);
        foreach ($DEFVALUES as $sql) {
            DB_query ($sql, 1);
            if (DB_error ()) {
                PLG_uninstall($pi_name);
                return false;
            }
        }
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
function plugin_compatible_with_this_version_nexlist($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES, $_CONF;

    $install_flag=true;
    //  so lets test out to see if the nexPro is installed.  If not, bail out with an error
    $nxpro=intval(DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'"));
    if ($nxpro==0) {     //install nexpro first
        if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'") == '0') {     //nexpro disabled?
            COM_errorLog ('The nexpro plugin must be enabled for nexList to work.  Please enable the nexpro it before continuing to install nexList');
        }else{
            COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing to install nexList');
        }
        $install_flag=false;
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