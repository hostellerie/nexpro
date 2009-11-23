<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexForm Plugin v2.2 for the nexPro Portal Server                            |
// | Sept 14, 2009                                                               |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | uninstall_defaults - nexfile plugin uninstall file                          |
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
function plugin_autouninstall_nexform ()
{

    global $_TABLES,$_CONF;

    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(
        	'nxform_definitions',
        	'nxform_fields',
        	'nxform_results',
        	'nxform_resdata',
        	'nxform_restext',
        	'nxform_results_tmp',
        	'nxform_resdata_tmp',
        	'nxform_restext_tmp'),
        /* give the full name of the group, as in the db */
        'groups' => array('nexform Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('nexform.edit'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(''),
        /* give all vars with their name */
        'vars'=> array('')
    );

    return $out;
}

?>