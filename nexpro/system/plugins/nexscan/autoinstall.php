<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexScan Plugin v1.0.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Eric de la Chevrotiere - Eric DOT delaChevrotiere AT nextide DOT ca       |
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

require_once ($_CONF['path'] . 'plugins/nexscan/autouninstall.php');
require_once ($_CONF['path'] . 'plugins/nexscan/nexscan.php');

if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}
/**
* Autoinstall API functions for the nexScan plugin
*
* @package nexscan
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nexscan($pi_name)
{

    global $CONF_NS, $_CONF;
    @require ($_CONF['path'] . 'plugins/nexscan/nexscan.php');
    $pi_name         = $CONF_NS['pi_name'];
    $pi_display_name = $CONF_NS['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => $CONF_NS['version'],
        'pi_gl_version'   => $CONF_NS['pi_gl_version'],
        'pi_homepage'     => 'http://www.nextide.ca/'
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
    );

    $mappings = array(
    );

    $tables = array(
        'nxscan_cssscan',
        'nxscan_options'
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
* @see      plugin_initconfig_nexscan
*
*/
function plugin_load_configuration_nexscan($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexscan();
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
function plugin_postinstall_nexscan($pi_name)
{
    global $_DB_dbms, $_CONF, $_DB_table_prefix, $_TABLES,$CONF_NS ;
    $pi_version=$CONF_NS['version'];
    $gl_version=$CONF_NS['pi_gl_version'];
    $pi_url='http://www.nextide.ca';

    return true;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_nexscan($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES, $CONF_NS;

    $install_flag=true;

    if(!function_exists('NXCOM_normalizeVersionNumbers')){
        $install_flag=false;
        COM_errorLog ('The nexpro plugin must be installed and enabled for nexTime to work.  Please install and enable the nexpro plugin before continuing to install nexTime');
    }else{
        foreach($CONF_NS['dependent_plugins'] as $plugin=>$required_version){
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