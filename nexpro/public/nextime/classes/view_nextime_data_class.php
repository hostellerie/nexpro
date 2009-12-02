<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Sept. 23, 2009                                                      |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | view_nextime_data_class.php - view overlay for the timesheet system       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |  Author: Randy Kolenko   - randy@nextide.ca                               |
// +---------------------------------------------------------------------------+
// | View class that extends the Base table class and provides view-like       |
// | methods to use in the timesheet system                                    |
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

class VIEW_nextime_data extends TABLE{
    var $fulltablename;     // table name
    var $tablename;         // alias name used when accessing this class so not to use index identifiers
    var $fieldlist;         // list of fields in this table
    var $fieldvals;         // list of values for fields
    var $numberOfColumns;   // holds the number of columns dynamically determined in this variable
    var $resultSet;         // holds a result set
    var $rowsReturned;      //holds the number of rows from the result set

    //constructor
    function VIEW_nextime_data(){
        global $_TABLES;
        $this->fulltablename   = '';
        $this->tablename       = 'view_nextime_data';
        $this->rows_per_page   = 10;
        $this->resultSet       = NULL;
        $this->rowsReturned    = 0;

        $this->fieldlist = array('id','uid','nextime_activity_id','task_id','project_id','regular_time',
        'time_1_5','time_2_0','evening_hours','standby','vacation_time_used','floater','sick_time',
        'bereavement','jury_duty','unpaid_hrs','other','comment','datestamp','locked','approved','rejected',
        'rejected_comment','modified_by_uid','rejected_by_uid',
        'tech_number','region','fullname','maxuserdt', 'minuserdt',
        'locked_by_supervisor');

        $this->numberOfColumns=count($this->fieldlist);
    }


    //based on id, datestamp and uid, we can determine if this is all rows, 1 specific row, or many rows for 1 date for 1 user
    function get_timesheet_approval_rows($idlist='', $showApproved=false, $showOnlyApproved=false, $showOnlyRejected=false){
        global $_TABLES, $_DB_table_prefix;
        //get a row whose user ids are in this list
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
        $sql  ="SELECT ";

        $sql .="a.id,a.uid,a.nextime_activity_id,a.task_id,a.project_id,a.regular_time,a.time_1_5,";
        $sql .="a.time_2_0,a.evening_hours,a.standby,a.vacation_time_used,a.floater,a.sick_time,";
        $sql .="a.bereavement,a.jury_duty,a.unpaid_hrs,a.other,a.comment,a.datestamp,a.locked,a.approved,a.rejected,";
        $sql .="b.region, b.tech_number,  ";
        $sql .="c.fullname, max(d.datestamp) as maxuserdt, min(d.datestamp) as minuserdt, ";
        $sql .="case when e.startdate is null then 'FALSE' else 'TRUE' end as locked_by_supervisor ";
        $sql .="FROM {$_TABLES['nextime_timesheet_entry']} a ";
        $sql .="LEFT OUTER JOIN {$_TABLES['nextime_extra_user_data']} b ON a.uid = b.uid ";
        $sql .="LEFT OUTER JOIN {$_TABLES['users']} c ON a.uid = c.uid ";
        $sql .="INNER JOIN {$_TABLES['nextime_timesheet_entry']} d on a.uid=d.uid ";
        $sql .="LEFT OUTER JOIN ".$_DB_table_prefix."nxtime_locked_timesheets e on (a.uid=e.uid and a.datestamp>=e.startdate and a.datestamp<=e.enddate) ";
        $sql .="WHERE a.uid ";
        $sql .="IN ({$idlist})  {$approvedClause} ";
        $sql .="GROUP BY ";
        $sql .="a.id,a.uid,a.nextime_activity_id,a.task_id,a.project_id,a.regular_time,a.time_1_5,";
        $sql .="a.time_2_0,a.evening_hours,a.standby,a.vacation_time_used,a.floater,a.sick_time,";
        $sql .="a.bereavement,a.jury_duty,a.unpaid_hrs,a.other,a.comment,a.datestamp,a.locked,a.approved,a.rejected,";
        $sql .="b.region, b.tech_number, c.fullname ";
        $sql .="ORDER BY a.UID ASC , a.datestamp ASC  ";
        //$sql .="ORDER BY  a.datestamp ASC  ";

        $this->resultSet=DB_query($sql);
        $this->rowsReturned=DB_numRows($this->resultSet);
        $this->fetchNextRow();
    }


    //returns an array denoting number of items in [0] and then number of approved items in [1] and number of rejected items in [2]
    function checkForAllApprovedInRange($uid,$start,$end){
        global $_TABLES;
        $sql  ="SELECT count(id) from {$_TABLES['nextime_timesheet_entry']} a ";
        $sql .="WHERE uid={$uid} and datestamp>={$start} and datestamp<={$end}";
        $res=DB_query($sql);
        list($count)=DB_fetchArray($res);

        $sql  ="SELECT count(id) from {$_TABLES['nextime_timesheet_entry']} a ";
        $sql .="WHERE uid={$uid} and datestamp>={$start} and datestamp<={$end} and approved=1";
        $res=DB_query($sql);
        list($appcount)=DB_fetchArray($res);

        $sql  ="SELECT count(id) from {$_TABLES['nextime_timesheet_entry']} a ";
        $sql .="WHERE uid={$uid} and datestamp>={$start} and datestamp<={$end} and rejected=1";
        $res=DB_query($sql);
        list($rejcount)=DB_fetchArray($res);

        return array(0=>"$count", 1=>"$appcount", 2=>"$rejcount");
    }

    function getEmployeeNumber($uid){
        global $_TABLES;
        return DB_getItem($_TABLES['nextime_extra_user_data'],'tech_number','uid='.$uid);
    }


    //this method returns only the OPTION tags and the supervisors within that group
    //excludes the ROOT users explicitly
    function getSupervisorsDropDownList(){
        global $_TABLES, $CONF_NEXTIME;

        $sql  ="SELECT a.ug_uid, b.fullname FROM {$_TABLES['group_assignments']} a ";
        $sql .="INNER JOIN {$_TABLES['users']} b on a.ug_uid=b.uid ";
        $sql .="WHERE (a.ug_main_grp_id={$CONF_NEXTIME['supervisor_group_id']} or a.ug_main_grp_id=1) and a.ug_uid>2 GROUP BY a.ug_uid, b.fullname ORDER BY b.fullname ASC";
        $res=DB_query($sql);

        //$output='<option value="">All</option>';
        $output='';
        while($A=DB_fetchArray($res)){
            //test if the ug_uid is in the root group.. if not, add that as a drop down item
            //if(!SEC_inGroup('Root',$A['ug_uid'])){
                $output .="<option value='{$A['ug_uid']}'>{$A['fullname']}</option>";
            //}
        }
        return $output;
    }



    function getSupervisorsUIDList(){
        global $_TABLES, $CONF_NEXTIME;

        $sql  ="SELECT a.ug_uid, b.fullname FROM {$_TABLES['group_assignments']} a ";
        $sql .="INNER JOIN {$_TABLES['users']} b on a.ug_uid=b.uid ";
        $sql .="WHERE a.ug_main_grp_id={$CONF_NEXTIME['supervisor_group_id']} GROUP BY a.ug_uid, b.fullname ORDER BY b.fullname ASC";
        $res=DB_query($sql);
        $output='';
        while($A=DB_fetchArray($res)){
            //test if the ug_uid is in the root group.. if not, add that as a drop down item
            //if(!SEC_inGroup('Root',$A['ug_uid'])){
                if($output!='') $output .=',';
                $output .=$A['ug_uid'];
            //}
        }
        return $output;
    }


}


?>