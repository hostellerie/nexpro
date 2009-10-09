<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Sept. 23, 2009                                                      |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nextime_timesheet_table_class.php - main timesheet table class            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |  Author: Randy Kolenko   - randy@nextide.ca                               |
// +---------------------------------------------------------------------------+
// | Extends Base table class and provides methods for manipulating the main   |
// | timesheet table                                                           |
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
require_once('base_table_class.php');

class TABLE_timesheet_entry extends TABLE{
    var $fulltablename;     // table name
    var $tablename;         // alias name used when accessing this class so not to use index identifiers
    var $fieldlist;         // list of fields in this table
    var $fieldvals;         // list of values for fields
    var $numberOfColumns;   // holds the number of columns dynamically determined in this variable
    var $resultSet;         // holds a result set
    var $rowsReturned;      //holds the number of rows from the result set

    //constructor
    function TABLE_timesheet_entry(){
        global $_TABLES;
        $this->fulltablename   = $_TABLES['nextime_timesheet_entry'];
        $this->tablename       = 'nextime_timesheet_entry';
        $this->rows_per_page   = 10;
        $this->resultSet       = NULL;
        $this->rowsReturned    = 0;

        $this->fieldlist = array('id','uid','nextime_activity_id','task_id','project_id','regular_time',
        'time_1_5','time_2_0','evening_hours','standby','adjustment','stat_time','vacation_time_used','floater','sick_time',
        'bereavement','jury_duty','unpaid_hrs','other','comment','datestamp','locked','approved','rejected','rejected_comment','modified_by_uid','rejected_by_uid','ack_modified');

        $this->numberOfColumns=count($this->fieldlist);
    }


    //based on id, datestamp and uid, we can determine if this is all rows, 1 specific row, or many rows for 1 date for 1 user
    function get_timesheet_rows($datestamp=NULL,$uid=NULL,$id=0,$idlist='', $showApproved=false, $showOnlyApproved=false, $showOnlyRejected=false){
        global $_TABLES;

        $lowerextent=$datestamp-14400;
        $upperextent=$datestamp+14400;

        if($id!=0 || $id!=NULL){//specific row we're after
            $sql="SELECT * FROM {$this->fulltablename} WHERE id='{$id}'";

        }elseif( ($datestamp!=NULL || $datestamp!=0) && ($uid!=NULL || $uid!=0)){//after a user and specific day (may return multiple rows
            $sql="SELECT * FROM {$this->fulltablename} WHERE datestamp<='{$upperextent}' AND  datestamp>='{$lowerextent}' AND uid='{$uid}'  ";
            $sql.=" ORDER BY ID ASC ";

        }elseif(($datestamp==NULL || $datestamp==0) && ($uid!=NULL || $uid!=0)){//after all the records for a user
            $sql="SELECT * FROM {$this->fulltablename} WHERE uid='{$uid}' ";
            $sql.=" ORDER BY ID ASC ";
        }elseif($idlist!=''){//get a row whose user ids are in this list
            $approvedClause='';
            if($showApproved){
                $approvedClause=' AND (a.approved=1 or a.approved=0) ';
            }
            if($showOnlyApproved){
                $approvedClause=' AND (a.approved=1) ';
            }
            if($showOnlyRejected){
                $approvedClause=' AND (a.rejected=1) ';
            }
            $sql  ="SELECT a.*,b.region,b.tech_number FROM {$this->fulltablename} a  ";
            $sql .="LEFT OUTER JOIN {$_TABLES['nextime_extra_user_data']} b ON a.uid=b.uid ";
            $sql .="WHERE a.uid in ($idlist) {$approvedClause} ORDER BY a.UID ASC ";

        }//end elseif
        $this->resultSet=DB_query($sql);
        $this->rowsReturned=DB_numRows($this->resultSet);
        $this->fetchNextRow();
    }


    function commitData($approvalUID, $forWhichUID){
        global $_TABLES,$_USER;
        $allZeroFlag=true;
        if($approvalUID!=$forWhichUID && $approvalUID!=0){//if the approving person is NOT the person this entry is for
            $this->fieldvals['uid']=$forWhichUID;
            $this->fieldvals['modified_by_uid']=$approvalUID;
            $isapproval=1;
        }else{
            $this->fieldvals['uid']=$_USER['uid'];
            $this->fieldvals['modified_by_uid']=$_USER['uid'];
            $isapproval=0;
        }

        $fieldlist=implode(",",$this->fieldlist);
        $fieldlist=",".$fieldlist;
        $fieldlist=str_replace(",id,","",$fieldlist);
        $arrFields=explode(",",$fieldlist);
        if($this->fieldvals['id']==NULL || $this->fieldvals['id']==0 ){//insert
            $sql  ="INSERT INTO {$this->fulltablename} ({$fieldlist}) VALUES (";
            $insertFields='';
            foreach($arrFields as $field){
                $fieldValue=$this->fieldvals[$field];
                $fieldValue=addslashes($fieldValue);
                if($fieldValue=='' && $field!='comment') {
                    $fieldValue=0;
                }
                if( ($fieldValue!='' && $field!='uid' && $field!='datestamp' && $field!='modified_by_uid') && ( ($fieldValue!='0' ) && ($field!='nextime_activity_id' || $field!='task_id' || $field!='project_id') ) ) $allZeroFlag=false;
                if($insertFields!='') $insertFields.=',';
                if($field=='comment') {
                    if ($fieldValue[0] == '@' || $fieldValue[0] == '=') {
                        $fieldValue = ' ' . $fieldValue;
                    }
                }
                $insertFields .="'{$fieldValue}'";
            }
            $sql.=$insertFields;
            $sql .=")";
        }else{//update
            //lets see if its locked first.  if it is, ignore it!

            $sql="SELECT locked,approved FROM {$this->fulltablename} WHERE id={$this->fieldvals['id']}";
            $res=DB_query($sql);
            list($islocked, $isapproved)=DB_fetchArray($res);

            if(intval($islocked)==0 && intval($isapproved)==0){
                $sql  ="UPDATE {$this->fulltablename} SET ";
                $updateFields='';
                foreach($arrFields as $field){
                    $fieldValue=$this->fieldvals[$field];
                    $fieldValue=addslashes($fieldValue);
                    if($fieldValue=='' && $field!='comment' && $field!='rejected_comment') {
                        $fieldValue=0;
                    }
                    if($field!='rejected_comment'){
                        if($fieldValue!='' && $field!='uid' && $field!='datestamp') $allZeroFlag=false;
                        if($updateFields!='') $updateFields.=',';
                        if($field=='comment') {
                            if ($fieldValue[0] == '@' || $fieldValue[0] == '=') {
                                $fieldValue = ' ' . $fieldValue;
                            }
                        }
                        $updateFields .="{$field}='{$fieldValue}'";
                    }
                }
                $sql.=$updateFields;
                $sql .=" WHERE id={$this->fieldvals['id']}";
            }else{
                $sql="SELECT id FROM {$this->fulltablename} WHERE id={$this->fieldvals['id']}";
            }
        }
        if(!$allZeroFlag){

            DB_query($sql,1);
        }
        if(DB_error()){
            return false;
        }else{
            return true;
        }
    }//end commitData



    function deleteEntry($id){
        $id=intval($id);
        $isApproved=DB_getItem($this->fulltablename,"approved","id={$id}");
        $isRejected=DB_getItem($this->fulltablename,"rejected","id={$id}");
        if($isApproved!=1 && $isRejected!=1){
            $sql  ="DELETE FROM  {$this->fulltablename} WHERE id={$id} ";
            DB_query($sql);
            return true;
        }else{
            return false;
        }

    }


   function lockTimesheetEntries($datestamp, $uid=0){
        global $_USER;
        if($uid==0) $uid=$_USER['uid'];
        $datestamp=intval($datestamp);

        $sql="UPDATE {$this->fulltablename} SET locked=1 WHERE datestamp='{$datestamp}' AND uid='{$uid}'";
        DB_query($sql);
    }

    function unlockTimesheetEntries($datestamp, $uid=0){
        global $_USER;
        if($uid==0) $uid=$_USER['uid'];
        $datestamp=intval($datestamp);


        $sql="SELECT approved FROM {$this->fulltablename} WHERE datestamp='{$datestamp}' AND uid='{$uid}' ORDER BY id ASC LIMIT 1";
        $res=DB_query($sql);
        list($isapproved)=DB_fetchArray($res);
        if(intval($isapproved)==0){
            $sql="UPDATE {$this->fulltablename} SET locked=0 WHERE datestamp='{$datestamp}' AND uid='{$uid}'";
            DB_query($sql);
        }
    }



    function approveSingleItem($id){
        $sql="UPDATE {$this->fulltablename} set approved=1, rejected=0 where id={$id}";
        $res=DB_query($sql,1);
        if (DB_error ()) {
            return false;
        }else{
            return true;
        }
    }


    function unapproveSingleItem($id){
        $sql="UPDATE {$this->fulltablename} set approved=0, rejected=0 where id={$id}";
        $res=DB_query($sql,1);
        if (DB_error ()) {
            return false;
        }else{
            return true;
        }
    }


    function approveRange($uid, $start, $end){
        if($start!=0 && $start!='' && $end!=0 && $end!=''){
            $sql="UPDATE {$this->fulltablename} set approved=1, rejected=0 where uid={$uid} and datestamp>={$start} and datestamp<={$end}";
            $res=DB_query($sql,1);
            if (DB_error ()) {
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }


    function unApproveRange($uid, $start, $end){
        if($start!=0 && $start!='' && $end!=0 && $end!=''){
            $sql="UPDATE {$this->fulltablename} set approved=0, rejected=0 where uid={$uid} and datestamp>={$start} and datestamp<={$end}";
            $res=DB_query($sql,1);
            if (DB_error ()) {
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }




    function rejectSingleItem($id,$comment){
        $sql="UPDATE {$this->fulltablename} set rejected=1, approved=0, rejected_comment='{$comment}' where id={$id}";
        $res=DB_query($sql,1);
        if (DB_error ()) {
            return false;
        }else{
            return true;
        }
    }


    function unrejectSingleItem($id){
        $sql="UPDATE {$this->fulltablename} set rejected=0 where id={$id}";
        $res=DB_query($sql,1);
        if (DB_error ()) {
            return false;
        }else{
            return true;
        }
    }


    function getDateStampFromID($id){
        $sql="SELECT datestamp FROM {$this->fulltablename} WHERE id={$id}";
        $res=DB_query($sql,1);
        list($datestamp)=DB_fetchArray($res);
        return $datestamp;
    }

    function getUIDFromID($id){
        $sql="SELECT uid FROM {$this->fulltablename} WHERE id={$id}";
        $res=DB_query($sql,1);
        list($uid)=DB_fetchArray($res);
        return $uid;

    }

    function getRejectionReasonById($id){
        $sql="SELECT rejected_comment FROM {$this->fulltablename} WHERE id={$id}";
        $res=DB_query($sql,1);
        list($reason)=DB_fetchArray($res);
        return $reason;

    }

    function getTotalHRSFromID($id){
        $sql  ="SELECT (regular_time+time_1_5+time_2_0+vacation_time_used+stat_time+floater+sick_time+bereavement+jury_duty+adjustment) as tot ";    //removed +evening_hours+unpaid_hrs+other from calculation july 31/08 ED
        $sql .=" FROM {$this->fulltablename} WHERE id={$id}";
        $res=DB_query($sql,1);
        list($tot)=DB_fetchArray($res);
        return $tot;
    }

    function getOTHRSFromID($id){
        $sql  ="SELECT (time_1_5+time_2_0) as tot ";
        $sql .=" FROM {$this->fulltablename} WHERE id={$id}";
        $res=DB_query($sql,1);
        list($tot)=DB_fetchArray($res);
        return $tot;
    }


    function getTotalHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid){
        $startDateStamp=$startDateStamp-3600;
        $endDateStamp=$endDateStamp+3600;

		$sql  ="SELECT sum(regular_time+time_1_5+time_2_0+evening_hours+stat_time+vacation_time_used+floater+sick_time+bereavement+jury_duty+unpaid_hrs+other+adjustment) as tot ";
        $sql .=" FROM {$this->fulltablename} WHERE uid={$uid} AND datestamp>={$startDateStamp} AND datestamp<={$endDateStamp}";
        $res=DB_query($sql,1);
        list($tot)=DB_fetchArray($res);
        return $tot;
    }

    function getRegularHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid){
        $sql  ="SELECT sum(regular_time+evening_hours+stat_time+vacation_time_used+floater+sick_time+bereavement+jury_duty+unpaid_hrs+other+adjustment) as tot ";
        $sql .=" FROM {$this->fulltablename} WHERE uid={$uid} AND datestamp>={$startDateStamp} AND datestamp<={$endDateStamp}";
        $res=DB_query($sql,1);
        list($tot)=DB_fetchArray($res);
        return $tot;
    }

    function getOTHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid){
        $sql  ="SELECT sum(time_1_5+time_2_0) as tot ";
        $sql .=" FROM {$this->fulltablename} WHERE uid={$uid} AND datestamp>={$startDateStamp} AND datestamp<={$endDateStamp}";
        $res=DB_query($sql,1);
        list($tot)=DB_fetchArray($res);
        return $tot;
    }


    function selectByProjectHoursByUser($uid,$startStamp,$endStamp){
        global $CONF_NEXTIME;
        $sql  ="SELECT project_id,sum(regular_time+time_1_5+time_2_0+evening_hours+stat_time+vacation_time_used+floater+sick_time+bereavement+jury_duty+unpaid_hrs+other+adjustment) as tot  ";
        $sql .=" FROM {$this->fulltablename} WHERE uid={$uid} AND datestamp>='{$startStamp}' AND datestamp<='{$endStamp}' ";
        $sql .="GROUP BY project_id ";
        $res=DB_query($sql,1);
        $retarray=array();
        while($A=DB_fetchArray($res)){
            $projnumber=nexlistValue($CONF_NEXTIME['nexlist_nextime_projects'],$A['project_id'],0);
            if($projnumber=='') $projnumber='Misc. ';
            //if we have an identical key.. don't overwrite it!  tally it up
            if(array_key_exists($projnumber,$retarray)){
                $retarray["$projnumber"]+= $A['tot']  ;
            }else{
                $retarray["$projnumber"]= $A['tot']  ;
            }
            //com_errorlog("$projnumber {$A['tot']}");
        }
       return $retarray;
    }


    function getRejectedItems($uid){
        global $CONF_NEXTIME;

        $sql ="SELECT id FROM {$this->fulltablename} WHERE uid={$uid} AND rejected=1";
        $res=DB_query($sql);
        $retarray=array();
        for($cntr=0;$cntr<DB_numRows($res);$cntr++){
            $A=DB_fetchArray($res);
            $retarray[$cntr]=$A['id'];
        }
        return $retarray;

    }

    function getItemsSubmittedBySomeoneElse($uid){
        global $CONF_NEXTIME;

        $sql ="SELECT id FROM {$this->fulltablename} WHERE modified_by_uid<>{$uid} AND uid={$uid} AND modified_by_uid<>0 AND ack_modified=0";
        $res=DB_query($sql);
        $retarray=array();
        for($cntr=0;$cntr<DB_numRows($res);$cntr++){
            $A=DB_fetchArray($res);
            $retarray[$cntr]=$A['id'];
        }
        return $retarray;

    }

    //set the ack_modified flag to 1 to signify that the end user knows that their
    //entry has been altered by someone
    function setAcknowledgedModified($startStamp,$endStamp,$uid){
		$sql="UPDATE {$this->fulltablename} SET ack_modified=1 WHERE uid={$uid} AND (datestamp>={$startStamp} OR datestamp>=({$startStamp}-3600)) AND (datestamp<={$endStamp} OR datestamp<=({$endStamp}+3600) )";
        DB_query($sql);
        if(DB_error()){
            return false;
        }else{
            return true;
        }
    }

    // updates a single field in a single record with specified value
    // written for use with the adjustments
    function set_specific_column_value($id, $field, $value) {
        $sql = "UPDATE {$this->fulltablename} SET `".$field."`='".$value."' WHERE `id`='".$id."'";
        DB_query($sql);
    }

}


?>