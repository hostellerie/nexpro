<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                          |
// | Date: Sept. 23, 2009                                                        |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | english.php - nexTime main language declarations                            |
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
$LANG_NEXTIME=array(
    'pluginlabel'                   =>  'Timesheet System',
    'menulabel'                     =>  'Timesheet System',
    'not_logged_in'                 =>  'You must be logged in to use the Timesheet System',
    'title_label'                   =>  'Please choose what you would like to do',
    'timesheet_entry_label'         =>  'Your Timesheets:',
    'approve_timesheet_label'       =>  'Approve Timesheets:',
    'all_timesheet_approval'        =>  'View all timesheets to approve',
    'should_not_be_approving'       =>  'You are not the supervisor of the user requested...',
    'reports_label'                 =>  'Choose a report to run:',
    'start_date_label'              =>  'Start Date',
    'end_date_label'                =>  'End Date',
    'begin_timesheet_button'        =>  'View/Begin Timesheet Entry',
    'review_timesheet_button'       =>  'Review Your Timesheets',
    'begin_timesheet_approval'      =>  'Begin Timesheet Approvals',
    'alert_missing_dates'           =>  'You need to enter both Start and End dates before continuing...',
    'end_date_before_start'         =>  'The End Date you\'ve chosen is before the Start Date...  Please go back and re-select your dates.',
    'back_link_label'               =>  '&nbsp;Go Back..',
    'monday'                        =>  'Monday',
    'tuesday'                       =>  'Tuesday',
    'wednesday'                     =>  'Wednesday',
    'thursday'                      =>  'Thursday',
    'friday'                        =>  'Friday',
    'saturday'                      =>  'Saturday',
    'sunday'                        =>  'Sunday',
    'add_this_entry'                =>  'Add another Task to this day',
    'status_message_changes'        =>  'Please note that there are changes that have been made.  Please save your changes before leaving this page.',
    'delete_message'                =>  'Any unsaved rows will not be retained.  Continue?',
    'continue_with_delete'          =>  'Continue with Delete',
    'cancel_delete'                 =>  'Cancel',
    'error_message_panel_title'     =>  'Error!',
    'comment_entry_panel_title'     =>  'Enter/Edit your comment',
    'submit_comment'                =>  'Submit Comment',
    'no_items_to_remove_error'      =>  'You have not chosen any items to delete...',
    'continue_button'               =>  'Continue',
    'wait_while_saving'             =>  'Please wait while your timesheet is being saved',
    'lock_this_entry'               =>  'Lock this entry and signal that it is complete and awaiting approval...',
    'rejected_note'                 =>  'This entry has been rejected by your supervisor',
    'approved_note'                 =>  'This entry has been approved by your supervisor and cannot be modified',
    'stats_note_1'                  =>  'completed',
    'stats_note_2'                  =>  'rejected',
    'stats_note_3'                  =>  'All Entered Items approved',
    'stats_note_4'                  =>  'Items approved',
    'stats_note_5'                  =>  'Total HRS worked',
    'cant_approve_blank_entry'      =>  'Sorry, you cannot approve a blank entry!',
    'cant_reject_blank_entry'       =>  'Sorry, you cannot reject a blank entry!',
    'submit_reject_comment'         =>  'Submit Reason for Rejection',
    'reject_panel_title'            =>  'Enter your reason for rejecting this entry',
    'rejection_reason_loading'      =>  'Please wait.... fetching rejection reason...',
    'rejection_reason_panel_title'  =>  'Reason for Rejection',
    'select_activity_option'        =>  'Choose Activity',
    'report_by_employee_panel_title'=>  'Report By Employee',
    'report_by_employee_go'         =>  'Generate Report',
    'report_please_wait'            =>  'Some reports are large and can take a few minutes to generate. Please wait while report is being generated....',
    'fetching_sunday_to_sunday'     =>  'Please wait while we fetch the Sunday-Saturday date range...',
    'by_emp_date_range_label'       =>  'The date range is as follows: ',
    'please_wait_general_msg'       =>  'Please wait........',
    'enter_timesheet_for_someone'   =>  'Enter a timesheet for someone else:',
    'fetching_reports_message'      =>  'Fetching available reports... please wait...',
    'acknowledge_change'            =>  'Ack. Change',
    'filter_complete_out'           =>  'Hide locked and completed items',
    'employee_label'                =>  'Employee:',
    'employee_number'               =>  'Emp. #:',
    'employee_supervisor'           =>  'Supervisor:',
    'cut_off_at_20'                 =>  'The date range you\'ve chosen would be too large. The display has been limited to the first 30 days...',
    'run_for_which_supervisor'      =>  'Filter by Supervisor?: ',
    'run_for_which_task'            =>  'Filter by Task?: ',
    'run_for_which_project'         =>  'Filter by Project?: ',
    'report_by_task_panel_title'    =>  'Report by Task',
    'report_by_task_go'             =>  'Generate Report',
    'show_unapproved'               =>  'Show Unapproved',
    'show_rejected'                 =>  'Show Rejected',
    'report_by_emp_and_task_go'     =>  'Report by Employee and Task',
    'report_by_emp_and_task_panel_title' => 'Report by Employee and Task',
    'report_by_project_panel_title' =>  'Report by Project',
    'report_by_project_go'          =>  'Report by Project',
    'report_by_freeform_panel_title' =>  'Free Form Report',
    'report_by_freeform_go'          =>  'Free Form Report',
    'log_out'                       =>  'Save and Log out'


);

$LANG_NEXTIME_HEADER=array(
    'delete_entry'           =>  'push me to delete checked lines',
    'delete_button'          =>  'Delete',
    'date'                   => 'Date',
    'project_id'             => 'Project Number',
    'nextime_activity_id'    => 'Activity',
    'task_id'                => 'Task',
    'regular_time'           => 'Reg.<BR>Time',
    'time_1_5'               => 'Time<BR>@150%',
    'time_2_0'               => 'Time<BR>@200%',
    'vacation_time_booked'   => 'Vac.<BR>Day',
    'vacation_time_used'     => 'Vac.<BR>Used',
    'stat_time'     => 'Stat.',
    'sick_time'              => 'Sick<BR>Time',
    'personal_time'          => 'Prsnl<BR>Time',
    'banked_time_to_pay_out' => 'Banked<BR>Payout',
    'prime_5percent'         => 'Prime<BR>5%',
    'prime_10percent'        => 'Prime<BR>10%',
    'kept_in_dollars'        => 'Keep in $s',
    'other'                  => 'Other',
    'bank_to_accumulate'     => 'To Bank',
    'comment'                => 'Comment',
    'recabling_time'         => 'Recabling<BR>Time',
    'project_time'           => 'Project<BR>Time',
    'adherance_to_standards' => 'Adherance To Standards',
    'rendezvous_time'        => 'Travel<BR>Time',
    'disconnection_time'     => 'Dscnnctn<BR>Time',
    'transfer_posts'         => 'Transfer<BR>Posts',
    'kept_out_of_time'       => 'Kept-out<BR>Time',
    'building_move_time'     => 'Building Move Time',
    'save_all_button'        => '    Save All    ',
    'evening_hours'          => 'Evning<br>Hrs',
    
    'adjustment'             => 'Adj',
    'floater'                => 'Floater',
    'bereavement'            => 'Brvmnt',
    'jury_duty'              => 'Jury<BR>Duty',
    'unpaid_hrs'             => 'Unpaid<BR>Hrs.',
    'approval_col'           => 'Approve',
    'reject_col'             => 'Reject',
    'totalhrs'               => 'Total<BR>Hrs',
    'othrs'                  => 'OT<BR>Hrs',
    'total_hours'            => 'Total Hours:',
    'weekly_totals'          => 'Weekly Totals:',
    'grand_totals'           => 'Grand Totals:'

);



$LANG_NEXTIME_REPORTS=array(
    'technician_name'           =>  'Employee Name:',
    'title'                     =>  'Timesheet Report by Employee',
    'technician_number'         =>  'Employee Number:',
    'supervisor_name'           =>  'Supervisor Name:',
    'supervisor_title'          =>  'Supervisor Role:',
    'technician_region'         =>  'Region:',
    'period_from'               =>  'Period From:',
    'period_to'                 =>  'Period To:',
    'date'                      =>  'Date',
    'employee_signature'        =>  'Employee Signature',
    'supervisor_signature'      =>  'Supervisor Signature',
    'telephone_number'          =>  'Telephone Number',
    'title_by_task'             =>  'Timesheet Report by Task',
    'task'                      =>  'Task #',
    'title_by_emp_and_task'     =>  'Timesheet Report by Employee and Task',
    'title_by_project'          =>  'Timesheet Report by Project',
    'title_by_freeform'         =>  'Free Form Timesheet Report'
);



$LANG_NEXTIME_REPORT_COLUMNS=array(
    'task_number'           =>  'Task #',
    'nextime_activity_id'   =>  'Activity',
    'task_id'               =>  'Task',
    'project_number'        =>  'Project #',
    'regular_time'          =>  'Reg. Time',
    'time_1_5'              =>  'Time @150%',
    'time_2_0'              =>  'Time @200%',
    'evening_hours'         =>  'Evening Hours',
    
    'stat_time'             =>  'Statutory',
    'vacation_time_used'    =>  'Vacation Time',
    'floater'               =>  'Floater',
    'sick_time'             =>  'Paid Sick Time',
    'bereavement'           =>  'Bereavement',
    'jury_duty'             =>  'Jury Duty',
    'total_reg_hours'       =>  'Total HRS. Paid',
    'unpaid_hrs'            =>  'Unpaid Hrs.',
    'other'                 =>  'Other',
    'comment'               =>  'Comment'
);





$LANG_NEXTIME_REPORT_FREE_FORM_COLUMNS=array(
    'thedate'               =>  'Date',
    'fullname'              =>  'Full Name',
    'nextime_activity_id'   =>  'Activity',
    'project_number'        =>  'Project #',
    'project_id'            =>  'Project',
    'task_id'               =>  'Task',
    'regular_time'          =>  'Reg. Time',
    'time_1_5'              =>  'Time @150%',
    'time_2_0'              =>  'Time @200%',
    'evening_hours'         =>  'Evening Hours',
    'vacation_time_used'    =>  'Vacation Time',
    'floater'               =>  'Floater',
    'sick_time'             =>  'Sick Time',
    'bereavement'           =>  'Bereavement',
    'jury_duty'             =>  'Jury Duty',
    'other'                 =>  'Other',
    'total_reg_hours'       =>  'Total HRS.',
    'comment'               =>  'Comment',
    'unpaid_hrs'            =>  'Unpaid Hrs.'
);


$PLG_nextime_MESSAGE1 = 'You must first install the nexList plugin before installing nexTime. nexList is freely available from <a href="http://www.nextide.ca">Nextide</a>';
$PLG_nextime_MESSAGE2 = 'You must first install the nexPro plugin before installing nexTime. nexPro plugin is freely available from <a href="http://www.nextide.ca">Nextide</a>';
$PLG_nextime_MESSAGE11 = 'nexTime Plugin Upgrade completed - no errors';
$PLG_nextime_MESSAGE12 = 'nexTime Plugin Upgrade failed - check error.log';
$PLG_nextime_MESSAGE13 = '<span style="color:red;font-weight:bold;font-size:13pt;">nexTime Plugin Upgrade completed - Please ensure that you update the online configuration for nexTime.  Ensure to use your pre-existing nexList IDs.<br>
                            <br>Failure to do so will render nexTime unusable.</span>';


$LANG_configsubgroups['nextime'] = array(
    'sg_main' => 'Main Settings',
    'sg_list' => 'nexList Associations'
);

$LANG_fs['nextime'] = array(
    'nt_main'       => 'Main Settings',
    'nxtime_list'   => 'nexList Associations'
);


$LANG_configselects['nextime'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE)
);


$LANG_confignames['nextime'] = array(
    'report_format'             => 'Report Format (Excel5 or Excel2007 are valid)?',
    'show_center_block'         => 'Show the main centre block with alerts?',
    'base_path'                 => 'Base path to nextime public url?',
    'template_path'             => 'Path to template files?',
    'path_to_reports'           => 'Path to report output?',
    'approval_page_date_span'   => '# of days to show in the approval page?',
    'payroll_start_date'        => 'The date to use to calculate the entry offsets from?',
    'payroll_date_span'         => 'Number of days to show in the approval/entry pages<br>(calculated based on start date above)?',
    'approval_history_span'     => 'In seconds, as to how many days "back" the approval page goes?',
    'enable_auto_end_date'      => 'Set to false to allow end-user selected start/end dates?',
    'nexlist_timesheet_tasks'   => 'nexList Timesheet Task list ID?',
    'nexlist_nextime_activities'=> 'nexList Timesheet Activities list ID?',
    'nexlist_nextime_projects'  => 'nexList Timesheet Projects list ID?',
    'nexlist_employee_to_supervisor'=> 'nexList Employee to Supervisor list ID?',
    'nexlist_user_locations'    => 'nexList Timesheet User Locations list ID?',
    'nexlist_manager_to_supervisor'=> 'nexList Manager to Supervisor list ID?',
    'nexlist_employee_to_delegate'=> 'nexlist Employee Delegate list ID?',
    'supervisor_group_id'       => 'Supervisor Group ID?'
);

?>