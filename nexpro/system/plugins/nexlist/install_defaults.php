<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.2.0 for the nexPro Portal Server                        |
// | Sept. 25, 2009                                                            |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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

global $CONF_LL_DEFAULT;;
$CONF_LL_DEFAULT = array();


$CONF_LL_DEFAULT['debug'] = false;
$CONF_LL_DEFAULT['pagesize'] = 20;      // Page size when viewing a list


/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_NEXPRO if available (e.g. from
* an old config.php), uses $_NEXPRO_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nexlist()
{
    global $CONF_LL_DEFAULT, $CONF_LL;

    if (is_array($CONF_LL) && (count($CONF_LL) > 1)) {
        $CONF_LL_DEFAULT = array_merge($CONF_LL_DEFAULT, $CONF_LL);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexlist')) {

      $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexlist');
      $c->add('nl_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexlist');
      $c->add('debug', $CONF_LL_DEFAULT['debug'],'select',
                0, 0, 0, 10, true, 'nexlist');
      $c->add('pagesize', $CONF_LL_DEFAULT['pagesize'],
              'text', 0, 0, 0, 20, true, 'nexlist');
    }
    return true;
}



?>