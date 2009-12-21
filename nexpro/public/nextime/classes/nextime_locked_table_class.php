<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Sept. 23, 2009                                                      |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nextime_locked_table_class.php - class to manage the locked table         |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |  Author: Randy Kolenko   - randy@nextide.ca                               |
// +---------------------------------------------------------------------------+
// | Extends the Base table class and provides lock table methods              |
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

class TABLE_nextime_locked_timesheets extends TABLE{
    var $fulltablename;     // table name
    var $tablename;         // alias name used when accessing this class so not to use index identifiers
    var $fieldlist;         // list of fields in this table
    var $fieldvals;         // list of values for fields
    var $numberOfColumns;   // holds the number of columns dynamically determined in this variable
    var $resultSet;         // holds a result set
    var $rowsReturned;      //holds the number of rows from the result set

    //constructor
    function TABLE_nextime_locked_timesheets(){
        global $_TABLES;
        $this->fulltablename   = $_TABLES['nextime_locked_timesheets'];
        $this->tablename       = 'nextime_locked_timesheets';
        $this->rows_per_page   = 10;
        $this->resultSet       = NULL;
        $this->rowsReturned    = 0;

        $this->fieldlist = array('uid','startdate','enddate');

        $this->numberOfColumns=count($this->fieldlist);
    }


    function lockTimesheet($startDateStamp,$endDateStamp,$uid){
        $this->unlockTimesheet($startDateStamp,$endDateStamp,$uid);
        $sql="INSERT INTO {$this->fulltablename}  (uid, startdate, enddate) values ('$uid','$startDateStamp','$endDateStamp')";
        DB_query($sql);
        if(DB_error()){
            return false;
        }else{
            return true;
        }
    }

    function unlockTimesheet($startDateStamp,$endDateStamp,$uid){
        $lockedRanges = array();
        $errorMargin = 4 * 3600; // allow for 8 hour deviation (4 both ways), should be ok since all times should be around nidnight anyways
        $sql = "SELECT `startdate`, `enddate` FROM {$this->fulltablename} WHERE `uid`={$uid}";
        $result = DB_query($sql);
        while ($data = DB_fetchArray($result)) {
            if ((($startDateStamp >= $data['startdate'] - $errorMargin) && ($startDateStamp <= $data['startdate'] + $errorMargin)) && (($endDateStamp >= $data['enddate'] - $errorMargin) && ($endDateStamp <= $data['enddate'] + $errorMargin))) {
                $lockedRanges[] = array('startdate'=>$data['startdate'], 'enddate'=>$data['enddate']);
            }
        }
        // this should probably only ever have one entry, but just in case there are issues with setting the timestamps and they're slightly off, we'll loop
        if ($lockedRanges) {
            $sql = "DELETE FROM {$this->fulltablename} WHERE `uid`={$uid} AND (";
            foreach ($lockedRanges as $range) {
                $sql .= "(`startdate`={$range['startdate']} AND `enddate`={$range['enddate']}) OR ";
            }
            $sql = substr($sql, 0, -4).")";
        }
        //$sql="DELETE FROM {$this->fulltablename} WHERE uid={$uid} AND startdate={$startDateStamp} AND enddate={$endDateStamp}";
        DB_query($sql);
        if(DB_error()){
            return false;
        }else{
            return true;
        }
    }


    function determineIfItemIsInLockRange($id){
        global $_TABLES,$CONF_NEXTIME,$_USER;
        $datestamp=DB_getItem($_TABLES['nextime_timesheet_entry'],"datestamp","id={$id}");
        $uid=DB_getItem($_TABLES['nextime_timesheet_entry'],"uid","id={$id}");
        $sql="SELECT * FROM {$this->fulltablename} WHERE uid={$uid} AND startdate<={$datestamp} AND enddate>={$datestamp} ";
        $res=DB_query($sql);
        $nrows=DB_numRows($res);
        if($nrows>0){
            return true;
        }else{
            return false;
        }

    }

    function determineIfItemIsInLockRangeByDateStamp($datestamp,$uid){
        $sql="SELECT * FROM {$this->fulltablename} WHERE uid={$uid} AND startdate<={$datestamp} AND enddate>={$datestamp} ";
        $res=DB_query($sql);
        $nrows=DB_numRows($res);
        if($nrows>0){
            return true;
        }else{
            return false;
        }
    }

}


?>