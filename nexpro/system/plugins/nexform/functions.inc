<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.2 for the nexPro Portal Server                          |
// | Sept 14, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

$langfile = $_CONF['path'] . 'plugins/nexform/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexform/language/english.php');
}

include_once ($_CONF['path'] . 'plugins/nexform/autouninstall.php');

/*
* Check and see if we need to load the plugin configuration
*/
if (!isset($CONF_FE['post_url'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $nexform_config = config::get_instance();
    $CONF_FE = $nexform_config->get_config('nexform');
}

require_once ($_CONF['path'] . 'plugins/nexform/nexform.php');
require_once ($_CONF['path'] . 'plugins/nexform/library.php');

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexform()
{
    global $_TABLES, $_CONF,$LANG_FE01;

    if (SEC_hasRights('nexform.edit')) {
    $numrecords = DB_getITEM($_TABLES['nxform_definitions'],"count(*)");
    return array($LANG_FE01['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexform/index.php', $numrecords);
    }

}

function plugin_cclabel_nexform()
{
    global $_CONF, $LANG_FE01;
    if (SEC_hasRights('nexform.edit')) {
        return array($LANG_FE01['adminmenupanel'],"{$_CONF['site_admin_url']}/plugins/nexform/index.php",plugin_geticon_nexform());
    }
}


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexform()
{
    global $_CONF;

    return $_CONF['layout_url'] .'/nexform/images/admin/nexform.gif';
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexform()
{
    global $_CONF, $CONF_FE;

    $code = '';
    $code = "\n\n<!-- Begin nexform  -->\n";
    $code .= LB . '<script language=JavaScript src="' . $CONF_FE['public_url'] . '/include/nexform.js" type="text/javascript"></script>';

    if ($CONF_FE['load_editor']) {
        $code .= '<link rel="stylesheet" type="text/css" href="'.$_CONF['site_url'] .'/fckeditor/css/fck_editorarea.css">' .LB;
        $code .= LB . '<script type="text/javascript" src="' . $_CONF['site_url'] . '/fckeditor/fckeditor.js" type="text/javascript"></script>';
    }

    $code .= "\n<script type=\"text/javascript\" src=\"{$CONF_FE['public_url']}/include/ajax_deletemfile_support.js\"></script>";
    $code .= "\n<!-- End nexform -->\n\n";
    return $code;
}

/**
* Geeklog is asking us to provide any new items that show up in the type drop-down
* on search.php.  Let's let users search the Filelistings in the Filemgmt Plugin
*
*/

function plugin_searchtypes_nexform()
{
    global $LANG_FE01;

    $tmp['nexfile'] = $LANG_FE01['searchlabel'];
    return $tmp;
}


/**
* This searches for faqs matching the user query and returns an object
* back to search.php where it will be formated and printed.
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for (not used)
* @param    date    $dateend    End date to get results for (not used)
* @param    string  $topic      The topic they were searching in (not used)
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author (not used)
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/
function plugin_dopluginsearch_nexform($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_FE01;
}

/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nexform($plugin,$state) {
    global $_TABLES;

    // The nexform plugin is dependant on the plugin nexpro being installed and enabled
    if ($plugin == 'nexpro') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nexform'");
        }
    }
}



/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexform() {
    global $CONF_FE;
    return $CONF_FE['version'];
}




function plugin_upgrade_nexform() {
    global $_TABLES,$CONF_FE;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexform'");

    switch ($curversion) {
        case '2.0.0':
            if (nexform_upgrade_201() == 0 )  {
                DB_query("ALTER TABLE {$_TABLES['nxform_fields']} ADD `label_padding` SMALLINT( 1 ) NULL AFTER `col_padding`");
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.0.1', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexform' LIMIT 1");
                if (nexform_upgrade_22() == 0) {
                    DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.2.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexform' LIMIT 1");
                    return true;
                } else {
                    return 1;
                }
            }
            break;
        case '2.0.1':
            if (nexform_upgrade_22() == 0) {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.2.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexform' LIMIT 1");
                return true;
            } else {
                return 1;
            }
            break;
        case '2.1.0':
            if (nexform_upgrade_22() == 0) {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.2.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexform' LIMIT 1");
                return true;
            } else {
                return 1;
            }
            break;

    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexform'") == $CONF_FE['version']) {
        return 11;
    } else {
        return 12;
    }
}



function plugin_getdocumentationurl_nexform($args) {
    return 'http://www.nextide.ca/dokuwiki/doku.php/core_applications_nexform#configuration';
}


function plugin_autotags_nexform($op,$content='',$tag='') {
    global $_CONF,$_TABLES;

    if ($op == 'tagname' ) {
        return 'form';
    } elseif ($op == 'parse') {
       if (DB_count($_TABLES['nxform_definitions'],'id',$tag['parm1']) == 1) {
           // Set the option to not show the print links with the form
           $parms = array(
                'noprint' => true
           );
           $formHTML = nexform_showform($tag['parm1'],0,'view',$parms);
           $retval = str_replace($tag['tagstr'],$formHTML,$content);
           return $retval;
        } else {
           return $content;
        }
    }
}

// Example of a custom function that could be used to show a form in a block
function phpblock_sampleform() {
    return nexform_showform(1);
}


// function to test permissions on directories
function plugin_chkPermissions_nexform(&$template) {
    global $CONF_FE;

    $display .= '<p>Testing <b>nexForm data</b> directory ' . $CONF_FE['uploadpath'] . ' ...<br>' . LB;
    if (!$file = @fopen ($CONF_FE['uploadpath'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_FE['uploadpath'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_FE['uploadpath'] . 'test.txt');
    }

    $display .= '<p>Testing <b>nexForm export</b> directory ' . $CONF_FE['export_dir'] . ' ...<br>' . LB;
    if (!$file = @fopen ($CONF_FE['export_dir'] . 'test.txt', 'w')) {
        $display .= '<font color="#ff0000">Could not write to <b>' . $CONF_FE['export_dir'] . '</b>.</font><br>Please make sure this directory exists and is set to <b>chmod 777</b>.<br>' . LB;
        $display .= 'Current permissions for <b>nexForm export</b>: ' . sprintf ("%3o", @fileperms ($CONF_FE['export_dir']) & 0777);
        $failed++;
    } else {
        fclose ($file);
        unlink ($CONF_FE['export_dir'] . 'test.txt');
        $display .= '<b>nexForm export</b> directory is okay.' . LB;
        $successful++;
    }

    return $display;
}


?>