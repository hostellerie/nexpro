<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nextime.class.php                                                         |
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

//this base class provides all of the base business logic for the nexTime plugin

require_once("classes/nextime_timesheet_table_class.php");
require_once("classes/view_nextime_data_class.php");
require_once("classes/nextime_locked_table_class.php");
require_once ($_CONF['path_html'].'lib-common.php');

class nexTime{

    var $_tables;  //this is the nifty dynamic table array
    var $_numberOfTables=0;

    //constructor
    function nexTime(){
        global $CONF_NEXTIME;

        //set up our base tables API
        $this->_tables=array();
        for($cntr=0;$cntr<count($CONF_NEXTIME['table_class_array']);$cntr++){
            $this->_tables[$cntr]=new $CONF_NEXTIME['table_class_array'][$cntr]();
            $this->_tables[$cntr]->clear_column_values(NULL);
            $this->_tables["{$this->_tables[$cntr]->tablename}"]=&$this->_tables[$cntr]; //pointer to the table obj
        }
        $this->_numberOfTables=count($this->_tables);
        //done setting up tables.
    }//end constructor



    //generates timesheet rows based on the starting day timestamp (integer) and the number of days to show.
    //example, if you give a timestamp, and then $number_of_days_to_show=5, you will generate 5 rows of a timesheet
    //however the output will be bounded by a sunday to saturday set.  Thus you may get the display chopped into multiple segments.
    function generateTimesheetRows($whichUser, $starting_day_timestamp, $number_of_days_to_show, $TEMPLATE=NULL, $rowcounter=0, $disable=FALSE, $isApproval=FALSE, &$totalHours=0){
        global $LANG_NEXTIME,$CONF_NEXTIME,$_TABLES,$_CONF,$_USER;
            if($number_of_days_to_show>30 && $TEMPLATE==NULL) {
                $output ="<div class='cutOffAt30Message'>{$LANG_NEXTIME['cut_off_at_30']}</div>";
                $number_of_days_to_show=30;
            }else{
                $output='';
            }

            $whichUser=intval($whichUser);
            if($whichUser==0){
                $whichUser=$_USER['uid'];
            }

            $timesheetIsLocked = $this->determineIfItemIsInLockRangeByDateStamp($starting_day_timestamp, $whichUser);

            $numberDayOfWeek=strftime("%w",$starting_day_timestamp);
            $header_display_flag=0;
            $thisDay=$starting_day_timestamp;
            $dayOffset=24*60*60;
            //$totalHours=0;
            $OThours=0;
            $fieldsToSum = array(
                'regular_time'=>0,
                'time_1_5'=>0,
                'time_2_0'=>0,
                'evening_hours'=>0,
                'stat_time'=>0,
                'vacation_time_used'=>0,
                'floater'=>0,
                'sick_time'=>0,
                'bereavement'=>0,
                'jury_duty'=>0,
                'other'=>0,
                'placeholder'=>'',
                'total'=>0,
                'ot'=>0,
                'unpaid_hrs'=>0
            );
            if ($timesheetIsLocked) {
                $fieldsToSum['adjustment'] = 0;
            }
            $totals = array('week1'=>$fieldsToSum, 'week2'=>$fieldsToSum, 'grand'=>$fieldsToSum);
            $currentWeek = 'week1';
            for($cntr=0;$cntr<$number_of_days_to_show;$cntr++){
                //first, lets convert the timestamp to a date,
                //then we re-convert to an int.  if the int is off (most likely by 3600 seconds)
                //we have a time change here.....
                $testdate=strftime("%Y/%m/%d 00:00:00",$thisDay);
                $testintdate=strtotime($testdate);
                //if($testintdate!=$thisDay){
                //    $thisDay=$testintdate;
                //}

                if($testintdate==$thisDay-3600){
                    $thisDay=$testintdate;
                }elseif($testintdate==$thisDay){

                }else{
                    $thisDay+=3600;
                }

                $T = new Template($CONF_NEXTIME['template_path']);
                if($TEMPLATE==NULL){ //set up the main row to show
                    $T->set_file (array ('row'        => 'timesheet_entry_row.thtml'  ));
                    $this->_tables['nextime_timesheet_entry']->get_timesheet_rows($thisDay,$whichUser);
                }else{//set up the secondary row to show
                    $T->set_file (array ('row'        => 'timesheet_entry_row_multi_tasks.thtml'  ));
                    $this->_tables['nextime_timesheet_entry']->fetchNextRow();
                }
                if($numberDayOfWeek==0 and $cntr>0){ //echo out the footer and then the header
                    $totals[$currentWeek]['total'] = $totalHours;
                    $totals[$currentWeek]['ot'] = $OThours;
                    $totals['grand']['total'] += $totalHours;
                    $totals['grand']['ot'] += $OThours;
                    if($timesheetIsLocked){
                        $output.=$this->generateTableFooter(true,$totalHours,$OThours, $totals[$currentWeek], array(), $timesheetIsLocked);
                        $output.=$this->generateTableHeader(true, $isApproval, $timesheetIsLocked);
                    }else{
                        $output.=$this->generateTableFooter(false,$totalHours,$OThours, $totals[$currentWeek], array(), $timesheetIsLocked);
                        $output.=$this->generateTableHeader(false, $isApproval, $timesheetIsLocked);
                    }
                    $currentWeek = 'week2';
                    $totalHours=0;
                    $OThours=0;
                }//all common display items
                $today=strftime("%w",$thisDay);
                $T->set_var('site_url',$_CONF['site_url']);
                $T->set_var($LANG_NEXTIME);
                $T->set_var('rownumber',$rowcounter);
                $dayColour = 'normal_days';
                if($today == 0 || $today == 6) {// 0 or 6 (sunday or saturday then change colour
                    $dayColour = 'weekend_days'; //d8e3ee';
                }
                $T->set_var('day_colour',$dayColour);
                $T->set_var('human_date_day',$LANG_NEXTIME[$CONF_NEXTIME['day_offsets'][$today]]);
                $T->set_var('human_date_formatted',date("Y/m/d",$thisDay));

                $T->set_var('nextime_activity_options',$this->getActivitiesDropDown($this->_tables['nextime_timesheet_entry']->fieldvals['nextime_activity_id']));

                // modified next 2 calls to include the fieldvals project_id and task_id as arguments, to allow correctly repopulating lists based on activity selected
                $T->set_var('project_options',$this->getProjectDropDownFromActivityID($this->_tables['nextime_timesheet_entry']->fieldvals['nextime_activity_id'], $this->_tables['nextime_timesheet_entry']->fieldvals['project_id']));
                $T->set_var('task_options',$this->getTaskDropDownFromActivityID($this->_tables['nextime_timesheet_entry']->fieldvals['nextime_activity_id'], $this->_tables['nextime_timesheet_entry']->fieldvals['task_id']));

                $T->set_var('OThrs',$this->getOTHRSFromID($this->_tables['nextime_timesheet_entry']->fieldvals['id']));
                $T->set_var('totalhrs',$this->getTotalHRSFromID($this->_tables['nextime_timesheet_entry']->fieldvals['id']));
                $totalHours+=$this->getTotalHRSFromID($this->_tables['nextime_timesheet_entry']->fieldvals['id']);
                $OThours+= $this->getOTHRSFromID($this->_tables['nextime_timesheet_entry']->fieldvals['id']);
                $T->set_var('locked_or_not','lock.png');
                if($isApproval){
                    $T->set_var('display_for_approval','');
                }else{
                    $T->set_var('display_for_approval','none');
                }
                $T->set_var('enable_on_lock', 'display:none;');
                if ($timesheetIsLocked) {
                    $T->set_var('enable_on_lock', '');
                }
                if($this->_tables['nextime_timesheet_entry']->fieldvals['locked']=='1' || $this->_tables['nextime_timesheet_entry']->fieldvals['approved']=='1' || $disable){
                    if(!$isApproval){
                        $T->set_var('disable_on_lock',' disabled ');
                        //$T->set_var('disabled_style',' display:none; ');
                        $T->set_var('adj_lock', ' readonly ');
                        if($this->_tables['nextime_timesheet_entry']->fieldvals['locked']=='1'){
                            $T->set_var('locked_or_not','lock.png');
                            $T->set_var('js_disable_on_lock','locked');
                        }else{
                            $T->set_var('locked_or_not','lock_add.png');
                            $T->set_var('js_disable_on_lock','');
                        }
                    }else{
                        $T->set_var('disable_on_lock','');
                        if($this->_tables['nextime_timesheet_entry']->fieldvals['locked']=='1'){
                            $T->set_var('locked_or_not','lock.png');
                            $T->set_var('js_disable_on_lock','locked');
                        }else{
                            $T->set_var('locked_or_not','lock_add.png');
                            $T->set_var('js_disable_on_lock','');
                        }



                    }

                }else{
                    $T->set_var('locked_or_not','lock_add.png');
                    $T->set_var('disable_on_lock','');
                    $T->set_var('adj_lock', '');
                }

                //now to test for the overriding fact that this timesheet range has been locked!
                //if the following method returns true, we set the disable_on_lock to disabled

                if($this->determineIfItemIsInLockRangeByDateStamp($thisDay,$whichUser)){
                    $T->set_var('disable_on_lock',' disabled ');
                    $T->set_var('disabled_style',' display:none; ');
                    $T->set_var('locked_or_not','lock.png');
                    $T->set_var('adj_lock', ' readonly ');
                }

                if($this->_tables['nextime_timesheet_entry']->fieldvals['rejected']=='1'){
                    $T->set_var('rejected_style','background-color:red;');
                    $T->set_var('rejected_note',$LANG_NEXTIME['rejected_note']);
                    $T->set_var('chkreject_checked','CHECKED');
                }else{
                    $T->set_var('rejected_style','');
                    $T->set_var('rejected_note','');
                    $T->set_var('chkreject_checked','');
                }
                if($this->_tables['nextime_timesheet_entry']->fieldvals['approved']=='1'){
                    $T->set_var('rejected_style','background-color:green;');
                    $T->set_var('rejected_note',$LANG_NEXTIME['approved_note']);
                    $T->set_var('chkapproval_checked','CHECKED');
                }else{
                    $T->set_var('chkapproval_checked','');
                }
                if($this->_tables['nextime_timesheet_entry']->fieldvals['rejected']=='1' && !$isApproval){
                    $T->set_var('display_for_entry_rejection','');
                }else{
                    $T->set_var('display_for_entry_rejection','none');
                }

                // add current row values to the sums
                $currentRow = $this->_tables['nextime_timesheet_entry']->fieldvals;
                foreach (array_diff(array_keys($fieldsToSum), array('placeholder', 'total', 'ot')) as $field) {
                    $totals[$currentWeek][$field] += $currentRow[$field];
                    $totals['grand'][$field] += $currentRow[$field];
                }

                if($TEMPLATE==NULL){ //we're in the main body to show specific main row vs. secondary row data
                    //nextime_activity_options

                    if($this->_tables['nextime_timesheet_entry']->rowsReturned==1){
                       $T->set_var('rowspan_value','');
                       $T->set_var($this->_tables['nextime_timesheet_entry']->fieldvals);   //fieldvals is an array of values with actual table field names
                       $T->parse('output','row',true);
                       $output.= $T->finish($T->get_var('output'));
                       $rowcounter+=1;
                    }elseif($this->_tables['nextime_timesheet_entry']->rowsReturned>1){//more than one task for this day here.....
                        $T->set_var($this->_tables['nextime_timesheet_entry']->fieldvals);   //fieldvals is an array of values with actual table field names
                        //we must echo out a new row, but without the date column.  Thus this is a special case
                        $T->set_var('rowspan_value',' rowspan="'.$this->_tables['nextime_timesheet_entry']->rowsReturned.'" ');
                        $T->parse('output','row',true);
                        $output.= $T->finish($T->get_var('output'));
                        $rowcounter+=1;
                        //we loop to rowsReturned-1 because we've already peeled off the first row for the top display row
                        for($looprows=0;$looprows<($this->_tables['nextime_timesheet_entry']->rowsReturned-1);$looprows++){
                            $retval=$this->generateTimesheetRows($whichUser,$thisDay, 1, TRUE,$rowcounter, $disabled, $isApproval, $totalHours);//timesheet_entry_row_multi_tasks.thtml
                            $OThours += $retval[3];
                            foreach (array_diff(array_keys($fieldsToSum), array('ot', 'total', 'placeholder')) as $field) {
                                $totals[$currentWeek][$field] += $retval[5][$field];
                                $totals['grand'][$field] += $retval[5][$field];
                            }
                            $rowcounter=$retval[0];
                            $output.=$retval[1];
                        }
                    }else{//nothing to display.. just carry on
                        $T->set_var('datestamp',$thisDay);
                        $T->parse('output','row',true);
                        $output.= $T->finish($T->get_var('output'));
                        $rowcounter+=1;
                    }
                }else{  //for all intents and purposes, we're here because we have a secondary template to show
                    $T->set_var($this->_tables['nextime_timesheet_entry']->fieldvals);   //fieldvals is an array of values with actual table field names
                    $T->parse('output','row',true);
                    $output.= $T->finish($T->get_var('output'));
                    $rowcounter+=1;
                }
                $thisDay+=$dayOffset;
                $numberDayOfWeek++;
                if($numberDayOfWeek>6) $numberDayOfWeek=0;
            }

        $totals[$currentWeek]['total'] = $totalHours;
        $totals[$currentWeek]['ot'] = $OThours;
        $totals['grand']['total'] += $totalHours;
        $totals['grand']['ot'] += $OThours;

        $retarray=array(0=>$rowcounter,1=>"$output",2=>"$totalHours",3=>"$OThours", 4=>$totals[$currentWeek], 5=>$totals['grand']);
        return $retarray;
    }

    // added the isLocked param, it's the result of running determineIfItemIsInLockRangeByDateStamp(), used for the adjustment field
    function generateTableHeader($disable=false, $isApproval=false, $isLocked = false){
        global $CONF_NEXTIME,$LANG_NEXTIME_HEADER,$_CONF;
        //header of the output
        $T = new Template($CONF_NEXTIME['template_path']);
        $T->set_file (array (
            'header'        => 'timesheet_entry_header.thtml'
            ));
        $T->set_var($LANG_NEXTIME_HEADER);
        $T->set_var('site_url',$_CONF['site_url']);
        if(!$disable){
            $T->set_var('onclick','YAHOO.nextide.container.panel1.show();');
        }
        $T->set_var('enable_on_lock', ';display:none');
        //if (($isLocked) && ((SEC_inGroup('nexTime Finance')) || (SEC_inGroup('nexTime Supervisors')))) {
        if($isLocked){
            $T->set_var('enable_on_lock', '');
        }
        if($isApproval){
            $T->set_var('header_item_for_approval',"<th class='verticalText'>{$LANG_NEXTIME_HEADER['approval_col']}</th>");
            $T->set_var('header_item_for_reject',"<th class='verticalText'>{$LANG_NEXTIME_HEADER['reject_col']}</th>");
        }else{
            $T->set_var('header_item_for_approval','');
            $T->set_var('header_item_for_reject',"");
        }
        $T->parse('output','header');
        return $T->finish($T->get_var('output'));
    }

    // added the isLocked param, it's the result of running determineIfItemIsInLockRangeByDateStamp(), used for the adjustment field
    function generateTableFooter($disable=false,$totalhours=0, $othours=0, $weeklytotals = array(), $grandtotals = array(), $isLocked = false){
        global $CONF_NEXTIME,$LANG_NEXTIME_HEADER,$_CONF;
        $T = new Template($CONF_NEXTIME['template_path']);
        $T->set_file (array (
            'footer'        => 'timesheet_entry_footer.thtml'
            ));
        $T->set_var($LANG_NEXTIME_HEADER);
        $T->set_var('site_url',$_CONF['site_url']);
        if($disable==true) {
            $T->set_var('disabled_style','none');
        }else{
            $T->set_var('disabled_style','');
        }
        if (($isLocked) && ((SEC_inGroup('nexTime Finance')) || (SEC_inGroup('nexTime Supervisors')))) {
            $T->set_var('disabled_style', '');
        }else{
            if($isLocked){
                $T->set_var('disabled_style','none');
            }

        }
        // output weekly totals
        $T->set_var('hide_weekly_totals', 'display:none;');
        $weeklynumbers = '<td colspan=16></td>';
        if ($weeklytotals) {
            $T->set_var('hide_weekly_totals', '');
            $weeklynumbers = '';
            foreach ($weeklytotals as $field=>$sum) {
                if ($field != 'placeholder') {
                    $weeklynumbers .= '<td style="font-weight:bold">'.sprintf("%01.2f", $sum).'</td>';
                } else {
                    $weeklynumbers .= '<td style="font-weight:bold"></td>';
                }
            }
        }
        $T->set_var('weekly_numbers', $weeklynumbers);

        // output grand totals
        $T->set_var('hide_grand_totals', 'display:none;');
        $grandnumbers = '<td colspan=16></td>';
        if ($grandtotals) {
            $T->set_var('hide_grand_totals', '');
            $grandnumbers = '';
            foreach ($grandtotals as $field=>$sum) {
                if ($field != 'placeholder') {
                    $grandnumbers .= '<td style="font-weight:bold">'.sprintf("%01.2f", $sum).'</td>';
                } else {
                    $grandnumbers .= '<td style="font-weight:bold"></td>';
                }
            }
        }
        $T->set_var('grand_numbers', $grandnumbers);

        //$T->set_var('hours',number_format($totalhours+$othours, 2, '.', ''));
//        $T->set_var('hours',number_format($totalhours, 2, '.', ''));
        $T->parse('output','footer');
        return $T->finish($T->get_var('output'));
    }


    function generateTotalRowCount($rowcount){
        $ret='<input type="hidden" name="max_row_number" id="max_row_number" value="'.$rowcount.'">';
        $ret.='<input type="hidden" name="changes_flag" id="changes_flag" value="0">';
        return $ret;
    }



    function getTableColumns($table){
        return  $this->_tables['nextime_timesheet_entry']->returnColumns();
    }


    //simply pass in the current row you're looking for, and this function will set the table's values ready
    //for a commit
    function setDataFromPOST($rownumber){
        $this->_tables['nextime_timesheet_entry']->clear_column_values(NULL);
        foreach($this->_tables['nextime_timesheet_entry']->fieldlist as $col){
            //it would be good to filter these entries....

            $this->_tables['nextime_timesheet_entry']->set_column_value($col,$_POST[$col . $rownumber]);
        }

    }

    function saveAdjustment($id, $adjustment) {
        $this->_tables['nextime_timesheet_entry']->set_specific_column_value($id, 'adjustment', $adjustment);
    }

    //we're assuming that the data in the row is sound.. commit that data to the table handler
    function commitData($approvalUID, $forWhichUID){
        $ret=$this->_tables['nextime_timesheet_entry']->commitData($approvalUID, $forWhichUID);
        return $ret;
    }


    //using $list, we will delete these IDs
    function deleteEntries($list){
        $arr=explode(",",$list);
        $retval=true;
        foreach($arr as $id){
            $ret=$this->_tables['nextime_timesheet_entry']->deleteEntry($id);
            $retval=$retval&$ret;
        }
        return $retval;
    }


    function lockTimesheetEntries($datestamp, $uid=0){
        $uid=intval($uid);
           $this->_tables['nextime_timesheet_entry']->lockTimesheetEntries($datestamp, $uid);
    }

    function unlockTimesheetEntries($datestamp, $uid=0){
        $uid=intval($uid);
           $this->_tables['nextime_timesheet_entry']->unlockTimesheetEntries($datestamp, $uid);
    }


    //based on the passed in UID, we need to determine which users this person can approve timesheets for
    //this will generate an option list without the SELECT tags
    //nexTime admins see all users
    function getOptionListOfAssignedEmployees($supervisorUID, $suppressViewAll=false){
        global $_TABLES,$CONF_NEXTIME,$LANG_NEXTIME;
        $suppressViewAll=true;
        $thisuid=intval($supervisorUID);
        $output="";
        if(SEC_inGroup('nexTime Admin')){   //show all users for admins
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],0,'');
        }else{
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],0,'','1:' . $thisuid);
        }
        if(!$suppressViewAll){
            $output.='<option value=""></option><option value="0">View All</option>';
        }else{
            $output.='<option value="">'.$LANG_NEXTIME['empty_nexlist_dropdown_emp'].'</option>';
        }
        @asort($list);
        if(is_array($list)){
            foreach($list as $x=>$val){
                $thisuid=nexlistValue($CONF_NEXTIME['nexlist_employee_to_supervisor'],$x,0);
                $username=DB_getItem($_TABLES['users'],"fullname","uid={$thisuid}");
                $output.='<option value="'. $thisuid . '">' . $username. '</option>';
            }
        }
        return $output;
    }

    //based on the passed in UID, we need to determine which users this person can approve timesheets for
    //this will generate an option list without the SELECT tags
    //nexTime admins see all users
    function getOptionListOfDelegatedEmployees($supervisorUID, $suppressViewAll=false){
        global $_TABLES,$CONF_NEXTIME,$LANG_NEXTIME;
        $thisuid=intval($supervisorUID);
        $output="";
        if(SEC_inGroup('nexTime Admin')){   //show all users for admins
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],0,'');
        }else{
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_delegate'],0,'','1:' . $thisuid);
        }
        if(!$suppressViewAll){
            $output.='<option value=""></option><option value="0">View All</option>';
        }else{
            $output.='<option value="">'.$LANG_NEXTIME['empty_nexlist_dropdown_emp'].'</option>';
        }
        $listToUse = (SEC_inGroup('nexTime Admin')) ? $CONF_NEXTIME['nexlist_employee_to_supervisor'] : $CONF_NEXTIME['nexlist_employee_to_delegate'];
        @asort($list);
        if(is_array($list)){
            foreach($list as $x=>$val){
                $thisuid=nexlistValue($listToUse,$x,0);
                $username=DB_getItem($_TABLES['users'],"fullname","uid={$thisuid}");
                $output.='<option value="'. $thisuid . '">' . $username. '</option>';
            }
        }
        return $output;
    }

    //based on the passed in UID, we will determine the hierarchy of this user.
    //we are assuming tht the UID is a manager who has subordinates who in turn have employees
    //this method returns ONLY those employees which are associated with this manager's supervisors....
    function getOptionListOfHierarchyEmployees($managerUID){
        global $_TABLES,$CONF_NEXTIME,$LANG_NEXTIME;
        $thisuid=intval($managerUID);
        $output="";
        $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_manager_to_supervisor'],0,'','0:' . $thisuid);
        //now, this list potentially contains the supervisors list.
        //we now take THIS list, cycle through it and generate the option list of assigned employees.
        $output.='<option value="">'.$LANG_NEXTIME['empty_nexlist_dropdown_emp'].'</option>';
        if(is_array($list)){
            foreach($list as $x=>$val){
                $thisuid=nexlistValue($CONF_NEXTIME['nexlist_manager_to_supervisor'],$x,1);
                $username=DB_getItem($_TABLES['users'],"fullname","uid={$thisuid}");
                $output.='<option value="'. $thisuid . '">' . $username. '</option>';
            }
        }
        return $output;
    }


    //this method returns ONLY those employees which are supervisors
    function getSupervisorsDropDownList(){
        //i need to skip out the root users here.. ONLY those that are in this group and NOT root
        return $this->_tables['view_nextime_data']->getSupervisorsDropDownList();
    }

    function getSupervisorsUIDList(){
        return $this->_tables['view_nextime_data']->getSupervisorsUIDList();
    }

    function getAllUIDsWhichHaveSupervisors(){
        $supuidlist=$this->getSupervisorsUIDList();
        $uidlist='0';
        $suparray=explode(',',$supuidlist);
        foreach($suparray as $val){//loop thru each supervisor
            $templist='';
            $templist=$this->getCSVListOfAssignedEmployees($val,true);
            if($templist!=''){
                if($uidlist!='') $uidlist .=",";
                $uidlist.=$templist;
            }
        }

        return $uidlist;
    }


    //based on the passed in UID, we need to determine which users this person can approve timesheets for
    //this will generate a csv list
    //nexTime admins see all users
    function getCSVListOfAssignedEmployees($managerUID, $isManager){ //set isManager to false to signify that this is NOT a supervisor you're trying to get info on
        global $_TABLES,$CONF_NEXTIME;
        $thisuid=intval($managerUID);
        $output="";
        if( (SEC_inGroup('nexTime Admin') || SEC_inGroup('nexTime Finance') ) && !$isManager){   //show all users for admins but NOT if we're trying to only show this for a manager
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],0,'');
        }else{
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],0,'','1:' . $thisuid);
        }
        foreach($list as $x=>$val){
            $thisuid=nexlistValue($CONF_NEXTIME['nexlist_employee_to_supervisor'],$x,0);
            if($output!='') $output.=",";
            $output.=$thisuid;
        }
        return $output;
    }


    //this method takes in the userid trying to do the approval and the userid of the person they're trying to approve
    //returns true if they can approve or false if they shouldnt be approving
    function testIfUserCanApprove($useriddoingtheapproving, $useridtoapprove){
        global $CONF_NEXTIME, $_USER;
        $useriddoingtheapproving=intval($useriddoingtheapproving);
        if($useriddoingtheapproving==0){
            $useriddoingtheapproving=$_USER['uid'];
        }
        $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],0,'','0:'. $useridtoapprove .',1:' . $useriddoingtheapproving);
        if (count($list)<1) {
            $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_delegate'],0,'','0:'. $useridtoapprove .',1:' . $useriddoingtheapproving);
        }
        $isNextimeAdmin=SEC_inGroup('nexTime Admin',$useriddoingtheapproving);
        if(count($list)>0 || $isNextimeAdmin){
            return true;
        }else{
            return false;
        }
    }

    function getSupervisorUID($employeeUID){
         global $CONF_NEXTIME, $_USER;
         $list=nexlistOptionList('alist','',$CONF_NEXTIME['nexlist_employee_to_supervisor'],1,'','0:'. $employeeUID);
         foreach($list as $x=>$val){
             $thisuid=nexlistValue($CONF_NEXTIME['nexlist_employee_to_supervisor'],$x,1);
             return $thisuid;
         }
    }

    function getEmployeeNumber($uid){
        return $this->_tables['view_nextime_data']->getEmployeeNumber($uid);
    }

    function getUserFullName($uid){
        global $_TABLES;

        return DB_getItem($_TABLES['users'],'fullname','uid=' . $uid);
    }


    //based on the uidDoingApproval, this method will fetch the related timesheet entries
    //that are NOT approved for the specified $uidGettingApproved user.
    //if uidGettingApproved==0, then all current entries that are not approved for
    //the supervisor are shown
    function generateApprovalTimesheetRows($uidDoingApproval, $uidGettingApproved=0, $hidePartiallyApproved=false, $hideFullyApproved=false,$isManager=false){
        global $CONF_NEXTIME, $_USER, $_TABLES,$LANG_NEXTIME;
        $error='';
        $output='';
        $uidDoingApproval=intval($uidDoingApproval);
        if($uidDoingApproval==0){
            $uidDoingApproval=$_USER['uid'];
        }
        if($uidGettingApproved!=0){ //this is a specific user's items getting looked at
            $canApprove=$this->testIfUserCanApprove($uidDoingApproval, $uidGettingApproved);
            if(!$canApprove){
                $error="You're not authorized for approving this user's entries...";
                $output="";
                $retarray=array(0=>$error,1=>"$output");
                return $retarray;
            }
            $csvListOfUIDs=$uidGettingApproved;
        }else{
           $csvListOfUIDs=$this->getCSVListOfAssignedEmployees($uidDoingApproval,$isManager);
        }

        $userarr=@implode(",",$csvListOfUIDs);
        if(!is_array($userarr)) $userarr=array($csvListOfUIDs);
        $output="";
        $row=1;

        foreach($userarr as $userid){
            $userstdt=time()-$CONF_NEXTIME['approval_history_span'];
            $userenddt=time();
            $userstdt=intval($userstdt);
            $userenddt=intval($userenddt);

            if($userstdt==0) $userstdt=time()-$CONF_NEXTIME['approval_history_span'];
            if($userenddt==0) $userenddt=time();
            $output .="<table id=\"userinformation\" class=\"approvalTimesheetTable\"><tr><th colspan=5>" .DB_getItem("gl_users","fullname","uid=$userid"). "</th></tr><tr><th>Date From</th><th>Date to</th><th>Approval Info</th><th>Lock</th><th>Info</th></tr>";
                //now loop thru starting at the startdt

            $retDateArray=$this->generateSundayToSundayRange($userstdt);
            $rptstart=$retDateArray[0];
            $rptend=$retDateArray[1];

            while ($rptstart<=$userenddt){
                $s=strftime("%Y/%m/%d",$rptstart);
                $e=strftime("%Y/%m/%d",$rptend);
                $stats='';
                                        //get the total # of hours and put that in the stats...
                $stats.=floatval ($this->getTotalHrsFromStartAndEndDate($rptstart,$rptend,$userid)) . " {$LANG_NEXTIME['stats_note_5']}<br>";;
                $ret=$this->_tables['view_nextime_data']->checkForAllApprovedInRange($userid,$rptstart,$rptend);
                $tst=$this->determineIfItemIsInLockRangeByDateStamp($rptstart,$userid);

                if(intval($ret[0])===intval($ret[1]) && intval($ret[0])>0 && $tst==true){  //if (# of items = # approved) and # of items >0
                    $approved="<input type=checkbox checked onclick=\"lockItem('{$userid}','{$rptstart}','{$rptend}',this)\">";
                    if($stats!='') $stats.="<br>";
                    $stats.="{$LANG_NEXTIME['stats_note_3']}";
                }else{
                    if(intval($ret[1])>0){  //if there's more than one item approved
                        //we can use this scenario to trap whether to show this section of the timesheet output or not
                        if($ret[1]!=$ret[0]){
                            $approvalnote='Partially Approved';
                        }else{
                            $approvalnote='';
                        }

                        //RK - keep this in here as a visual marker
                        //RK - keep the checkbox enabled to do a blanket "approve all"
                        //$T->set_var('is_disabled',' DISABLED ');
                        if($stats!='') $stats.="<br>";
                        $stats.="{$ret[1]} {$LANG_NEXTIME['stats_note_4']}";
                    }else{  //catch all
                        $approvalnote='';
                    }
                    if(!$tst){
                        $approved="<input type=checkbox onclick=\"lockItem('{$userid}','{$rptstart}','{$rptend}',this)\">";
                    }else{
                        $approved="<input type=checkbox checked onclick=\"lockItem('{$userid}','{$rptstart}','{$rptend}',this)\">";
                    }
                }

                if($row==0){
                    $row=1;
                    $rowcolor="#a0a0a0";
                }else{
                    $row=0;
                    $rowcolor="#ffffff";
                }

                if($hideFullyApproved==false || $tst==false){
                    $output .= "<tr bgcolor=\"$rowcolor\"><td><a href='approvals.php?emp={$userid}&start_date={$s}&end_date={$e}&showAsTimesheet=1'>$s</a></td><td>$e</td><td>$stats</td><td>$approved</td><td>$approvalnote</td></tr>";
                }

                $retDateArray=$this->generateSundayToSundayRange($rptend+864001);

                $rptstart=$retDateArray[0];
                $rptend=$retDateArray[1];
            }
        }
        $output.= "</table>";
        $retarray=array(0=>$error,1=>"$output");
        return $retarray;
        }


    //simple data accessor method that uses an already fetched and referenced data array ($resource)
    //and passes that to another table's fetching mechanism
    //$resource is the fetched array, $arrayOfTables is an array of referenced tables that you'd like to fill with this data
    function setTableData($resource,$arrayOfTables){
        foreach($arrayOfTables as $table){
            $this->_tables[$table]->fetchNextRow(true,$resource);  //skip the automatic fetchNext
        }
    }

    //takes a single timesheet ID in as a parameter and attempts to set its approved flag to 1
    //returns true on success, false on failure
    function approveSingleItem($id){
        return $this->_tables['nextime_timesheet_entry']->approveSingleItem($id);
    }

    //takes a single timesheet ID in as a parameter and attempts to set its approved flag to 1
    //returns true on success, false on failure
    function unApproveSingleItem($id){
        return $this->_tables['nextime_timesheet_entry']->unapproveSingleItem($id);
    }


    //takes a single timesheet ID in as a parameter and attempts to set its rejected flag to 1
    //returns true on success, false on failure
    function rejectSingleItem($id,$comment){
        return $this->_tables['nextime_timesheet_entry']->rejectSingleItem($id,$comment);
    }

    //takes a single timesheet ID in as a parameter and attempts to set its rejected flag to 1
    //returns true on success, false on failure
    function unRejectSingleItem($id){
        return $this->_tables['nextime_timesheet_entry']->unrejectSingleItem($id);
    }


    function approveRange($emp, $start, $end){
        return $this->_tables['nextime_timesheet_entry']->approveRange($emp,$start,$end);
    }

    function unApproveRange($emp, $start, $end){
        return $this->_tables['nextime_timesheet_entry']->unApproveRange($emp,$start,$end);
    }

    //based on your datestamp you pass in, this method will return an array
    //[0] is the start date, [1] is the end
    function generateSundayToSundayRange($datestamp){
        global $CONF_NEXTIME;

        $retarray=array();
        $stamp=strtotime($CONF_NEXTIME['payroll_start_date']);
        $flag=true;
        $diff=(86400*$CONF_NEXTIME['payroll_date_span']);
        while($flag){
            if($datestamp>=$stamp || $datestamp>=($stamp-3600)){
                $retarray[0]=$stamp;
            }
            if($datestamp<$stamp && $datestamp<($stamp-3600)){
                $stamp=$stamp-86400;
                $retarray[1]=$stamp;
                $flag=false;
            }
            $stamp=$stamp+$diff;
        }
        return $retarray;
    }

    function getDateStampFromID($id){
        return $this->_tables['nextime_timesheet_entry']->getDateStampFromID($id);
    }

    function getUIDFromID($id){
        return $this->_tables['nextime_timesheet_entry']->getUIDFromID($id);
    }

    function getRejectionReasonById($id){
        return $this->_tables['nextime_timesheet_entry']->getRejectionReasonById($id);
    }

    function getActivitiesDropDown($selected){
        global $CONF_NEXTIME;

        $list=nexlistOptionList('options','',$CONF_NEXTIME['nexlist_nextime_activities'],0,$selected, '', -1, false, 'asc');
        return $list;
    }

    function getProjectDropDown($selected){
        global $CONF_NEXTIME,$LANG_NEXTIME;

        $selected=intval($selected);
        if($selected=='0'){
            $list="<option value='0'>{$LANG_NEXTIME['select_activity_option']}</option>";
        }else{
            $list=nexlistOptionList('options','',$CONF_NEXTIME['nexlist_nextime_projects'],0,$selected);
        }

        return $list;
    }

    function getExpandedProjectDropDown(){
        global $CONF_NEXTIME;

        $list=nexlistOptionList('options','',$CONF_NEXTIME['nexlist_nextime_projects'],0,false,'',-1,true,'asc');

        return $list;
    }

    function getProjectDropDownFromActivityID($id, $selected = false){
        global $CONF_NEXTIME,$LANG_NEXTIME;
        $id=intval($id);
        if($id=='0'){
            $list="<option value='0'>{$LANG_NEXTIME['select_activity_option']}</option>";
        }else{
            $list=nexlistOptionList('options','',$CONF_NEXTIME['nexlist_nextime_projects'],0,$selected,'4:'.$id, -1, false, 'asc');
        }
        return $list;
    }

    function getTaskDropDownFromActivityID($id, $selected = false){
        global $CONF_NEXTIME,$LANG_NEXTIME;
        $id=intval($id);
        if($id=='0'){
            $list="<option value='0'>{$LANG_NEXTIME['select_activity_option']}</option>";
        }else{
            $list=nexlistOptionList('options','',$CONF_NEXTIME['nexlist_timesheet_tasks'],1,$selected,'2:'.$id, -1, false, 'asc');
        }
        return $list;
    }


    function getTasksDropDown($selected,$noDefault=false,$mode='options'){
        global $CONF_NEXTIME;
        $list=nexlistOptionList($mode,'',$CONF_NEXTIME['nexlist_timesheet_tasks'],1,$selected,'',-1,$noDefault,'asc');
        return $list;
    }

    function getTotalHRSFromID($id){
        return  $this->_tables['nextime_timesheet_entry']->getTotalHRSFromID($id);
    }

    function getOTHRSFromID($id){
        return  $this->_tables['nextime_timesheet_entry']->getOTHRSFromID($id);
    }

    function selectByProjectHoursByUser($uid,$startStamp,$endStamp){
        return $this->_tables['nextime_timesheet_entry']->selectByProjectHoursByUser($uid,$startStamp,$endStamp);
    }

    function getTotalHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid){
        return $this->_tables['nextime_timesheet_entry']->getTotalHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid);
    }

    function getRegularHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid){
        return $this->_tables['nextime_timesheet_entry']->getRegularHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid);
    }

    function getOTHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid){
        return $this->_tables['nextime_timesheet_entry']->getOTHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid);
    }

    //returns all rejected IDs in an array
    function getRejectedItems($uid){
        return $this->_tables['nextime_timesheet_entry']->getRejectedItems($uid);
    }

     //returns all rejected IDs in an array
    function getItemsSubmittedBySomeoneElse($uid){
        return $this->_tables['nextime_timesheet_entry']->getItemsSubmittedBySomeoneElse($uid);
    }

    //returns true if the id's datestamp falls in the lock range
    //return false otherwise
    function determineIfItemIsInLockRange($id){
        $retval=$this->_tables['nextime_locked_timesheets']->determineIfItemIsInLockRange($id);
        return $retval;
    }

    //returns true if the id's datestamp falls in the lock range
    //return false otherwise
    function determineIfItemIsInLockRangeByDateStamp($dateStamp,$whichUser){
        $retval=$this->_tables['nextime_locked_timesheets']->determineIfItemIsInLockRangeByDateStamp($dateStamp,$whichUser);
        return $retval;
    }

    function lockRange($startDateStamp,$endDateStamp,$uid){
        //first, we check to see if the range that has been asked to be locked has >=75 hrs worked
        $ret=$this->getTotalHrsFromStartAndEndDate($startDateStamp, $endDateStamp, $uid);
        if($ret>=0){    //changed from >=75 to >=0 July 31 2008 ED
            $startDateStamp=$startDateStamp-3600;
            $endDateStamp=$endDateStamp+3600;

            return $this->_tables['nextime_locked_timesheets']->lockTimesheet($startDateStamp,$endDateStamp,$uid);
        }else{
            return -1;
        }
    }

    function unlockRange($startDateStamp,$endDateStamp,$uid){
        return $this->_tables['nextime_locked_timesheets']->unlockTimesheet($startDateStamp,$endDateStamp,$uid);
    }


    function setAcknowledgedModified($startStamp,$endStamp,$uid){
        return $this->_tables['nextime_timesheet_entry']->setAcknowledgedModified($startStamp,$endStamp,$uid);
    }


    //simple unit test for data table creation.
    function test_table(){
        echo "Number of tables: {$this->_numberOfTables}<HR>";
        foreach($this->_tables as $table){
            echo "<b>Table: {$table->tablename}</b><br>" ;
            foreach($table->fieldlist as $x){
                echo "$x - value: ";
                echo $table->fieldvals[$x];
                echo "<BR>";
            }
            echo "<BR>";
        }//end for

    }





}

