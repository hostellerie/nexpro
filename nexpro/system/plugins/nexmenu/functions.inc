<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | Sept 16, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
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

$langfile = $_CONF['path'] . 'plugins/nexmenu/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexmenu/language/english.php');
}

/*
* Check and see if we need to load the plugin configuration
*/

if (!isset($CONF_NEXMENU['milonicstyles'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $nexmenu_config = config::get_instance();
    $CONF_NEXMENU = $nexmenu_config->get_config('nexmenu');
}

require_once ($_CONF['path'] . 'plugins/nexmenu/nexmenu.class.php');
require_once ($_CONF['path'] . 'plugins/nexmenu/autouninstall.php');

// Call the function to initialize some additional CONF variables for glMenu
$CONF_NEXMENU['installed_version'] = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexmenu'");
if (function_exists(plugin_getHeaderCode_nexmenu)) {
    if (isset($_TABLES)) {
        if ($CONF_NEXMENU['installed_version'] >= 3) {
            if (isset($_USER['theme']) and DB_count($_TABLES['nexmenu_config'],'theme',$_USER['theme'])) {
                $query = DB_query("SELECT header_style,block_style FROM {$_TABLES['nexmenu_config']} WHERE theme = '{$_USER['theme']}'");
                list ($headerStyle,$blockStyle) = DB_fetchArray($query);
                $headermenu =  & nexmenu::factory('header',$_USER['theme']);
                $blockmenu =  & nexmenu::factory('block',$_USER['theme']);
            } else {
                $query = DB_query("SELECT header_style,block_style FROM {$_TABLES['nexmenu_config']} WHERE theme = 'default'");
                list ($headerStyle,$blockStyle) = DB_fetchArray($query);
                $headermenu =  & nexmenu::factory('header');
                $blockmenu =  & nexmenu::factory('block');
            }
            $CONF_NEXMENU['headerstyle'] = $headerStyle;
            $CONF_NEXMENU['blockstyle'] = $blockStyle;

        }
    }
}

require_once ($_CONF['path_system'] . 'lib-portalparts.php');

$CONF_NEXMENU['charset'] = COM_getCharset();


/**
* This will put an option for forum admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_nexmenu()
{
    global $_CONF,$LANG_NEXMENU00;
    if (SEC_hasRights('nexmenu.edit')) {
        $adminLink = "{$_CONF['site_url']}/admin/plugins/nexmenu/index.php";
        $adminImage = plugin_geticon_nexmenu();
        return array($LANG_NEXMENU00['adminmenutitle'],$adminLink,$adminImage);
    }
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexmenu()
{
    global $_TABLES, $_CONF,$LANG_NEXMENU00;

    if (SEC_hasRights('nexmenu.edit')) {
        return array($LANG_NEXMENU00['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexmenu/index.php');
    }

}


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexmenu()
{
    global $_CONF;
    return $_CONF['layout_url'] .'/nexmenu/images/admin/nexmenu.gif';
}

function plugin_templatesetvars_nexmenu($type,&$template) {
    global $CONF_NEXMENU;

    if ($type == 'header' AND $CONF_NEXMENU['installed_version'] >= 3) {
        $template->set_var( 'nexmenu', nexmenu_siteheader() );
    }
}

/**
* Returns the needed link to the menu csss- which will be included in the header
*/
function plugin_getHeaderCode_nexmenu()
{
    global $_CONF,$CONF_NEXMENU,$blockmenu,$headermenu;
    $code = '';
    if ($CONF_NEXMENU['installed_version'] >= 3) {
        $code = "\n\n" .'<!-- Begin nexmenu from www.portalparts.com Javascript and CSS includes -->' ."\n";
        $code .= $headermenu->getHeaderCode();
        $code .= $blockmenu->getHeaderCode();
        $code .= '<!-- End nexmenu includes -->' ."\n\n";
    }
    return $code;
}

/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nexmenu($plugin,$state) {
    global $_TABLES;

    // The nexmenu plugin is dependant on the plugin nexpro being installed and enabled
    if ($plugin == 'nexpro') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nexmenu'");
        }
    }
}


/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexmenu()
{
    global $CONF_NEXMENU;
    return $CONF_NEXMENU['version'];
}

/**
* Called by the plugin Editor to run the SQL Update for a plugin update
*/
function plugin_upgrade_nexmenu()
{
    global $_TABLES,$CONF_NEXMENU;

    include ('upgrade.inc');        // Include the upgrade functions

    if ($CONF_NEXMENU['installed_version'] < "2.5") {
        if (nexmenu_upgrade_25() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.5' WHERE `pi_name` = 'nexmenu' LIMIT 1");
            return true;
        } else {
            return 1;
        }
    } elseif ($CONF_NEXMENU['installed_version'] == "2.5") {
        DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.6' WHERE `pi_name` = 'nexmenu' LIMIT 1");
        return true;
    } elseif ($CONF_NEXMENU['installed_version'] == "2.5.2") {
        DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.2' WHERE `pi_name` = 'nexmenu' LIMIT 1");
        return true;
    }elseif ($CONF_NEXMENU['installed_version'] == "2.6") {
        if (nexmenu_upgrade_26() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.1', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexmenu' LIMIT 1");
            if (upgrade_32() == 0) {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.2', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexmenu' LIMIT 1");
                return true;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    } elseif ($CONF_NEXMENU['installed_version'] == "3.0") {
        if (nexmenu_upgrade_32() == 0) {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.2', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexmenu' LIMIT 1");
            return true;
        } else {
            return 1;
        }
        return true;
    } elseif ($CONF_NEXMENU['installed_version'] == "3.1") {
        if (nexmenu_upgrade_32() == 0) {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.2', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexmenu' LIMIT 1");
            return true;
        } else {
            return 1;
        }
    } else {
        return 1;
    }
}






function phpblock_nexmenu()
{
    global $blockmenu,$CONF_NEXMENU;

    if ($CONF_NEXMENU['installed_version'] >= 3) {
        $blockmenu->setMenuType('block');
        $blockmenu->initMenuItems();
        return $blockmenu->renderMenu();
    }

}

function nexmenu_siteheader()
{
    global $headermenu;

    $headermenu->setMenuType('header');
    $headermenu->initMenuItems();
    return $headermenu->renderMenu();
}


function phpblock_gluserlogin()
{
    global $_USER,$_CONF, $_TABLES, $LANG01,$_BLOCK_TEMPLATE;

    if ($_USER['uid'] < 2) {
        $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/blockheader.thtml,nexmenu/blockfooter.thtml';

        $t = new Template( $_CONF['path_layout'] );
        $t->set_file( 'form', 'loginform.thtml' );
        $t->set_var( 'site_url', $_CONF['site_url'] );
        $t->set_var( 'layout_url', $_CONF['layout_url'] );
        $t->set_var( 'lang_username', $LANG01[21] );
        $t->set_var( 'lang_password', $LANG01[57] );
        $t->set_var( 'lang_login', $LANG01[58] );
        $t->set_var( 'lang_signup', $LANG01[59] );
        $t->set_var('lang_forgetpassword',$LANG01[119]);
        $t->set_var('services','');
        $t->parse( 'output', 'form' );

        $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
        return $t->finish ($t->get_var('output'));
    }
}



function phpblock_glusermenu()
{
    global $_USER,$_CONF,$LANG01,$_BLOCK_TEMPLATE,$blockmenu;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
    if ($blockmenu->_menustyle == 'milonic') {
        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/milonicmenu/blockheader-blockmenu.thtml,nexmenu/milonicmenu/blockfooter-blockmenu.thtml';
        $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
    } else {
        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
        $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
    }

    if ($_USER['uid'] > 1) {
        if ($blockmenu->_menustyle == 'milonic') {
            $retval .= COM_userMenu();
        } else {
            $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/cssmenu/blockheader.thtml');
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
            $t->set_file('menu','blockmenu.thtml');
            $t->set_var('menuitems',COM_userMenu());
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
            $retval .= COM_endBlock('nexmenu/cssmenu/blockfooter.thtml');
        }
    } else {
        $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/blockheader.thtml');
        $t = new Template( $_CONF['path_layout'] );
        $t->set_file( 'form', 'loginform.thtml' );
        $t->set_var( 'site_url', $_CONF['site_url'] );
        $t->set_var( 'layout_url', $_CONF['layout_url'] );
        $t->set_var( 'lang_username', $LANG01[21] );
        $t->set_var( 'lang_password', $LANG01[57] );
        $t->set_var( 'lang_login', $LANG01[58] );
        $t->set_var('lang_forgetpassword',$LANG01[119]);
        if( $_CONF['disable_new_user_registration'] == 1 )
        {
            $t->set_var( 'lang_signup', '' );
        }
        else
        {
            $t->set_var( 'lang_signup', $LANG01[59] );
        }
        if( $_CONF['remoteauthentication'] && !$_CONF['usersubmission'] ) {
            // Build select
            $select = '<select name="service" id="service"><option value="">' .
                            $_CONF['site_name'] . '</option>';
            if( is_dir( $_CONF['path_system'] . 'classes/authentication/' )) {
                $folder = opendir( $_CONF['path_system'] . 'classes/authentication/' );
                while(( $filename = @readdir( $folder )) !== false ) {
                    $strpos = strpos( $filename, '.auth.class.php' );
                    if( $strpos ) {
                        $service = substr( $filename, 0, $strpos );
                        $select .= '<option value="' . $service . '">'
                                . $service . '</option>';
                    }
                }
            }
            $select .= '</select>';
            $t->set_file( 'services', 'blockservices.thtml' );
            $t->set_var( 'lang_service', $LANG04[121] );
            $t->set_var( 'select_service', $select );
            $t->parse( 'output', 'services' );
            $t->set_var( 'services', $t->finish( $t->get_var( 'output' )));
        } else {
            $t->set_var( 'services', '' );
        }
        $t->parse( 'output', 'form' );
        $retval .= $t->finish ($t->get_var('output'));
        $retval .= COM_endBlock('nexmenu/blockfooter.thtml');
    }
    $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
    return $retval;
}

function phpblock_gladminmenu()
{
    global $_CONF,$_USER,$_TABLES,$_BLOCK_TEMPLATE,$blockmenu;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
    if ($blockmenu->_menustyle == 'milonic') {
        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/milonicmenu/blockheader-blockmenu.thtml,nexmenu/milonicmenu/blockfooter-blockmenu.thtml';
        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
    } else {
        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
    }
    // Check if the admin menu is enabled
    $headermenuItems = false;
    $retval = '';
    if ($_USER['uid'] > 1) {
        $query = DB_query("SELECT grp_access FROM {$_TABLES['nexmenu']} WHERE url='adminmenu' AND location='block' AND is_enabled=1");
        while (list ($grp_id) = DB_fetchArray($query)) {
            $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
            if (SEC_inGroup($grp_name)) {
                if ($blockmenu->_menustyle == 'milonic') {
                    $retval .= COM_adminMenu();
                } else {
                    $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/cssmenu/blockheader.thtml');
                    $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
                    $t->set_file('menu','blockmenu.thtml');
                    $t->set_var('menuitems', COM_adminMenu());
                    $t->parse ('output', 'menu');
                    $retval .= $t->finish ($t->get_var('output'));
                    $retval .= COM_endBlock('nexmenu/cssmenu/blockfooter.thtml');
                }
                break;
            }
        }
    }
    $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
    return $retval;
}


/* Recursive function used to generate any needed submenus and menuitems for the Links Plugin
 * Only used in CSS Menu mode
*/
function nexmenu_showlinks($pid,$type,$cid='site',$numitems=0,$curcategory=0,$lastitem)
{
    global $_CONF,$_TABLES,$CONF_NEXMENU;

    $retval = '';
    $thisitemMenuorder = DB_getItem($_TABLES['nexmenu'],'menuorder',"pid=$pid AND url='linksmenu'");

    $sql = "SELECT title,lid FROM {$_TABLES['links']} WHERE cid='$cid' " . COM_getPermSQL( 'AND' );

    $query = DB_query($sql);
    if ($numitems == 0) {
        $numitems = DB_numRows($query);
    }
    $curcategory++;
    $i=1;
    while (list ($title,$lid) = DB_fetchArray ($query)) {
        $url = $_CONF['site_url'].'/links/portal.php?what=link&item='.$lid;
        if ($i == 1) {
            $retval .= "<li><a href=\"$url\" $target class=\"enclose\">$title</a>" . LB;
        } else {
            $retval .= "<li><a href=\"$url\" $target>$title</a>" . LB;
        }
        $i++;
    }
    $sql = "SELECT cid,category FROM {$_TABLES['linkcategories']} WHERE pid='$cid' " . COM_getPermSQL( 'AND' );
    $qlinkcat = DB_query($sql);
    $numcategories = DB_numRows($qlinkcat);
    if ($numcategories > 0) {
        while (list($catid,$category) = DB_fetchArray($qlinkcat)) {
            $url = $_CONF['site_url'].'/links/index.php?category='.urlencode($category);
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu/');
            if ($type == 'header') {
                $t->set_file('menu','headersubmenu.thtml');
            } else {
                $t->set_file('menu','submenu.thtml');
            }
            if ($pid == 0) {
                $t->set_var('imgclass','drop');
            } else {
                $t->set_var('imgclass','fly');
            }
            if ($pid > 0 AND $i == 1 AND $thisitemMenuorder == 10) {
                $t->set_var('lastitemclass','class="enclose"');
            } elseif ($lastitem AND $pid == 0 AND $i == $numcategories) {
                $t->set_var('lastitemclass','class="enclose"');
            } else {
                $t->set_var('lastitemclass','');
            }

            $t->set_var('menuitem_url',$url);
            $t->set_var('menuitem_label',$category);
            $t->set_var('submenu_items',nexmenu_showlinks($pid,$type,$catid,$numcategories,$curcategory,$lastitem));
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
            $i++;
        }
    }
    return $retval;
}


function phpblock_gltopicsmenu()
{
    global $_CONF,$_BLOCK_TEMPLATE,$CONF_NEXMENU,$blockmenu;

    // Save the current theme functions.php settings
    $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
    if ($blockmenu->_menustyle == 'milonic') {
        $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
        $retval ='<script>
            with(milonic=new menuname("Site Topics Menu")) {' . LB;
        $retval .= $CONF_NEXMENU['blockmenu_default_styles'] . $blockmenu->_blockMenuProperties . LB;
        $retval .= 'style='. $blockmenu->_blockMenuStyle .';';
        $retval .= COM_showTopics(''," sortnum < '50'" );
        $retval .=  '} drawMenus(); </script>';
    } else {
        $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/cssmenu/menuitem2.thtml,nexmenu/cssmenu/menuitem2_on.thtml';
        $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/cssmenu/blockheader.thtml');
        $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
        $t->set_file('menu','blockmenu.thtml');
        $t->set_var('menuitems',COM_showTopics(''," sortnum < '50'" ));
        $t->parse ('output', 'menu');
        $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
        $retval .=  $t->finish ($t->get_var('output'));
        $retval .= COM_endBlock('nexmenu/blockfooter.thtml');
    }
    return $retval;
}


function nexmenu_debug() {
    global $CONF_NEXMENU;

    $retval = '';
    if ($CONF_NEXMENU['debug']) {
        if (!empty($_POST)) {
            $retval .= COM_startBlock("POST_VARS");
            $retval .= print_r($_POST,true);
            $retval .= COM_endBlock();
        }
        if (!empty($_GET)) {
            $retval .= COM_startBlock("GET_VARS");
            $retval .= print_r($_GET,true);
            $retval .= COM_endBlock();
        }

        if (!empty($_FILES)) {
            $retval .= COM_startBlock("POST_FILES");
            $retval .= print_r($_FILES,true);
            $retval .= COM_endBlock();
        }
    }
    return $retval;

}



?>