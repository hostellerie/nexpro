<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | approvals.php                                                             |
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

//this page will provide a list of abilities for the end user to do.
require_once('../lib-common.php');
require_once("nextime.class.php");



$ts=new nexTime();

// checking whether employee or delegate selected
$emp = (isset($_GET['emp'])) ? COM_applyFilter($_GET['emp'], true) : COM_applyFilter($_GET['del'], true);
$sup=COM_applyFilter($_GET['sup'],true);
$hidefullyapproved=COM_applyFilter($_GET['hidefullyapproved']);
$cookieHideFullyApproved=$_COOKIE['hidefullyapproved'];

$showAsTimesheet=COM_applyFilter($_GET['showAsTimesheet'],true);
$start_date=COM_applyFilter($_REQUEST['start_date']);
$end_date=COM_applyFilter($_REQUEST['end_date']);

if($start_date==''){
    $startStamp=time()-(86400*$CONF_NEXTIME['number_of_days_before']);
}else{
    $startStamp=strtotime($start_date);
}
if($end_date==''){
    if($start_date!=''){
        $endStamp=$startStamp+(86400*$CONF_NEXTIME['number_of_days_after']);
    }else{
        $endStamp=time()+(86400*$CONF_NEXTIME['number_of_days_after']);
    }
}else{
    $endStamp=strtotime($end_date);
}
$numberOfDays=intval(($endStamp-$startStamp)/60/60/24)+1;

$uid=$_USER['uid'];
if($uid<1){
    echo $LANG_NEXTIME['not_logged_in'];
    echo COM_siteFooter();
    exit(0);
}

$hideapproved=false;
if($hidefullyapproved=='true'){
    $hideapproved=true;
    setcookie('hidefullyapproved','true');
}elseif($hidefullyapproved=='false'){
    $hideapproved=false;
    setcookie('hidefullyapproved','false');
 }elseif($hidefullyapproved==''){ //condition where we're just entering into the page from somewhere else
    if($cookieHideFullyApproved=='true'){
        $hideapproved=true;
    }
}


echo COM_siteHeader();

if($endStamp<$startStamp){ //Hey! You can't have an end date thats less than the start date...
    echo '<br><br><a href="index.php">' . $LANG_NEXTIME['end_date_before_start'];
    echo  $LANG_NEXTIME['back_link_label'] . '</a>' ;
    echo COM_siteFooter();
    exit(0);
}


$ts= new nexTime();
$T = new Template($CONF_NEXTIME['template_path']);
$T->set_file (array (
    'javascript'  => 'nextime.js.thtml',
    'approvalpage'=> 'approve.thtml',
    ));
$T->set_var('site_url',$_CONF['site_url']);
$T->set_var($LANG_NEXTIME);
$T->set_var('allfields',$ts->getTableColumns('nextime_timesheet_entry'));
$T->set_var('start_date',$start_date);
$T->set_var('end_date',$end_date);
$T->set_var('emp',$emp);
$T->set_var('disable',"display:none");
$T->set_var('disable_on_lock'," readonly ");
$T->set_var('comment_disable', '');
$T->set_var('comment_edit_disable', '');

$adjustment_save = ($ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp)) ? '_adj' : '';
$T->set_var('adj_postfix', $adjustment_save);

$T->parse('output','javascript');
if($hideapproved){
    $T->set_var('hidefullyapproved_check','checked');
}


if($emp>0){//specific user
    //first, detect if they should be seeing this user's timesheets
    $shouldBeApproving=$ts->testIfUserCanApprove($uid,$emp );
    if(!$shouldBeApproving){
        echo $LANG_NEXTIME['should_not_be_approving'];
        echo COM_siteFooter();
        exit(0);
    }

    if($showAsTimesheet==1){
        $output='';
        if(SEC_inGroup('nexTime Finance') && !SEC_inGroup('Root')){//disable it all
            $T->set_var('comment_disable', ' disabled ');
            $T->set_var('comment_edit_disable', 'display:none');
            $output.=$ts->generateTableHeader(true,true, $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp));
            $retval=$ts->generateTimesheetRows($emp,$startStamp,$numberOfDays,null,0,true,true);
            $output.=$retval[1];
            $output.=$ts->generateTableFooter(true,$retval[2],$retval[3], $retval[4], $retval[5], $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp));
            $output.=$ts->generateTotalRowCount($retval[0]);
        }else{ //enable the timesheet for editing
            $output.=$ts->generateTableHeader(false,true, $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp));
            $retval=$ts->generateTimesheetRows($emp,$startStamp,$numberOfDays,null,0,false,true);
            $output.=$retval[1];
            if($ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp)){
                $output.=$ts->generateTableFooter(true,$retval[2],$retval[3], $retval[4], $retval[5], $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp));
            }else{
                $output.=$ts->generateTableFooter(false,$retval[2],$retval[3], $retval[4], $retval[5], $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$emp));
            }
            $output.=$ts->generateTotalRowCount($retval[0]);
        }
        $T->set_var('approval_rows',$output);
        $T->set_var('approval_all_table_style','display:none');
        $T->set_var('approved_by',$_USER['uid']);
        $T->set_var('userinformation_table_style','');

       //using $emp, we generate the top information table
       $T->set_var('emp_sup',$ts->getUserFullName($ts->getSupervisorUID($emp)));
       $T->set_var('emp_number',$ts->getEmployeeNumber($emp));
       $T->set_var('emp_name',$ts->getUserFullName($emp));

    }else{//show as line items
        $output=$ts->generateApprovalTimesheetRows($uid, $emp, false, $hideapproved);
        $T->set_var('approval_rows',$output[1]);
        $T->set_var('approval_all_table_style','');
        $T->set_var('userinformation_table_style','display:none;');
    }
}else{//all timesheets that this user can see
    if($sup>0){
        $output=$ts->generateApprovalTimesheetRows($sup, 0,false,$hideapproved,true);
    }else{
        $output=$ts->generateApprovalTimesheetRows($uid, 0,false,$hideapproved,false);
    }
    $T->set_var('approval_all_table_style','');
    $T->set_var('userinformation_table_style','display:none;');
    $T->set_var('approval_rows',$output[1]);
}
$T->set_var($LANG_NEXTIME);
$T->parse('output','approvalpage',true);
echo $T->finish($T->get_var('output'));
echo COM_siteFooter();

?>
