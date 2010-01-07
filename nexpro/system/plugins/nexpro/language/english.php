<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.1.0 for the nexPro Portal Server                         |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
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

$LANG_NEXPRO = array (
    'admin_only'        => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'            => 'Plugin',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'install_header'    => 'Install/Uninstall Plugin',
    'installed'         => 'The Plugin is now installed.<p><i>Enjoy,<br><a href="MAILTO:support@nextide.ca">Nextide Support</a></i>',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'   => 'The nexPro base plugin installation was successful',
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'enabled'           => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'           => 'nexPro Plugin De-Install Warning'
);


$PLG_nexpro_MESSAGE11 = 'nexPro Plugin Upgrade completed - no errors';
$PLG_nexpro_MESSAGE12 = 'nexPro Plugin Upgrade failed - check error.log';


// Localization of the Admin Configuration UI
$LANG_configsections['nexpro'] = array(
    'label' => 'Nexpro',
    'title' => 'Nexpro Configuration'
);

$LANG_confignames['nexpro'] = array(
    'yui_base_url' => 'Base URL to load YUI Libraries?',
    'load_yuiloader' => 'Use the YUI Loader to load required libraries on demand?',
    'load_treemenu' => 'Load Treemenu Library?',
    'load_sarissa' => 'Load Sarissa (AJAX) library?',
    'load_calendar' => 'Load Pop-up Calendar Library?',
    'load_fvalidate' => 'Load Forms Validation Library?',
    'load_yui' => 'Load Core Library?',
    'load_yui_dom' => 'Load DOM Library?',
    'load_yui_event' => 'Load Event Library?',
    'load_yui_container' => 'Load Container Library?',
    'load_yui_calendar' => 'Load Calendar Library?',
    'load_yui_menu' => 'Load Menu Library?',
    'load_yui_button' => 'Load Button Library?',
    'load_yui_animation' => 'Load Animation Library?',
    'load_yui_connection' => 'Load Connection Library?',
    'load_yui_dragdrop' => 'Load Drag and Drop Library?',
    'load_yui_element' => 'Load Element Library?',
    'load_yui_treeview' => 'Load Treeview Library?',
    'load_yui_layout' => 'Load Layout Library?',
    'load_yui_cookie' => 'Load Cookie Utility?',
    'load_yui_logger' => 'Load Logger Utility?',
    'load_yui_uploader' => 'Load File Uploder Utility?',
    'load_yui_autocomplete' => 'Load Auto-Complete Library?'
);

$LANG_configsubgroups['nexpro'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['nexpro'] = array(
    'fs_libraries' => 'Javascript Libraries',
    'fs_yui'       => 'YUI Libraries',
    'fs_misc'      => 'Misc Settings'
);

// Note: entries 0, 1 and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['nexpro'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3)
);


?>