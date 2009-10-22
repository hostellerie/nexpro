<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | Sept 16, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - randy.kolenko@nextide.ca                         |
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

include_once ($_CONF['path'] . 'plugins/nexmenu/autouninstall.php');

if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* Autoinstall API functions for the nexList plugin
*
* @package nexMenu
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nexmenu($pi_name)
{

    global $CONF_NEXMENU;
    $pi_name         = $CONF_NEXMENU['pi_name'];
    $pi_display_name = $CONF_NEXMENU['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $CONF_NEXMENU['pi_name'],
        'pi_display_name' => $CONF_NEXMENU['pi_display_name'],
        'pi_version'      => $CONF_NEXMENU['version'],
        'pi_gl_version'   => $CONF_NEXMENU['gl_version'],
        'pi_homepage'     => 'http://www.nextide.ca/'
    );

    $groups = array(
        $pi_admin       => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.edit'    => 'Plugin Admin'
    );

    $mappings = array(
        $pi_name . '.edit'  => array($pi_admin),
    );

    $tables = array(
        'nxmenu',
        'nxmenu_language',
        'nxmenu_config'
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
* @see      plugin_initconfig_nexmenu
*
*/
function plugin_load_configuration_nexmenu($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexmenu();
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
function plugin_postinstall_nexmenu($pi_name)
{

    return true;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_nexmenu($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES, $CONF_NEXMENU;

    $install_flag=true;

    if(!function_exists('NXCOM_normalizeVersionNumbers')){
        $install_flag=false;
        COM_errorLog ('The nexpro plugin must be installed and enabled for nexTime to work.  Please install and enable the nexpro plugin before continuing to install nexTime');
    }else{
        foreach($CONF_NEXMENU['dependent_plugins'] as $plugin=>$required_version){
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