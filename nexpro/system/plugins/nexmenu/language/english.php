<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | Sept 16, 2009                                                             |
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

$LANG_NEXMENU00 = array (
    'admin_only'        => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'            => 'Plugin',
    'headermenu'        => '',
    'useradmintitle'    => '',
    'adminmenutitle'    => 'Menus',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'             => 'Plugin Admin',
    'install_header'    => 'Install/Uninstall Plugin',
    'installed'         => 'The Plugin is now installed.<p><i>Enjoy,<br><a href="MAILTO:support@portalparts.com">Portalparts Support</a></i>',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'   => 'nexMenu Plugin Installation Successful',
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'enabled'           => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'           => 'nexMenu Plugin De-Install Warning'
);



$LANG_NEXMENU01 = array (
    'LANG_CANCEL'           => 'Cancel',
    'LANG_UPDATE'           => 'Update Record',
    'LANG_ADD'              => 'Add Record',
    'LANG_OPTIONS'          => 'Options',
    'LANG_HEADING1'         => 'Menu Structure',
    'LANG_HEADING2'         => 'Menu Item Detail',
    'LANG_HELPMSG1'         => 'Choose "Top Level Menu" to make this a main menu item. It can then be made a submenu for other menu items to be assigned to it. Submenu\'s do not have a URL assigned.',
    'LANG_HELPMSG2'         => 'Choose "Top Level Menu" to make this a main menu item. If this is a Submenu, it will not have a URL assigned.',
    'LANG_MenuItemAdmin'    => 'Menu Item Admin',
    'LANG_ParentMenu'       => 'Parent Menu',
    'LANG_Label'            => 'Label',
    'LANG_ORDER'            => 'Order',
    'LANG_Enabled'          => 'Enabled',
    'LANG_Submenu'          => 'Submenu',
    'LANG_URLITEM'          => 'Menuitem Definition',
    'LANG_EditRecord'       => 'Edit Record',
    'LANG_DeleteRecord'     => 'Delete Record',
    'LANG_DELCONFIRM'       => 'Please confirm that you want to delete this record',
    'LANG_TopLevelMenu'     => 'Top Level Menu',
    'LANG_SubNoneReq'       => ' Submenu -- none required',
    'LANG_ACCESS'           => 'Access Rights',
    'LANG_MoveUp'           => 'Move Item UP',
    'LANG_MoveDn'           => 'Move Item Down',
    'LANG_Alternatelabel'   => 'Alternative Labels',
    'LANG_Languages'        => 'Languages',
    'ANONYMOUS'             => 'Anonymous',
    'LANG_IMAGE'            => 'Menu Item Image',
    'LANG_ImageHelp'        => 'Optional, if you want an image to appear in the left-handside of the menuitem<br>Enter a full URL to the image or just the image file name is located in the sites glmenu/menuimages folder',
    'LANG_EditHelp'         => 'Click on Menu Item to see item details and editing options'
);

/* Definitions for Menu Item Type Options */
$LANG_NEXMENU02 = array (
    0       => 'Select Type',
    1       => 'URL - Same Window',
    2       => 'URL - New Window',
    3       => 'SubMenu',
    4       => 'Core GL Menu',
    5       => 'PHP Function'
);

/* Defintions for Core Menu Type Options */
$LANG_NEXMENU03 = array (
    0       => 'Select Core Menu',
    1       => 'User Menu',
    2       => 'Admin Menu',
    3       => 'Topics Menu',
    4       => 'Staticpages Menu',
    5       => 'pluginmenu',
    6       => 'linksmenu'
);


/* Admin Navbar Setup */
$LANG_NEXMENU04 = array (
    1   => 'Header Menu Listing',
    2   => 'Block Menu Listing',
    3   => 'Add new entry',
    4   => 'Admin Settings',
    5   => 'Edit Menuitem',
    6   => 'Configuration'

);


/* Language used in the configuration screen */
$LANG_NEXMENU05 = array (
    0   => 'Menu Mode',
    1   => 'Multi-Language Labels',
    2   => 'New Window Options',
    3   => 'Reference',
    4   => 'Enabled',
    5   => 'Disabled',
    6   => 'Menu Administration',
    7   => 'Milonic Menu Styles to use',
    8   => 'Block Menu Style',
    9   => 'Block Sub-Menu Style',
    10  => 'Block Menu Properties',
    11  => 'Separate menu properties with a semicolan',
    12  => 'Header Menu Style',
    13  => 'Header Sub-Menu Style',
    14  => 'Header Menu Properties',
    15  => 'CSS Menu Color Pallet',
    16  => 'Property',
    17  => 'Primary Color',
    18  => 'On-Hover',
    19  => 'Header Background',
    20  => 'Header Text',
    21  => 'Block Background',
    22  => 'Block Text',
    23  => 'Over-write the menu.css file',
    24  => 'Yes',
    25  => 'No',
    26  => 'Header Menu',
    27  => 'Block Menu',
    28  => 'Header SubMenu',
    29  => 'Submenu Background',
    30  => 'Submenu Text',
    31  => 'Block SubMenu',
    32  => 'Click to use color picker'
);

$PLG_nexmenu_MESSAGE1  = 'nexMenu Plugin Upgrade failed - check error.log';
$PLG_nexmenu_MESSAGE10 = 'You must first install the nexpro plugin before installing any other nexpro related plugins.';
$PLG_nexmenu_MESSAGE11 = 'nexMenu Plugin Upgrade completed - no errors';
$PLG_nexmenu_MESSAGE12 = 'nexMenu Plugin Upgrade failed - check error.log';

// Localization of the Admin Configuration UI
$LANG_configsections['nexmenu'] = array(
    'label' => 'Nexmenu',
    'title' => 'Nexmenu Configuration'
);

$LANG_confignames['nexmenu'] = array(
    'debug'                        => 'Debug - set true for extra error.log detail',
    'menutypes'                    => 'Available Core Menu Styles',
    'milonicstyles'                => 'Avilable Milonic Menu Styles',
    'languages'                    => 'Languages for which menuitem labels can be defined',
    'sp_labelonly'                 => 'Create menuitems for staticpages with "add to menu" ',
    'links_maxtoplevels'           => 'Limit for the number of link menu categories to show',
    'restricted_topics'            => 'Directory for form export data',
    'headermenu_default_styles'    => 'Default Milonic menu properties for the header menu',
    'blockmenu_default_styles'     => 'Default Milonic menu properties for the block menu'
);

$LANG_configsubgroups['nexmenu'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['nexmenu'] = array(
    'fs_main'           => 'Main Settings'
);

// Note: entries 0, 1 and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['nexmenu'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
    20 => array('Public Access' => 2, 'Site Members' => 13)
);


?>