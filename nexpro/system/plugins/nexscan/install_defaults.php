<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexScan v1.0.0 - CSS Scan Plugin for nexPro                               |
// | November 19, 2009                                                         |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2008 by the following authors:                              |
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

global $_NEXSCAN_DEFAULT;
$_NEXSCAN_DEFAULT = array();

$_NEXSCAN_DEFAULT['valid_wrap_chars'] = '\'\"\\ ';     //chars that are allowed to wrap the class def... ie class=\"cname\" or class="cname"
$_NEXSCAN_DEFAULT['types_to_scan'] = array ('.php', '.inc', '.thtml', '.html', '.js');     //scan type options

/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_NS if available (e.g. from
* an old config.php), uses $_NEXSCAN_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nexscan()
{
    global $CONF_NS, $_NEXSCAN_DEFAULT;

    if (is_array($CONF_NS) && (count($CONF_NS) > 1)) {
        $_NEXSCAN_DEFAULT = array_merge($_NEXSCAN_DEFAULT, $CONF_NS);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexscan')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexscan');
        $c->add('fs_libraries', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexscan');
        $c->add('valid_wrap_chars', $_NEXSCAN_DEFAULT['valid_wrap_chars'],
                'text', 0, 0, 1, 10, true, 'nexscan');
        $c->add('types_to_scan', $_NEXSCAN_DEFAULT['types_to_scan'],
                '%text', 0, 0, 1, 20, true, 'nexscan');
    }

    return true;
}

?>