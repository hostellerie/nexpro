<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $CONF_NEXMENU_DEFAULT;
$CONF_NEXMENU_DEFAULT = array();


$CONF_NEXMENU['debug'] = false;         // Set to true to enable extra logging to error.log

// +---------------------------------------------------------------------------+
// | Mapping of menu styles to their assigned theme related directory          |
// | This allows us to extend this plugin to support new menu styles           |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU_DEFAULT['menutypes'] = array(
    'CSS'           => array('cssmenu' => 1),
    'Milonic'       => array('milonicmenu' => 1),
    'ProCSS'        => array('procssmenu' => 1)
);

// +------------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['milonicstyles']                                       |
// | Define Milonic styles - mapping a style ID to the style name                 |
// | Styles are defined in the file {themedir}/nexmenu/milonicmenu/menustyles.php |
// | Ensure that you retain the same format for the variable definition           |
// +------------------------------------------------------------------------------+
$CONF_NEXMENU_DEFAULT['milonicstyles'] = array(
    'menuStyle1',
    'menuStyle2',
    'menuStyle3',
    'XPClassicMenuStyle',
    'XPMainStyle',
    'XPMenuStyle',
    'corpMenuStyle',
    'corpSubmenuStyle',
    'macMenuStyle',
    'macSubmenuStyle',
    'tabMenuStyle',
    'tabSubmenuStyle',
    'bwMenuStyle',
    'bwSubmenuStyle'
);

// +----------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['languages']                                         |
// | Define optional languages for which menuitem labels will be defined        |
// | For each option - ensure the language name is identical to the name        |
// | used for the core GL Language file.                                        |
// | The ID used should not be changed or deleted once the option is used       |
// | If the user selected language matches an available label then it is used   |
// | Add/Delete or edit the item definitions to match your site requirements    |
// +----------------------------------------------------------------------------+

$CONF_NEXMENU_DEFAULT['languages'] = array ('french_canada_utf-8','german_utf-8','japanese','chinese_simplified_utf-8','italian','spanish');


// +----------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['sp_labelonly']                                      |
// | If true only create menuitems for staticpages that have "add to menu"      |
// | flag set in StaticPage definition                                          |
// +----------------------------------------------------------------------------+
$CONF_NEXMENU_DEFAULT['sp_labelonly'] = true;


// +---------------------------------------------------------------------------+
// | Setting $CONF_NEXMENU['links_maxtoplevels']                               |
// | If greater then 0, will limit the number of link menu categories to show  |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU_DEFAULT['links_maxtoplevels'] = 5;


// +---------------------------------------------------------------------------+
// | Default Milonic menu properties for the header and block menus.           |
// | Reference: http://www.milonic.com/menuproperties.php.                     |
// | Optional Menu properties are defined in the online admin config screen.   |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU_DEFAULT['headermenu_default_styles'] = 'orientation="horizontal";position="relative";alwaysvisible=1;';
$CONF_NEXMENU_DEFAULT['blockmenu_default_styles'] = 'position="relative";alwaysvisible=1;';


/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_NEXMENU if available (e.g. from
* an old config.php), uses $CONF_NEXMENU_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nexmenu()
{
    global $CONF_NEXMENU, $CONF_NEXMENU_DEFAULT;

    if (is_array($CONF_FE) && (count($CONF_FE) > 1)) {
        $CONF_NEXMENU_DEFAULT = array_merge($CONF_NEXMENU_DEFAULT, $CONF_NEXMENU);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexmenu')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexmenu');
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexmenu');
        $c->add('debug', $CONF_NEXMENU_DEFAULT['debug'],'select', 0, 0, 0, 5, true, 'nexmenu');
        $c->add('menutypes',$CONF_NEXMENU_DEFAULT['menutypes'],'**placeholder',0,0,NULL,10,TRUE,'nexmenu');
        $c->add('milonicstyles',$CONF_NEXMENU_DEFAULT['milonicstyles'],'%text',0,0,NULL,20,TRUE,'nexmenu');
        $c->add('languages',$CONF_NEXMENU_DEFAULT['languages'],'%text',0,0,NULL,30,TRUE,'nexmenu');
        $c->add('sp_labelonly',$CONF_NEXMENU_DEFAULT['sp_labelonly'],'select',0,0,0,40,true,'nexmenu');
        $c->add('links_maxtoplevels',$CONF_NEXMENU_DEFAULT['links_maxtoplevels'],'text',0,0,NULL,50,true,'nexmenu');
        $c->add('headermenu_default_styles',$CONF_NEXMENU_DEFAULT['headermenu_default_styles'],'text',0,0,NULL,70,true,'nexmenu');
        $c->add('blockmenu_default_styles',$CONF_NEXMENU_DEFAULT['blockmenu_default_styles'],'text',0,0,NULL,80,true,'nexmenu');
    }

    return true;
}



?>