<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                          |
// | Date: Sept. 23, 2009                                                        |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | functions.inc - nexTime plugin functions file                               |
// +-----------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                                |
// | Randy Kolenko          - randy.kolenko@nextide.ca                           |
// +-----------------------------------------------------------------------------+
// |                                                                             |
// | This program is licensed under the terms of the GNU General Public License  |
// | as published by the Free Software Foundation; either version 2              |
// | of the License, or (at your option) any later version.                      |
// |                                                                             |
// | This program is part of the Nextide nexPro Suite and is licensed under      |
// | The GNU license and is OpenSource but released under closed distribution.   |
// | You are freely able to modify the source code to meet your needs but you    |
// | are not free to distribute the original or modified code without permission |
// | Refer to the license.txt file or contact nextide if you have any questions  |
// |                                                                             |
// | This program is distributed in the hope that it will be useful, but         |
// | WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY  |
// | or FITNESS FOR A PARTICULAR PURPOSE.                                        |
// | See the GNU General Public License for more details.                        |
// |                                                                             |
// | You should have received a copy of the GNU General Public License           |
// | along with this program; if not, write to the Free Software Foundation,     |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             |
// |                                                                             |
// +-----------------------------------------------------------------------------+
//

$langfile = $_CONF['path'] . 'plugins/nextime/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
}else {
    include_once ($_CONF['path'] . 'plugins/nextime/language/english.php');
}
require_once ($_CONF['path'] . 'plugins/nextime/nextime.php');
include_once ($_CONF['path'] . 'plugins/nextime/autouninstall.php');


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nextime()
{
    global $_CONF;

    //return $_CONF['layout_url'] . '/nextime/images/admin/nextime.gif';
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nextime()
{
    global $_CONF, $CONF_NEXTIME,$CONF_NEXPRO;

    $code  ='<link rel="stylesheet" type="text/css" href="' . $_CONF['site_url'] . '/nextime/css/nextime.css">';
    $code .='<link type="text/css" rel="stylesheet" href="' . $CONF_NEXPRO['yui_base_url'] . '/calendar/assets/calendar.css">' . LB;
    $code .='<script type="text/javascript" src="' . $_CONF['site_url'] . '/nextime/javascript/nextime.js"></script>';
    $code .='<script type="text/javascript" src="' . $_CONF['site_url'] . '/javascript/nexYUICal.js"></script>';
    return $code;
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getmenuitems_nextime()
{
    global $CONF_FORUM,$_CONF,$LANG_NEXTIME;
    $menuitems["$LANG_NEXTIME[pluginlabel]"] = "$_CONF[site_url]/nextime/index.php";
    return $menuitems;

}

/**
* Returns the user menuitem option for this plugin
* Only one menu item can be returned.
*/
function plugin_getuseroption_nextime()
{
    global $CONF_FORUM, $_CONF, $LANG_NEXTIME;

    return array($LANG_NEXTIME['menulabel'], $_CONF['site_url'] . '/nextime/index.php', 0);

}



function plugin_user_create_nextime($uid){
    global $_CONF, $_TABLES,$CONF_NEXTIME;
    DB_query("UPDATE {$_TABLES['users']} SET status=3 WHERE uid = $uid");
    plugin_user_changed_nextime($uid);
}


function plugin_user_changed_nextime ($uid) {
    global $_CONF, $_TABLES,$CONF_NEXTIME;

    $region=COM_applyFilter($_REQUEST['region']);
    $techNumber=COM_applyFilter($_REQUEST['techNumber']);
    $special_exclusion=0;
    if(isset($_REQUEST['special_exclusion'])) {
        $special_exclusion=1;
    }
    $isThere=DB_getItem($_TABLES['nextime_extra_user_data'],"uid", "uid=$uid");
    if($isThere!=''){
        $sql="UPDATE {$_TABLES['nextime_extra_user_data']} set region='$region', tech_number='{$techNumber}' , special_exclusion=$special_exclusion where uid={$uid}";
    }else{
        $sql="INSERT INTO {$_TABLES['nextime_extra_user_data']} (uid,region,tech_number,special_exclusion) values ('{$uid}','{$region}','{$techNumber}',$special_exclusion)";
    }
    DB_query($sql);


}


function plugin_profilevariablesedit_nextime($uid, &$template){
    global $_TABLES,$CONF_NEXTIME;
    $region=DB_getItem($_TABLES['nextime_extra_user_data'],"region","uid=$uid");
    $list=nexlistOptionList('options','',$CONF_NEXTIME['nexlist_user_locations'],0,$region);

    $techNumber=DB_getItem($_TABLES['nextime_extra_user_data'],"tech_number","uid=$uid");

    $template->set_var('region',$list);
    $template->set_var('techNumber',htmlentities($techNumber));

}

//display our status information block
function plugin_centerblock_nextime ($where = 1, $page = 1, $topic = ''){
    global $_CONF, $_USER, $CONF_NEXTIME,$LANG_NEXTIME;

    if($CONF_NEXTIME['show_center_block']){
        require_once($_CONF['path_html']."nextime/nextime.class.php");

        if($where!=1){
            return;
        }

        $T = new Template($CONF_NEXTIME['template_path']);
        $T->set_file (array ('row'        => 'front_page_notifications.thtml'  ));
        $T->set_var('site_url',$_CONF['site_url']);
        $T->set_var($LANG_NEXTIME);
        if($_USER['uid']>1){
            $ts=new nexTime();
            $ret=$ts->getRejectedItems($_USER['uid']);
            $row="";
            $totalcount=count($ret);
            if(count($ret)>0){
                for($cntr=0;$cntr<count($ret);$cntr++){
                    //need to pull back the sunday-sunday record for this item
                    $stos=$ts->generateSundayToSundayRange($ts->getDateStampFromID($ret[$cntr]));
                    $startdate=date('Y/m/d',$stos[0]);
                    $enddate=date('Y/m/d',$stos[1]);

                    $row .="<a href='nextime/entry.php?start_date={$startdate}&end_date={$enddate}'>{$startdate} - {$enddate}</a><br>";

                }
                $T->set_var('rejected_items',$row);
            }else{
                $T->set_var('rejected_items',$row);
                $T->set_var('show_rejection_row','none');
            }
            $ret=$ts->getItemsSubmittedBySomeoneElse($_USER['uid']);
            $row="";
            $storageArray=array();
            $totalcount=count($ret);
            if(count($ret)>0){
                for($cntr=0;$cntr<count($ret);$cntr++){
                    //need to pull back the sunday-sunday record for this item
                    $stos=$ts->generateSundayToSundayRange($ts->getDateStampFromID($ret[$cntr]));
                    if(!in_array($stos[0], $storageArray)){
                        $startdate=date('Y/m/d',$stos[0]);
                        $enddate=date('Y/m/d',$stos[1]);
                        $storageArray[]=$stos[0];
                        $row .="<a href='nextime/entry.php?start_date={$startdate}&end_date={$enddate}'>{$startdate} - {$enddate}</a>";
                        $row .="&nbsp;&nbsp;&nbsp;<input type='checkbox' onclick='ackModified(\"{$stos[0]}\",\"{$stos[1]}\");'>{$LANG_NEXTIME['acknowledge_change']}";
                        $row .="<br>";
                        $storageArray[]=$stos[0];
                    }
                }
                $T->set_var('modified_items',$row);
            }else{
                $T->set_var('modified_items',$row);
                $T->set_var('show_modified_row','none');
            }
            if($totalcount==0){
                $T->set_var('hide_table','display:none');
            }
        }else{
            $T->set_var('rejected_items','');
            $T->set_var('show_rejection_row','none');
            $T->set_var('modified_items','');
            $T->set_var('show_modified_row','none');
            $T->set_var('hide_table','display:none');

        }


        $T->parse('output','row',true);
        return $T->finish($T->get_var('output'));
    }
}



function plugin_upgrade_nextime() {
    global $_TABLES,$CONF_NEXTIME;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nextime'");

    switch ($curversion) {
    case "1.0.0":
    case "1.1.0":
        if (nextime_upgrade_120() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '1.2.0', `pi_gl_version` = '1.6.1' WHERE `pi_name` = 'nextime' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nextime'") == $CONF_NEXTIME['version']) {
        return 13;
    } else {
        return 12;
    }
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nextime() {
    global $CONF_NEXTIME;
    return $CONF_NEXTIME['version'];
}


/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nextime($plugin,$state) {
    global $_TABLES;

    // The nexproject plugin is dependant on the plugin nexpro being installed and enabled
    if ($plugin == 'nexpro' || $plugin=='nexlist') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nextime'");
        }
    }
}


/**
* Provide URL of a documentation file
*
* @param    string  $file   documentation file being requested, e.g. 'config'
* @return   mixed           URL or false when not available
*
*/
function plugin_getdocumentationurl_nextime($file)
{
    global $_CONF;

    static $docurl;

    switch ($file) {
    case 'index':
    case 'config':
        if (isset($docurl)) {
            $retval = $docurl;
        } else {
            $retval = 'http://www.nextide.ca/dokuwiki/doku.php/core_applications_nextime#configuration';
        }
        break;

    default:
        $retval = false;
        break;
    }

    return $retval;
}
?>