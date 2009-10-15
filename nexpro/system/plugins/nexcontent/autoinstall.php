<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.3.0 for the nexPro Portal Server                     |
// | October 9, 2009                                                           |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

require_once ($_CONF['path'] . 'plugins/nexcontent/autouninstall.php');
if (strpos(strtolower($_SERVER['PHP_SELF']), 'autoinstall.php') !== false) {
    die('This file can not be used on its own!');
}
/**
* Autoinstall API functions for the nexList plugin
*
* @package nexContent
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_nexcontent($pi_name)
{
    $pi_name         = 'nexcontent';
    $pi_display_name = 'nexContent';
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '2.3.0',
        'pi_gl_version'   => '1.6.0',
        'pi_homepage'     => 'http://www.nextide.ca/'
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.edit'     => 'Plugin Admin',
        $pi_name . '.user'     => 'Plugin User'
    );

    $mappings = array(
        $pi_name . '.edit'   => array($pi_admin),
        $pi_name . '.user'   => array($pi_admin),
    );

    $tables = array(
        'nxcontent',
        'nxcontent_pages',
        'nxcontent_images'
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
* @see      plugin_initconfig_nexcontent
*
*/
function plugin_load_configuration_nexcontent($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexcontent();
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
function plugin_postinstall_nexcontent($pi_name)
{
    global $_DB_dbms, $_CONF, $_DB_table_prefix, $_TABLES ;

    $sql= "INSERT INTO {$_TABLES['nexcontent_pages']} (id,pid,type,pageorder,name,blockformat,heading,content,meta_description,meta_keywords) VALUES (1, 0, 'category', '10', 'frontpage', 'none', 'Front Page Folder', 'Create a page under this folder if you want to have a page loaded as the frontpage', '', '');";
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
function plugin_compatible_with_this_version_nexcontent($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES, $CONF_SE_PREREQUISITE;

    //prereq testing
    $install_flag=true;
    //  so lets test out to see if the nexPro plugin is installed.  If not, bail out with an error
    $nxpro=intval(DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'"));
    if ($nxpro==0) {     //install nexpro first
        if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'") == '0') {     //nexpro disabled?
            COM_errorLog ('The nexpro plugin must be enabled for nexContent to work.  Please enable the nexpro it before continuing to install nexContent');
        }else{
            COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing to install nexContent');
        }
        $install_flag=false;
    }

    //test if data directory has write permissions
    $fp = @fopen($CONF_SE_PREREQUISITE['uploadpath'] . 'test.txt', 'w');
    if ($fp != NULL) {
        fclose($fp);
        unlink($CONF_SE_PREREQUISITE['uploadpath'] . 'test.txt');
    }
    else {
        COM_errorLog("nexContent requires the {$CONF_SE_PREREQUISITE['uploadpath']} file location to be write enabled before installing.");
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