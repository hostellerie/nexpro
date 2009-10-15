<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
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

$LANG_LL01 = array (
    'admin_only'        => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'            => 'Plugin',
    'headermenu'        => '',
    'useradmintitle'    => '',
    'adminmenutitle'    => 'List Management',
    'adminmenupanel'    => 'List<br>Management',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'             => 'Plugin Admin',
    'install_header'    => 'Install/Uninstall Plugin',
    'installed'         => 'The Plugin is now installed.<p><i>Enjoy,<br><a href="MAILTO:support@nextide.ca">Nextide Support</a></i>',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'   => 'Plugin installation was successful',
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'enabled'           => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'           => 'Plugin De-Install Warning'

);

$PLG_nexlist_MESSAGE1 = 'Error: Invalid List Name';
$PLG_nexlist_MESSAGE10 = 'You must first install the nexpro plugin before installing any other nexpro related plugins.';
$PLG_nexlist_MESSAGE11 = 'nexList Plugin Upgrade completed - no errors';
$PLG_nexlist_MESSAGE12 = 'nexList Plugin Upgrade failed - check error.log';

$LANG_configsubgroups['nexlist'] = array(
    'sg_main' => 'Main Settings',
);

$LANG_fs['nexlist'] = array(
    'nl_main'      => 'Main Settings',

);


$LANG_configselects['nexlist'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE)
);

$LANG_confignames['nexlist'] = array(
    'debug'                   => 'Debug?',
    'pagesize'                => 'Page Size?'
    
);
?>