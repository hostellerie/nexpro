<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $_NEXPRO_DEFAULT;
$_NEXPRO_DEFAULT = array();

//javascript includes
$_NEXPRO_DEFAULT['yui_base_url'] = "http://yui.yahooapis.com/2.7.0/build";
$_NEXPRO_DEFAULT['load_treemenu'] = true;
$_NEXPRO_DEFAULT['load_sarissa'] = true;
$_NEXPRO_DEFAULT['load_calendar'] = true;
$_NEXPRO_DEFAULT['load_fvalidate'] = true;

//yui javascript includes
$_NEXPRO_DEFAULT['load_yuiloader'] = true;      //load and user YUI Loader Utility to load libraries and dependancies
$_NEXPRO_DEFAULT['load_yui'] = false;            //load yui core files
$_NEXPRO_DEFAULT['load_yui_dom'] = false;        //load yui dom
$_NEXPRO_DEFAULT['load_yui_event'] = false;      //load yui event
$_NEXPRO_DEFAULT['load_yui_container'] = false;  //load yui container
$_NEXPRO_DEFAULT['load_yui_calendar'] = false;  //load yui calendar
$_NEXPRO_DEFAULT['load_yui_menu'] = false;       //load yui menu
$_NEXPRO_DEFAULT['load_yui_button'] = false;    //load yui button library
$_NEXPRO_DEFAULT['load_yui_animation'] = false;  //load yui animation
$_NEXPRO_DEFAULT['load_yui_connection'] = false; //load yui connection
$_NEXPRO_DEFAULT['load_yui_dragdrop'] = false;   //load yui drag drop
$_NEXPRO_DEFAULT['load_yui_element'] = false;    //load yui element utility
$_NEXPRO_DEFAULT['load_yui_layout'] = false;    //load yui layout library
$_NEXPRO_DEFAULT['load_yui_layout'] = false;    //load yui layout library
$_NEXPRO_DEFAULT['load_yui_treeview'] = false;    //load yui treeview library
$_NEXPRO_DEFAULT['load_yui_cookie'] = false;    //load yui cookie utility
$_NEXPRO_DEFAULT['load_yui_uploader'] = false;    //load yui file uploader library
$_NEXPRO_DEFAULT['load_yui_logger'] = false;    //load yui logger library
$_NEXPRO_DEFAULT['load_yui_autocomplete'] = false;    //load yui autocomplete library


$_NEXPRO_DEFAULT['fckeditor_upload_dir'] = $_CONF['path_html'] . "images/library/";



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
function plugin_initconfig_nexpro()
{
    global $CONF_NEXPRO, $_NEXPRO_DEFAULT;

    if (is_array($CONF_NEXPRO) && (count($CONF_NEXPRO) > 1)) {
        $_NEXPRO_DEFAULT = array_merge($_NEXPRO_DEFAULT, $CONF_NEXPRO);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexpro')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexpro');
        $c->add('fs_libraries', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexpro');
        $c->add('load_treemenu', $_NEXPRO_DEFAULT['load_treemenu'],
                'select', 0, 0, 1, 10, true, 'nexpro');
        $c->add('load_sarissa', $_NEXPRO_DEFAULT['load_sarissa'],
                'select', 0, 0, 1, 20, true, 'nexpro');
        $c->add('load_calendar', $_NEXPRO_DEFAULT['load_calendar'],
                'select', 0, 0, 1, 30, true, 'nexpro');
        $c->add('load_fvalidate', $_NEXPRO_DEFAULT['load_fvalidate'],
                'select', 0, 0, 1, 40, true, 'nexpro');

        $c->add('fs_yui', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nexpro');

        $c->add('yui_base_url', $_NEXPRO_DEFAULT['yui_base_url'],
                'text', 0, 1, 1, 10, true, 'nexpro');
        $c->add('load_yuiloader', $_NEXPRO_DEFAULT['load_yuiloader'],
                'select', 0, 1, 1, 20, true, 'nexpro');
        $c->add('load_yui', $_NEXPRO_DEFAULT['load_yui'],
                'select', 0, 1, 1, 30, true, 'nexpro');
        $c->add('load_yui_dom', $_NEXPRO_DEFAULT['load_yui_dom'],
                'select', 0, 1, 1, 40, true, 'nexpro');
        $c->add('load_yui_event', $_NEXPRO_DEFAULT['load_yui_event'],
                'select', 0, 1, 1, 50, true, 'nexpro');
        $c->add('load_yui_container', $_NEXPRO_DEFAULT['load_yui_container'],
                'select', 0, 1, 1, 60, true, 'nexpro');
        $c->add('load_yui_calendar', $_NEXPRO_DEFAULT['load_yui_calendar'],
                'select', 0, 1, 1, 70, true, 'nexpro');
        $c->add('load_yui_menu', $_NEXPRO_DEFAULT['load_yui_menu'],
                'select', 0, 1, 0, 80, true, 'nexpro');
        $c->add('load_yui_button', $_NEXPRO_DEFAULT['load_yui_button'],
                'select', 0, 1, 0, 90, true, 'nexpro');
        $c->add('load_yui_animation', $_NEXPRO_DEFAULT['load_yui_animation'],
                'select', 0, 1, 0, 100, true, 'nexpro');
        $c->add('load_yui_connection', $_NEXPRO_DEFAULT['load_yui_connection'],
                'select', 0, 1, 0, 110, true, 'nexpro');
        $c->add('load_yui_dragdrop', $_NEXPRO_DEFAULT['load_yui_dragdrop'],
                'select', 0, 1, 0, 120, true, 'nexpro');
        $c->add('load_yui_autocomplete', $_NEXPRO_DEFAULT['load_yui_autocomplete'],
                'select', 0, 1, 0, 130, true, 'nexpro');
        $c->add('load_yui_element', $_NEXPRO_DEFAULT['load_yui_element'],
                'select', 0, 1, 0, 140, true, 'nexpro');
        $c->add('load_yui_layout', $_NEXPRO_DEFAULT['load_yui_layout'],
                'select', 0, 1, 0, 150, true, 'nexpro');
        $c->add('load_yui_treeview', $_NEXPRO_DEFAULT['load_yui_treeview'],
                'select', 0, 1, 0, 160, true, 'nexpro');
        $c->add('load_yui_cookie', $_NEXPRO_DEFAULT['load_yui_cookie'],
                'select', 0, 1, 0, 170, true, 'nexpro');
        $c->add('load_yui_uploader', $_NEXPRO_DEFAULT['load_yui_uploader'],
                'select', 0, 1, 0, 180, true, 'nexpro');
        $c->add('load_yui_logger', $_NEXPRO_DEFAULT['load_yui_logger'],
                'select', 0, 1, 0, 190, true, 'nexpro');

        $c->add('fs_misc', NULL, 'fieldset', 0, 2, NULL, 0, true, 'nexpro');
        $c->add('fckeditor_upload_dir', $_NEXPRO_DEFAULT['fckeditor_upload_dir'],
                'text', 0, 2, 1, 10, true, 'nexpro');

    }

    return true;
}



?>