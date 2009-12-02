<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.0 for the nexPro Portal Server                        |
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
require_once ($_CONF['path'] . 'plugins/nexflow/autouninstall.php');
require_once ($_CONF['path'] . 'plugins/nexflow/nexflow.php');

/**
* Autoinstall API functions for the nexflow plugin
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
function plugin_autoinstall_nexflow($pi_name)
{
    global $_CONF, $CONF_NF;

    @require ($_CONF['path'] . 'plugins/nexflow/nexflow.php');

    $pi_display_name = $CONF_NF['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_display_name' => $pi_display_name,
        'pi_name'         => $CONF_NF['pi_name'],
        'pi_version'      => $CONF_NF['version'],
        'pi_gl_version'   => $CONF_NF['gl_version'],
        'pi_homepage'     => $CONF_NF['pi_url']
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.admin'   => 'Administrative access to ' . $pi_display_name,
        $pi_name . '.user'    => 'Plugin user permission - Required if user will have edit rights to pages',
        $pi_name . '.edit'    => 'nexFlow template, variable and handler editing'
    );

    $mappings = array(
        $pi_name . '.admin'  => array($pi_admin),
        $pi_name . '.user'   => array($pi_admin),
        $pi_name . '.edit'   => array($pi_admin)
    );

    $tables = array(
        'nf_process',
        'nf_queue',
        'nf_template',
        'nf_templatedata',
        'nf_templateassignment',
        'nf_handlers',
        'nf_steptype',
        'nf_templatedatanextstep',
        'nf_processvariables',
        'nf_templatevariables',
        'nf_ifprocessarguments',
        'nf_ifoperators',
        'nf_queuefrom',
        'nf_notifications',
        'nf_productionassignments',
        'nf_useraway',
  		'nf_appgroups',
    	'nf_projects',
    	'nf_projectforms',
    	'nf_projecttimestamps',
    	'nf_projectcomments',
    	'nf_projecttaskhistory',
    	'nf_projectapprovals',
    	'nf_projectattachments',
    	'nf_projectdatafields',
   		'nf_projectdataresults'
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
* @see      plugin_initconfig_nexflow
*
*/
function plugin_load_configuration_nexflow($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_nexflow();
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
function plugin_postinstallnexflow($pi_name)
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
function plugin_compatible_with_this_version_nexflow($pi_name)
{
    global $_CONF, $_DB_dbms, $_TABLES;

    //  so lets test out to see if the nexPro is installed.  If not, bail out with an error
    $nxpro=intval(DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'"));
    if ($nxpro==0) {     //install nexpro first
        if (DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name='nexpro'") == '0') {     //nexpro disabled?
            COM_errorLog ('The nexpro plugin must be enabled for nexflow to work.  Please enable the nexpro it before continuing to install nexflow.');
        }else{
            COM_errorLog ('The nexpro plugin is not installed.  Please install it before continuing to install nexflow');
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