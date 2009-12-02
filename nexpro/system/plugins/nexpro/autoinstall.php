<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
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

require_once ($_CONF['path'] . 'plugins/nexpro/autouninstall.php');
require_once ($_CONF['path'] . 'plugins/nexpro/nexpro.php');

if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}
/**
* Autoinstall API functions for the nexList plugin
*
* @package nexPro
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nexpro($pi_name)
{

    global $CONF_NEXPRO, $_CONF;
    @require ($_CONF['path'] . 'plugins/nexpro/nexpro.php');
    $pi_name         = $CONF_NEXPRO['pi_name'];
    $pi_display_name = $CONF_NEXPRO['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => $CONF_NEXPRO['version'],
        'pi_gl_version'   => $CONF_NEXPRO['pi_gl_version'],
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
        'tagwords',
        'tagword_items',
        'tagword_metrics'
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
* @see      plugin_initconfig_nexrpo
*
*/
function plugin_load_configuration_nexpro($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexpro();
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
function plugin_postinstall_nexpro($pi_name)
{
    global $_DB_dbms, $_CONF, $_DB_table_prefix, $_TABLES,$CONF_NEXPRO ;
    $pi_version=$CONF_NEXPRO['version'];
    $gl_version=$CONF_NEXPRO['pi_gl_version'];
    $pi_url='http://www.nextide.ca';
    // silently delete an existing entry
    DB_delete ($_TABLES['plugins'], 'pi_name', $pi_name);

    //modification to make sure nexpro plugin is installed at the top of the list
    $res = DB_query("SELECT pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled FROM {$_TABLES['plugins']} LIMIT 1");
    list ($old_pi_name, $old_pi_version, $old_gl_version, $old_pi_url, $old_pi_enabled) = DB_fetchArray($res);

    //first entry is stored, now UPDATE the first entry with nexpro's
    $sql  = "UPDATE {$_TABLES['plugins']} ";
    $sql .= "SET pi_name='$pi_name', pi_version='$pi_version', pi_gl_version='$gl_version', pi_homepage='$pi_url', pi_enabled=1 ";
    $sql .= "WHERE pi_name='$old_pi_name'";
    DB_query($sql);

    //now insert the first entry to the plugin table again
    $sql = "INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) VALUES ";
    $sql .=  "('$old_pi_name', '$old_pi_version', '$old_gl_version', '$old_pi_url', '$old_pi_enabled')";
    DB_query($sql);

    return true;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_nexpro($pi_name)
{
    global $_CONF, $_DB_dbms;

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        return false;
    }

    //now here we are going to check if all of the files are installed in the right areas:
    $farray=array(
        $_CONF['path'].'system/nexpro/classes/multiupload.class.php',
        $_CONF['path'].'system/nexpro/classes/nexfilter.class.php',
        $_CONF['path'].'system/nexpro/classes/TreeMenu.php',
        $_CONF['path'].'system/nexpro/classes/nexreport/formatter.class.php',
        $_CONF['path'].'system/nexpro/classes/nexreport/report.php',
        $_CONF['path'].'system/nexpro/classes/nexreport/format/display.class.php',
        $_CONF['path'].'system/nexpro/classes/nexreport/format/excel.class.php',
        $_CONF['path'].'system/nexpro/classes/nexreport/type/users.php',
        $_CONF['path_html'].'javascript/php_serializer.js',
        $_CONF['path_html'].'javascript/nexYUICal.js'
    );

    $missingfiles=false;
    $errormessage="";
    foreach($farray as $file_to_check){
        $handle=@fopen($file_to_check,"r");
        if($handle===false){
                $missingfiles=true;
                $errormessage.="nexPro plugin requires the {$file_to_check} to exist.\n";
        }

    }
    if($missingfiles){
        COM_errorlog("You need to move all of the nexPro plugin files to the proper directories before installation.\n". $errormessage);
        return false;
    }


    //now check if the sanitize class has a specific method within it:
    require_once($_CONF['path'].'system/classes/sanitize.class.php');
    $testsanitize=new sanitizer();
    if(!method_exists($testsanitize,'normalize')){
        COM_errorlog("The {$_CONF['path']}/system/classes/sanitize.class.php file needs to be updated with the one provided with the nexPro plugin.");
        COM_errorlog("Please update the sanitize.class.php file to install the nexPro plugin");
        return false;
    }

    return true;



}

?>