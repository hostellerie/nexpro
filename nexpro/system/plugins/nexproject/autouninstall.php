<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexProject Plugin v2.1.0 for the nexPro Portal Server                       |
// | Date: Oct. 6, 2009                                                          |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | uninstall_defaults - nexProject plugin uninstall file                       |
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
global $_TABLES,$_DB_table_prefix,$_PRJCONF;


if (strpos(strtolower($_SERVER['PHP_SELF']), 'autouninstall.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_nexproject ()
{
    global $_PRJCONF, $_TABLES;
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('prj_category','prj_department','prj_location','prj_objective','prj_permissions','prj_users','prj_projects',
                          'prj_sorting','prj_task_users','prj_tasks','prj_statuslog','prj_session','prj_filters','prj_lockcontrol',
                          'prj_projPerms','prj_taskSemaphore','prj_config'),
        /* give the full name of the group, as in the db */
        'groups' => array('nexProject Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('nexproject.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array('phpblock_projectFilter'),
        /* give all vars with their name */
        'vars'=> array()
    );



    if (prj_forumExists()) {
        //using this row's config value, we'll delete all forums with this ID as the parent and then chuck out the category itself...
        $sql="SELECT * FROM {$_TABLES['gf_forums']} where forum_cat={$_PRJCONF['forum_parent']}";
        $forumres=DB_query($sql);
        while($X=DB_fetchArray($forumres)){
            forum_deleteForum($X['forum_id']);
        }
        DB_query("DELETE FROM {$_TABLES['gf_categories']} where id={$_PRJCONF['forum_parent']}");
    }

    if (prj_nexFileExists()) {
        PLG_itemDeleted($_PRJCONF['nexfile_parent'], 'nexproject_filefolder');
    }

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$_PRJCONF['nexlist_locations']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$_PRJCONF['nexlist_locations']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$_PRJCONF['nexlist_locations']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$_PRJCONF['nexlist_departments']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$_PRJCONF['nexlist_departments']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$_PRJCONF['nexlist_departments']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$_PRJCONF['nexlist_category']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$_PRJCONF['nexlist_category']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$_PRJCONF['nexlist_category']}");

    DB_query("DELETE FROM {$_TABLES['nexlistitems']} WHERE lid={$_PRJCONF['nexlist_objective']}");
    DB_query("DELETE FROM {$_TABLES['nexlistfields']} WHERE lid={$_PRJCONF['nexlist_objective']}");
    DB_query("DELETE FROM {$_TABLES['nexlist']} WHERE id={$_PRJCONF['nexlist_objective']}");

    return $out;
}

function prj_nexFileExists() {
    global $_TABLES;
    return (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='nexfile' AND pi_enabled=1") == 'nexfile') ? true:false;
}

function prj_forumExists() {
    global $_TABLES;
    return (DB_getItem($_TABLES['plugins'], 'pi_name', "pi_name='forum' AND pi_enabled=1") == 'forum') ? true:false;
}

?>