<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.0 for the nexPro Portal Server                          |
// | Oct 15, 2009                                                                |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | uninstall_defaults - nexflow plugin uninstall file                          |
// +-----------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                                |
// | Blaine Lang            - Blaine.Lang@nextide.ca                             |
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
function plugin_autouninstall_nexflow ()
{

    global $_TABLES,$_CONF;

    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(
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
            'nf_projectdataresults'),

        /* give the full name of the group, as in the db */
        'groups' => array('nexflow Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('nexflow.admin','nexflow.edit', 'nexflow.user'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(''),
        /* give all vars with their name */
        'vars'=> array('nexflow_admin')
    );

    return $out;
}

?>