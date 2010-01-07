<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upload.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
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


include ('../lib-common.php');
require_once("nextime.class.php");

$username = COM_applyFilter($_POST['username']);
$password = COM_applyFilter($_POST['password']);
$op       = COM_applyFilter($_POST['op']);

function nxtime_testuser($username,$password){
    global $_TABLES;
    $uid=DB_getItem($_TABLES['users'],'uid',"username='$username' AND passwd='$password'");
    if($uid > 0) {
        return $uid;
    } else {
        return false;
    }
}

switch($op) {
    case 'testconnection':
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: application/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        echo "<data>";
        $uid=nxtime_testuser($username,$password);
        if($uid){
            echo "<status>success</status>";
            echo "<statusid>1</statusid>";
            echo "<uid>{$uid}</uid>";
        } else {
            echo "<status>fail</status>";
            echo "<statusid>0</statusid>";
            echo "<uid>-1</uid>";
        }
        echo "</data>";
        break;

    case 'getdropdown':
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: application/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        echo "<data>";
        $whichddl = COM_applyFilter($_POST['whichdropdown'],true);
        $selected = COM_applyFilter($_POST['selected'],true);
        $ts=new nexTime();
        switch($whichddl){
            case 1:
                $title=$LANG_NEXTIME_HEADER['nextime_activity_id'];
                $lid=$CONF_NEXTIME['nexlist_nextime_activities'];
                $list=$ts->getActivitiesDropDown(0);
                break;

            case 2:
                $title=$LANG_NEXTIME_HEADER['project_id'];
                $lid=$CONF_NEXTIME['nexlist_nextime_projects'];
                $list=$ts->getProjectDropDownFromActivityID($selected);
                break;

            case 3:
                $title=$LANG_NEXTIME_HEADER['task_id'];
                $lid=$CONF_NEXTIME['nexlist_timesheet_tasks'];
                $list=$ts->getTaskDropDownFromActivityID($selected);
                break;

            default:
                $title=$LANG_NEXTIME_HEADER['nextime_activity_id'];
                $lid=$CONF_NEXTIME['nexlist_nextime_activities'];
                break;
        }

        $list=str_replace("&","&amp;",$list);
        echo $list;
        echo "<listname>".htmlentities($title)."</listname>";
        echo "</data>";
        break;

    case 'save':
        $ts=new nexTime();
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: application/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        $uid=nxtime_testuser($username,$password);
        if($uid>0){
            $ds=$_POST['datestamp0'];
            $testdate=strftime($ds." 00:00:00",time());
            $testintdate=strtotime($testdate);
            $_POST['datestamp0']=$testintdate;
            $_USER['uid']=$uid;
            $ts->setDataFromPOST(0);
            $ret=$ts->commitData(0,$uid);
            if($ret){
                echo "<data>\n<error>0</error>\n</data>";
            }else{
                echo "<data>\n<error>1</error>\n</data>";
            }
        }else{
            echo "<data>\n<error>1</error>\n</data>";
        }
        break;

}


?>