<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.1 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
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
require_once ($_CONF['path'] . 'plugins/nexfile/nexfile.php');
require_once ($_CONF['path'] . 'plugins/nexfile/autouninstall.php');

/**
* Autoinstall API functions for the nexFile plugin
*
* @package nexFile
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
/**
 * @param $pi_name
 * @return unknown_type
 */
function plugin_autoinstall_nexfile($pi_name)
{
    global $_CONF;

    @include ($_CONF['path'] . 'plugins/nexfile/nexfile.php');

    $pi_display_name = $_FMCONF['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_display_name' => $pi_display_name,
        'pi_name'         => $_FMCONF['pi_name'],
        'pi_version'      => $_FMCONF['version'],
        'pi_gl_version'   => $_FMCONF['gl_version'],
        'pi_homepage'     => $_FMCONF['pi_url']
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.admin'   => 'Administrative access to ' . $pi_display_name,
        $pi_name . '.user'    => 'Plugin user permission - Required if user will have edit rights to pages',
        $pi_name . '.edit'    => 'Plugin Selected File and Category Admin'
    );

    $mappings = array(
        $pi_name . '.admin'  => array($pi_admin),
        $pi_name . '.user'   => array($pi_admin),
        $pi_name . '.edit'   => array($pi_admin)
    );

    $tables = array(
        'nxfile_access',
        'nxfile_categories',
        'nxfile_files',
        'nxfile_filedetail',
        'nxfile_fileversions',
        'nxfile_notifications',
        'nxfile_filesubmissions',
        'nxfile_recentfolders',
        'nxfile_downloads',
        'nxfile_favorites',
        'nxfile_usersettings',
        'nxfile_notificationlog',
        'auditlog',
        'nxfile_import_queue',
        'nxfile_export_queue',
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
function plugin_load_configuration_nexfile($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexfile();
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
function plugin_postinstall_nexfile($pi_name)
{
    global $_TABLES;

    $sql = "INSERT INTO {$_TABLES['blocks']} "
         . "(is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon) "
         . " VALUES ('1','Latest Files','phpblock','Latest Files','all',0,0,'phpblock_nexfile_latestfiles',2,2,3,3,2,2)";
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
function plugin_compatible_with_this_version_nexfile($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES;

    //  so lets test out to see if the nexPro is installed.  If not, bail out with an error
    $nxpro=intval(DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'"));
    if ($nxpro==0) {     //install nexpro first
        if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'") == '0') {     //nexpro disabled?
            COM_errorLog ('The nexpro plugin must be enabled for nexfile to work.  Please enable the nexpro it before continuing to install nexfile.');
        }else{
            COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing to install nexfile');
        }
        return false;
    }

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        return false;
    }

    return true;
}

?>