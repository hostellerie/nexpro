<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.1.0 for the nexPro Portal Server                         |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
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

$langfile = $_CONF['path'] . 'plugins/nexpro/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexpro/language/english.php');
}

require_once ($_CONF['path_system'] . 'classes/sanitize.class.php');
require_once ($_CONF['path_system'] . 'lib-portalparts.php');
require_once ($_CONF['path_html'] . 'jscalendar/calendar.php');

// Used for debugging to log messages to the Firebug addon for Firefox from PHP
require_once ($_CONF['path_system'] . 'nexpro/FirePHPCore/FirePHP.class.php');
// Load common nextide/nexpro plugin functions
require_once ($_CONF['path'] . 'plugins/nexpro/lib-nextide.php');

require_once ($_CONF['path'] . 'plugins/nexpro/nexpro.php');
require_once ($_CONF['path'] . 'plugins/nexpro/autouninstall.php');
/**
* Returns the needed link to the menu csss- which will be included in the header
*/
function plugin_getHeaderCode_nexpro() {
    global $_CONF, $CONF_NEXPRO;

    $code = "\n\n" .'<!-- Begin Nextide nexpro -->' ."\n";
    $code .= '<link rel="stylesheet" type="text/css" href="'.$CONF_NEXPRO['yui_base_url'].'/container/assets/container.css">' . LB;
    $code .= '<link type="text/css" rel="stylesheet" href="'.$CONF_NEXPRO['yui_base_url'].'/menu/assets/menu.css">' . LB;

    $code .= '<script src="' . $_CONF['site_url'] . '/javascript/nexpro.js" language="JavaScript" type="text/javascript"></script>' . LB;
    if ($CONF_NEXPRO['load_treemenu']) {
        $code .= '<script src="' . $_CONF['site_url'] . '/javascript/TreeMenu.js" language="JavaScript" type="text/javascript"></script>' . LB;
    }
    if ($CONF_NEXPRO['load_yuiloader']) {
        $code .= "\n\n" .'<!-- Use the YUI Library Loader Utility -->' ."\n";
        $code .= LB . '<script type="text/javascript">' . LB;
        $code .= 'var useYuiLoader = true; ' . LB;
        $code .= '</script>' . LB;
        $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/yuiloader/yuiloader.js"></script>' . LB;
    } else {
        $code .= LB . '<script type="text/javascript">' . LB;
        $code .= 'var useYuiLoader = false; ' . LB;
        $code .= '</script>' . LB;
    }

    $code .= '<script type="text/javascript">' . LB;
    $code .= 'var YUIBaseURL  = "' . $CONF_NEXPRO['yui_base_url'] . '";' . LB;
    $code .= '</script>' . LB;

    if ($CONF_NEXPRO['load_yui']) {
        $code .= "\n<!-- Load Yahoo UI Javascript Libraries -->\n";
        $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/fonts/fonts.css">' .LB;
        $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/yahoo/yahoo.js"></script>' . LB;

        if ($CONF_NEXPRO['load_yui_dom']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/dom/dom.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_event']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/event/event.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_container']) {
            $code .= '<link rel="stylesheet" type="text/css" href="' . $CONF_NEXPRO['yui_base_url'] . '/container/assets/container.css">' . LB;
            $code .= '<link rel="stylesheet" type="text/css" href="' . $CONF_NEXPRO['yui_base_url'] . '/container/assets/skins/sam/container.css">' . LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/container/container.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_calendar']) {
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/calendar/assets/calendar.css">' . LB;
            $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/nexYUICal.js"></script>'. LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/calendar/calendar.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_menu']) {
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/menu/assets/menu.css">' .LB;
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/menu/assets/skins/sam/menu.css">' .LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/menu/menu.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_animation']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/animation/animation.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_connection']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/connection/connection.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_dragdrop']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/dragdrop/dragdrop.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_element']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/element/element-min.js"></script>' . LB;
        }

        if ($CONF_NEXPRO['load_yui_layout']) {
            //$code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/reset-fonts-grids/reset-fonts-grids.css">' .LB;
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/resize/assets/skins/sam/resize.css">' .LB;
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/layout/assets/skins/sam/layout.css">' .LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/resize/resize-min.js"></script>' . LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/layout/layout-min.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_button']) {
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/button/assets/skins/sam/button.css">' .LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/button/button-min.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_treeview']) {
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/treeview/assets/skins/sam/treeview.css">' .LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/treeview/treeview-min.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_cookie']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/cookie/cookie-min.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_uploader']) {
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/uploader/uploader.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_logger']) {
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/logger/assets/skins/sam/logger.css">' .LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/logger/logger-min.js"></script>' . LB;
        }
        if ($CONF_NEXPRO['load_yui_autocomplete']) {
            $code .= '<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/autocomplete/assets/skins/sam/autocomplete.css">' .LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/datasource/datasource-min.js"></script>' . LB;
            $code .= '<script type="text/javascript" src="' . $CONF_NEXPRO['yui_base_url'] . '/autocomplete/autocomplete-min.js"></script>' . LB;
        }

        $code .= "<!-- End of Yahoo UI libraries -->\n";
    }

    if ($CONF_NEXPRO['load_sarissa']) {
        $code .= "\n<!-- Load Sarissa Library for AJAX Support  -->\n";
        $code .= '<script language=JavaScript src="' . $_CONF['site_url'] . '/javascript/sarissa.js" type="text/javascript"></script>' . LB;
    }

    if ($CONF_NEXPRO['load_calendar']) {
        $code .= "\n<!-- Begin PopUp Calendar Javascript includes -->\n";
        $calendar = new DHTML_Calendar($_CONF['site_url'] . '/jscalendar/', 'en', 'calendar-system', false);
        $code .= $calendar->load_files();
        $code .= "\n". '<!-- End PopUp Calendar Javascript includes -->' ."\n";
    }

    if ($CONF_NEXPRO['load_fvalidate']) {
        $code .= "\n" .'<!-- Begin fValifdate Javascript includes for nexform Plugin -->';
        $code .= "\n" .'<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/jsval.js"></script>';
        $code .= "\n". '<!-- End fValifdate Javascript includes for nexform Plugin -->' ."\n";
    }

    $code .= '<!-- End Nextide Nexpro -->' ."\n\n";
    return $code;
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexpro() {
    global $CONF_NEXPRO;
    return $CONF_NEXPRO['version'];
}

function plugin_templatesetvars_nexpro($type,&$template) {
    global $_TABLES,$_CONF,$CONF_NEXMENU;

    if ($type == 'header') {
        if ($_COOKIE['leftblocksmode'] == 'none') {
            $template->set_var( 'showhide', '<img src="'.$_CONF['layout_url'].'/images/showleftblocks.gif" onClick="toggleleftblocks(this);" ALT="Click to show Left Block" TITLE="Click to show Left Blocks" border="0">' );
        } else {
            $template->set_var( 'showhide', '<img src="'.$_CONF['layout_url'].'/images/hideleftblocks.gif" onClick="toggleleftblocks(this);" ALT="Click to hide Left Block" TITLE="Click to hide Left Blocks" border="0">' );
        }
        $template->set_var( 'leftblockmode', $_COOKIE['leftblocksmode']);

        if (!isset($CONF_NEXMENU)) $template->set_var('nexmenu','');

        if (file_exists("{$_CONF['path_layout']}/header.js")) {
            $code = '<script type="text/javascript">' . LB;
            $code .= '  var layout_url = "'. $_CONF['layout_url'] .'";' . LB;
            $code .= '</script>' .LB;
            $code .=  '<script type="text/javascript" src="'.$_CONF['layout_url'] .'/header.js"></script>';
            $template->set_var('header_javascript',$code);
        } else {
            $template->set_var('header_javascript','');
        }

    }
}



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
function plugin_autouninstall_nexprox ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('tagwords','tagworditems','tagwordmetrics'),
        /* give the full name of the group, as in the db */
        'groups' => array(),
        /* give the full name of the feature, as in the db */
        'features' => array(),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars'=> array()
    );
    return $out;
}


function plugin_upgrade_nexpro() {
    global $_CONF,$_TABLES,$CONF_NEXPRO,$_NEXPRO_DEFAULT;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexpro'");

    switch ($curversion) {
        case "2.0.0":
            if (nexpro_upgrade_201() == 0 )  {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.0.1', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'nexpro' LIMIT 1");
            }
            break;

        case '2.0.1':
            require_once $_CONF['path_system'] . 'classes/config.class.php';
            $plugin_path = $_CONF['path'] . 'plugins/nexpro/';
            require_once $plugin_path . 'install_defaults.php';
            if (file_exists($plugin_path . 'config.php')) {
                global $_DB_table_prefix, $_NEXPRO_CONF;
                @include_once $plugin_path . 'config.php';
            }
            if (plugin_initconfig_nexpro()) {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.1.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexpro' LIMIT 1");
                COM_errorlog("nexPro updated successfully.");
                return true;
            } else {
                echo 'There was an error upgrading the plugin';
                COM_errorlog("nexPro was not updated to verion 2.1.0.  Please check any errors before retrying.");
                return false;
            }

            break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexpro'") == $CONF_NEXPRO['version']) {
        COM_errorlog("nexPro updated successfully.");
        return true;
    } else {
        COM_errorlog("nexPro was not updated to verion 2.1.0.  Please check any errors before retrying.");
        return false;
    }
}

function plugin_chkPermissions_nexpro(&$template) {
    global $CONF_NEXPRO;

    if (!$file = @fopen ($CONF_NEXPRO['fckeditor_upload_dir'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_NEXPRO['fckeditor_upload_dir'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_NEXPRO['fckeditor_upload_dir'] . 'test.txt');
    }
}

/**
* Called on User Login to Add Any Plugin Specific User Details
*/
function plugin_user_login_nexpro($uid) {
    global $_CONF, $CONF_NEXPRO, $_TABLES;
    $username = DB_getItem($_TABLES['users'], 'username', "uid=$uid");

    //check to see if this is an ldap user, if so, populate the fullname
    $authtype = DB_getItem($_TABLES['users'], 'remoteservice', "uid=$uid");
    if ($authtype == 'LDAP') {
        //get the user's fullname from the ldap

        //connect to ldap server
        $ds=ldap_connect($CONF_NEXPRO['ldap_server']);

        if ($ds) { //if we connected
            if ($CONF_NEXPRO['ldap_type'] == 'Active Directory') {
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION,3);
                ldap_set_option($ds, LDAP_OPT_REFERRALS,0);
            }

            foreach ($CONF_NEXPRO['ldap_ous'] as $ou) {
                $bind_string = sprintf($CONF_NEXPRO['ldap_bind_string'], $CONF_NEXPRO['ldap_username'], $ou);

                // bind with appropriate dn to give update access
                $r=@ldap_bind($ds, $bind_string, $CONF_NEXPRO['ldap_password']);
                if ($r!==FALSE) {
                    //authenticated successfully
                    //now search through the ldap for an email address
                    $search_string = sprintf($CONF_NEXPRO['ldap_search_string'], $username);
                    foreach ($CONF_NEXPRO['ldap_ous'] as $lou) {
                        $organization_string = sprintf($CONF_NEXPRO['ldap_organization_string'], $lou);
                        $sr=ldap_search($ds, $organization_string, $search_string);
                        $count = ldap_count_entries($ds, $sr);

                        if ($count > 0) {
                            $info = ldap_get_entries($ds, $sr);
                            $fullname = $info[0][$CONF_NEXPRO['fullname_parm']][0];
                            ldap_close($ds);

                            DB_query("UPDATE {$_TABLES['users']} SET fullname='$fullname' WHERE uid=$uid");
                            return;
                        }
                    }
                    //if we reach this point in the code that means we weren't able to find a fullname
                    return;
                }
            } //end foreach $CONF_NEXPRO['ldap_ous']
            //if we reach this point in the code that means we weren't able to bind
            ldap_close($ds);

            return;
        }
        else {
            return;
        }//end if $ds
    }
}

function plugin_user_create_nexpro($uid){
    global $_CONF, $_TABLES,$CONF_NEXPRO;
    if($CONF_NEXPRO['enable_remote_service_set']){
            //we've been told to set the remote service.
            //only do this if this is a NEW user by determining if the last logged in date is not set
            $lastlogin=DB_getItem($_TABLES['userinfo'],"lastlogin","uid={$uid}");
            $username=DB_getItem($_TABLES['users'],"username","uid={$uid}");
            if(($lastlogin==0 || $lastlogin=='') && $CONF_NEXPRO['enable_remote_service_set']){//new!  do the ldap force here
                $sql ="UPDATE {$_TABLES['users']} set status=3,remoteusername='{$username}',remoteservice='{$CONF_NEXPRO['new_user_remote_service']}' where uid={$uid}";
                DB_query($sql);
                $remUsers = DB_getItem ($_TABLES['groups'], 'grp_id',"grp_name = 'Remote Users'");
                $sql = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid) VALUES ($remUsers, $uid)";
                DB_query ($sql);
            }
        }

}
function plugin_user_changed_nexpro ($uid) {
    global $_CONF, $_TABLES,$CONF_NEXPRO;

    //first check to see if this is an ldap user
    $authtype = DB_getItem($_TABLES['users'], 'remoteservice', "uid=$uid");
    if ($authtype == 'LDAP') {
        require_once ($_CONF['path_system'] . 'classes/authentication/LDAP.auth.class.php');
        if ($_POST['passwd'] == $_POST['passwd_conf']) {
            $md5passwd = md5($_POST['passwd']);
            $username = DB_getItem($_TABLES['users'], 'username', "uid=$uid AND passwd='$md5passwd'");

            if ($username != '') {  //if username IS '' then the old password does not match the current password
                $authclass = new LDAP();
                $authclass->set_password($username, $_POST['passwd'], $_POST['old_passwd']);
            }
        }
    }else{  //LDAP is not set...
        //ok, we need to see what the system settings are first
        if($CONF_NEXPRO['enable_remote_service_set']){

            //we've been told to set the remote service.
            //only do this if this is a NEW user by determining if the last logged in date is not set
            $lastlogin=DB_getItem($_TABLES['userinfo'],"lastlogin","uid={$uid}");
            if(($lastlogin==0 || $lastlogin=='') && $CONF_NEXPRO['enable_remote_service_set']){//new!  do the ldap force here
                $sql ="UPDATE {$_TABLES['users']} set status=3,remoteusername='{$username}',remoteservice='{$CONF_NEXPRO['new_user_remote_service']}' where uid={$uid}";
                DB_query($sql);
                $remUsers = DB_getItem ($_TABLES['groups'], 'grp_id',"grp_name = 'Remote Users'");
                $sql = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid) VALUES ($remUsers, $uid)";
                DB_query ($sql);
            }
        }



    }
    return;
}

?>