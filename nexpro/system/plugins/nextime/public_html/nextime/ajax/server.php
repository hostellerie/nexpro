<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | server.php                                                                |
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


require_once('../../lib-common.php');
require_once("../nextime.class.php");


$op=COM_applyFilter($_REQUEST['op']);
$start_date=COM_applyFilter($_REQUEST['start_date']);
$end_date=COM_applyFilter($_REQUEST['end_date']);
$startStamp=strtotime($start_date);
$endStamp=strtotime($end_date);
$numberOfDays=intval(($endStamp-$startStamp)/60/60/24)+1;


function generateTimesheet($uid=0,$disabled=false, $isapproval=false, $forWhichUser=0){
    global $startStamp,$numberOfDays, $_USER;
    if($uid==0){
        $uid=$_USER['uid'];
    }
    if($forWhichUser!=0){
        $uid=$forWhichUser;

    }
    $ts= new nexTime();
    $output='';
    $output.=$ts->generateTableHeader($disabled,$isapproval, $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$uid));
    $retval=$ts->generateTimesheetRows($uid,$startStamp,$numberOfDays,NULL,0,$disabled,$isapproval);
    $output.=$retval[1];
    $output.=$ts->generateTableFooter($isapproval,$retval[2],$retval[3], $retval[4], $retval[5], $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$uid));
    $output.=$ts->generateTotalRowCount($retval[0]);
    return $output;
}


if($_USER['uid']>1){

    switch($op){
        case 'deleteentries': //delete timesheet entries
            $list=COM_applyFilter($_REQUEST['list']);
            $approvalUID=COM_applyFilter($_REQUEST['approved_by'],true);
            $forWhichUID=COM_applyFilter($_REQUEST['emp'],true);
            $ts= new nexTime();
            $ret=$ts->deleteEntries($list);
            if($forWhichUID==0){
                $output=generateTimesheet();
            }else{
                $output=generateTimesheet($forWhichUID,false,true,$forWhichUID);
            }

            if($ret){
                $error='Successfully deleted your selected items...';
            }else{
                $error='Some of your items could not be deleted as they were approved or rejected...';
            }
            break;

        // case where saving adjustments
        case 'savetimesheet_adj':
            $maxRowNumber=COM_applyFilter($_POST['max_row_number'],true);
            $approvalUID=COM_applyFilter($_POST['approved_by'],true);
            $forWhichUID=COM_applyFilter($_POST['emp'],true);
            $ts=new nexTime();
            for($cntr=0;$cntr<$maxRowNumber;$cntr++){
                $id = COM_applyFilter($_POST['id'.$cntr], true);
                $adj = COM_applyFilter($_POST['adjustment'.$cntr]);
                $ts->saveAdjustment($id, $adj);
            }
            $error='Successfully saved your timesheet... ';
            if( ($forWhichUID==0) || ($forWhichUID==$_USER['uid']) ){
                $output=generateTimesheet();
            }else{
                $output=generateTimesheet($forWhichUID,false,true,$forWhichUID);
            }
            break;

        case 'savetimesheet':
            $maxRowNumber=COM_applyFilter($_POST['max_row_number'],true);
            $approvalUID=COM_applyFilter($_POST['approved_by'],true);
            $forWhichUID=COM_applyFilter($_POST['emp'],true);


            $ts=new nexTime();
            for($cntr=0;$cntr<$maxRowNumber;$cntr++){
                $ts->setDataFromPOST($cntr);
                if($forWhichUID==0) {
                    $ret=$ts->commitData($approvalUID,$_USER['uid']);
                }else{
                    $ret=$ts->commitData($approvalUID,$forWhichUID);
                }

            }

            if($ret){

                $error='Successfully saved your timesheet... ';
                if( ($forWhichUID==0) || ($forWhichUID==$_USER['uid']) ){
                    $output=generateTimesheet();
                }else{
                    $output=generateTimesheet($forWhichUID,false,true,$forWhichUID);
                }
            }else{
                $error='There is an error in your timesheet - ensure all numeric fields are filled with numeric data!...';
                $output='';
            }

            break;

        case 'saveapprovaltimesheet':
            $maxRowNumber=COM_applyFilter($_POST['max_row_number'],true);
            $approvalUID=COM_applyFilter($_POST['approved_by'],true);
            $forWhichUID=COM_applyFilter($_POST['emp'],true);


            $ts=new nexTime();
            for($cntr=0;$cntr<$maxRowNumber;$cntr++){
                //pick off the approval

                $chk=COM_applyFilter($_POST['chkapproval'.$cntr],true);
                $rejectchk=COM_applyFilter($_POST['chkreject'.$cntr],true);

                $id=COM_applyFilter($_POST['id'.$cntr],true);
                com_errorlog("id: $id, chk: $chk");
                if($rejectchk==0){
                    if($chk==0){
	                    $ret=$ts->unApproveSingleItem($id);
                    }else{
	                    $ret=$ts->approveSingleItem($id);
                    }
                }

            }

            if($ret){

                $error='Successfully saved your timesheet... ';
                if( ($forWhichUID==0) || ($forWhichUID==$_USER['uid']) ){
                    $output=generateTimesheet();
                }else{
                    $output=generateTimesheet($forWhichUID,false,true,$forWhichUID);
                }
            }else{
                $error='There is an error in your timesheet - ensure all numeric fields are filled with numeric data!...';
                $output='';
            }

            break;
        case 'lockentries':

            $datestamp=COM_applyFilter($_REQUEST['datestamp'],true);
            $emp=COM_applyFilter($_REQUEST['emp'],true);
            $ts=new nexTime();
            $ts->lockTimesheetEntries($datestamp,$emp);
            if( $emp==$_USER['uid'] ){
                $output=generateTimesheet($emp);
            }else{
                $output=generateTimesheet($_USER['uid'],false,true,$emp);
            }
            //$output=generateTimesheet($emp);
            $error='Successfully locked your timesheet entry...';
            break;

        case 'unlockentries':

            $datestamp=COM_applyFilter($_REQUEST['datestamp'],true);
            $emp=COM_applyFilter($_REQUEST['emp'],true);

            $ts=new nexTime();

            $ts->unlockTimesheetEntries($datestamp, $emp);

            if( $emp==$_USER['uid'] ){
                $output=generateTimesheet($emp);
            }else{
                $output=generateTimesheet($_USER['uid'],false,true,$emp);
            }
            //$output=generateTimesheet($emp);
            $error='Successfully UN-locked your timesheet entry...';
            break;

        case 'approveitem':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $userid=COM_applyFilter($_REQUEST['uid'],true);
            $ts=new nexTime();
            $ret=$ts->approveSingleItem($id);
            if($ret){
                $error='Successfully approved the item...';
                $output=generateTimesheet($userid,false,true);
            }else{
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;

        case 'unapproveitem':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $userid=COM_applyFilter($_REQUEST['uid'],true);
            $ts=new nexTime();
            $ret=$ts->unApproveSingleItem($id);
            if($ret){
                $error='Successfully un-approved the item...';
                $output=generateTimesheet($userid,false,true);
            }else{
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;

        case 'rejectitem':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $comment=COM_applyFilter($_REQUEST['comment']);
            $comment=($_REQUEST['comment']);
            $userid=COM_applyFilter($_REQUEST['uid'],true);
            $ts=new nexTime();
            $ret=$ts->rejectSingleItem($id,$comment);
            if($ret){
                $error='Successfully rejected the item... ';
                $output=generateTimesheet($userid,false,true);
            }else{
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;

        case 'unrejectitem':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $userid=COM_applyFilter($_REQUEST['uid'],true);
            $ts=new nexTime();
            $ret=$ts->unRejectSingleItem($id);
            if($ret){
                $error='Successfully un-rejected the item...';
                $output=generateTimesheet($userid,true,true);
            }else{
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;



        case 'approverange':
            $emp=COM_applyFilter($_REQUEST['emp'],true);
            $start=COM_applyFilter($_REQUEST['start'],true);
            $end=COM_applyFilter($_REQUEST['end'],true);
            $ts=new nexTime();
            $ret=$ts->approveRange($emp, $start, $end);
            if($ret){
                $error='Successfully approved the range of dates...';
                $ret=$ts->generateApprovalTimesheetRows($_USER['uid'], $emp);
                $output=$ret[1];
            }else{
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;

        case 'unapproverange':
            $emp=COM_applyFilter($_REQUEST['emp'],true);
            $start=COM_applyFilter($_REQUEST['start'],true);
            $end=COM_applyFilter($_REQUEST['end'],true);
            $ts=new nexTime();
            $ret=$ts->unApproveRange($emp, $start, $end);
            if($ret){
                $error='Successfully un-approved the item...';
                $ret=$ts->generateApprovalTimesheetRows($_USER['uid'], $emp);
                $output=$ret[1];
            }else{
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;

        case 'approveallchecked':
            $idlist=COM_applyFilter($_REQUEST['allids']);
            $postedemp=COM_applyFilter($_REQUEST['emp'],true);
            if($idlist==''){
                $error='You have not chosen any items...';
                $output='';
            }else{
                $ts=new nexTime();
                $arr=explode(",",$idlist);
                $retval=true;
                $thisval='';
                foreach($arr as $id){
                    $thisval=COM_applyFilter($_REQUEST['approve'.$id]);
                    //using this ID, we need to determine the date range it fits in...
                    $dtarray=$ts->generateSundayToSundayRange($ts->getDateStampFromID($id));
                    if($thisval!=''){
                        $emp=$ts->getUIDFromID($id);
                        $ret=$ts->approveRange($emp, $dtarray[0], $dtarray[1]);
                        $retval=$retval&$ret;
                    }
                    $thisval='';
                }
                if($retval){
                    $error='Successfully approved all of the items you chose...';
                    $output='';
                    $ret=$ts->generateApprovalTimesheetRows($_USER['uid'], $postedemp);
                    $output=$ret[1];
                }else{
                    $error='Error! Database Error thrown.';
                    $output='';
                }
            }
            break;

        case 'getrejectionreason':

            $id=COM_applyFilter($_REQUEST['id'],true);
            $ts=new nexTime();
            $output=$ts->getRejectionReasonById($id);
            $error='';
            break;

        case 'clearreject':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $userid=COM_applyFilter($_REQUEST['uid'],true);
            $ts=new nexTime();
            $ts->unRejectSingleItem($id);
            $output=generateTimesheet($userid);
            $error='';
            break;


        case 'getproject':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $row=COM_applyFilter($_REQUEST['row'],true);
            $ts=new nexTime();
            $output=$ts->getProjectDropDownFromActivityID($id);
            $output ='<select name="project_id' . $row . '" id="project_id' . $row . '" onchange="changeflag()" class="dropdown_menus">' . $output . '</select>';
            $error='';
            break;

        case 'gettask':
            $id=COM_applyFilter($_REQUEST['id'],true);
            $row=COM_applyFilter($_REQUEST['row'],true);
            $ts=new nexTime();
            $output=$ts->getTaskDropDownFromActivityID($id);
            $output ='<select name="task_id' . $row . '" id="task_id' . $row . '" onchange="changeflag()" class="dropdown_menus">' . $output . '</select>';
            $error='';
            break;


        case 'getsundaytosunday':
            $date=COM_applyFilter($_REQUEST['date']);
            $datestamp=strtotime($date);
            $ts=new nexTime();
            $retDateArray=$ts->generateSundayToSundayRange($datestamp);
            $start=date("Y/m/d",$retDateArray[0]);
            $end=date("Y/m/d",$retDateArray[1]);
            $output="$start,$end";
            $error='';
            break;

        case 'lockrange':
            $emp=COM_applyFilter($_REQUEST['emp'],true);
            $start=COM_applyFilter($_REQUEST['start'],true);
            $end=COM_applyFilter($_REQUEST['end'],true);
            $ts=new nexTime();
            $ret=$ts->lockRange($start, $end,$emp);
            if($ret>0){
                $error='Successfully locked the range of dates...';
                $ret=$ts->generateApprovalTimesheetRows($_USER['uid'], $emp);
                $output=$ret[1];
            }elseif($ret===false){
                $error='Error! Database Error thrown.';
                $output='';
            }else{
                $error='Sorry, you cannot lock a timesheet when it has less than 80 booked hours.';
                $output='';
            }
            break;

        case 'unlockrange':
            $emp=COM_applyFilter($_REQUEST['emp'],true);
            $start=COM_applyFilter($_REQUEST['start'],true);
            $end=COM_applyFilter($_REQUEST['end'],true);
            $ts = new nexTime();
            $ret = $ts->unlockRange($start, $end, $emp);
            if($ret>0){
                $error='Successfully UN-locked the range of dates...';
                $ret=$ts->generateApprovalTimesheetRows($_USER['uid'], $emp);
                $output=$ret[1];
            }elseif($ret===false){
                $error='Error! Database Error thrown.';
                $output='';
            }
            break;

        case 'ackmodified':
            $start=COM_applyFilter($_REQUEST['startstamp'],true);
            $end=COM_applyFilter($_REQUEST['endstamp'],true);
            $ts=new nexTime();
            $ret=$ts->setAcknowledgedModified($start,$end,$_USER['uid']);
            $output='';
            if($ret){
                $error='';
            }else{
                $error='Error! Database Error thrown.';
            }
            break;


        case 'test':
            $output="<tr><td>3</td><td>4</td><td>5</td></tr>";
            break;
    }
}else{
    $output="";
    $error="Your login has expired - please log back into the system...";

}

$output=htmlentities($output);
$error=htmlentities($error);
$op=htmlentities($op);

$retval = "<result>";
$retval .= "<error>$error</error>";
$retval .= "<op>$op</op>";
$retval .= "<output>$output</output>";
$retval .= "</result>";

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
echo $retval;


?>