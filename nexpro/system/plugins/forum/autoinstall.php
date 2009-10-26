<?php
// +--------------------------------------------------------------------------+
// | Forum Plugin for Geeklog based framework CMS applications                |
// +--------------------------------------------------------------------------+
// | autoinstall.php                                                          |
// | $Id::                                                                    |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002 - 2009 the following authors:                         |
// | Blaine Lang                 -    blaine@portalparts.com                  |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

require_once ($_CONF['path'] . 'plugins/forum/forum.php');
require_once ($_CONF['path'] . 'plugins/forum/autouninstall.php');

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
function plugin_autoinstall_forum($pi_name)
{
    global $_CONF,$CONF_FORUM;

    @include ($_CONF['path'] . 'plugins/forum/forum.php');

    $pi_display_name = $CONF_FORUM['pi_display_name'];
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_display_name' => $pi_display_name,
        'pi_name'         => $CONF_FORUM['pi_name'],
        'pi_version'      => $CONF_FORUM['version'],
        'pi_gl_version'   => $CONF_FORUM['gl_version'],
        'pi_homepage'     => $CONF_FORUM['pi_url']
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.user'    => 'Forum user permission',
        $pi_name . '.edit'    => 'Forum admin permissions'
    );

    $mappings = array(
        $pi_name . '.user'   => array($pi_admin),
        $pi_name . '.edit'   => array($pi_admin)
    );

    $tables = array(
        'gf_userprefs',
        'gf_topic',
        'gf_categories',
        'gf_forums',
        'gf_settings',
        'gf_watch',
        'gf_moderators',
        'gf_banned_ip',
        'gf_log',
        'gf_userinfo',
        'gf_attachments',
        'gf_bookmarks'
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
* @see      plugin_initconfig_forum
*
*/
function plugin_load_configuration_forum($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_forum();
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
function plugin_postinstall_forum($pi_name)
{
    global $_TABLES;

    $sql = "INSERT INTO {$_TABLES['blocks']} "
         . "(is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon) "
         . " VALUES ('1','Forum News','phpblock','Forumposts','all',0,0,'phpblock_forum_newposts',2,2,3,3,2,2)";
    DB_query($sql);

    $sql = "INSERT INTO {$_TABLES['blocks']} "
         . "(is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon) "
         . " VALUES (0, 'forum_menu', 'phpblock', 'Forum Menu', 'all', 0, 1, 'phpblock_forum_menu', 2,2,3,2,2,2)";
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
function plugin_compatible_with_this_version_forum($pi_name)
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