<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.1 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
// | Eric de la Chevrotiere - Eric DOT delaChevrotiere AT nextide DOT ca       |
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

$LANG_NF00 = array (
    'admin_only'         => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'             => 'Plugin',
    'useradminmenu'      => 'My Tasks',
    'adminmenu'          => 'Workflow',
    'access_denied'      => 'Access Denied',
    'access_denied_msg'  => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin_access_error' => 'You do not have Nexflow Admin Permission',
    'admin'              => 'Plugin Admin',
    'install_header'     => 'Install/Uninstall Plugin',
    'installed'          => 'The Plugin and Block are now installed,<p><i>Enjoy,<br><a href="MAILTO:randy@nextide.ca">Nextide Inc.</a></i>',
    'uninstalled'        => 'The Plugin is Not Installed',
    'install_success'    => 'Installation Successful<p><b>Next Steps</b>:  <ol><li>Use the nexFlow Template Builder to create new flows...',

    'install_failed'     => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'      => 'Plugin Successfully Uninstalled',
    'install'            => 'Install',
    'uninstall'          => 'UnInstall',
    'enabled'            => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'            => 'nexFlow De-Install Warning',
    'srchText'           => 'Text Search',
    'srchFilter'         => 'Filter',
    'srchFilterTitle'    => 'Flow/Task Name',
    'srchFilterReqDesc'  => 'Request Description',
    'srchFilterPrjName'  => 'Application Group',
    'srchDoSearch'       => 'Apply Filter',
    'chkActive'          => 'Active',
    'chkComplete'        => 'Complete',
    'chkRegenerated'     => 'Regenerated',
    'processFilter'      => 'Process Filter:',
    'chooseAll'          => 'Choose All',
    'AppGroupError1'     => 'The Application Group name is being used by currently active Templates.'
);

// Away Preferences Language
$LANG_NF01 = array (
    'title'     => 'Out of Office',
    'label'     => 'Auto Re-assign of workflow task settings',
    'to'        => 'To',
    'from'      => 'From',
    'reason'    => 'Reason',
    'owner'     => 'Reassign to',
    'selectowner'   => 'Select New Owner',
    'active'    => 'Active'
);

//admin language defs
$LANG_NF02 = array (
    'workflow'              => 'Workflow',
    'actions'               => 'Actions',
    'new_workflow'          => 'New Workflow',
    'edit_workflow'         => 'Edit Workflow',
    'delete_workflow'       => 'Delete Workflow',
    'view_templates'        => 'View Templates',
    'my_tasks'              => 'My Tasks',
    'workflow_name'         => 'Workflow Name',
    'view_workflow'         => 'View Workflow',
    'submit'                => 'Submit',
    'conf_workflow_del'     => 'Are you sure you wish to delete this workflow? Deleting a workflow will remove ALL related tasks and processes.',
    'draw_line'             => 'Draw Line',
    'draw_line_true'        => 'Draw Success Line',
    'draw_line_false'       => 'Draw Fail Line',
    'clear_adj_lines'       => 'Clear Adjacent Lines',
    'add_interactive'       => 'Add Interactive',
    'add_noninteractive'    => 'Add Non-Interactive',
    'add_if'                => 'Add If',
    'cancel'                => 'Cancel',
    'select_first'          => 'Select First Element to Draw the Line to.',
    'select_last'           => 'Select Second Element to Draw the Line to.',
    'delete_task'           => 'Delete Task',
    'confirm_del_task'      => 'Are you sure you wish to delete this task?',
    'edit_task'             => 'Edit Task',
    'clear_task'            => 'Clear all lines around this task',
    'if_task'               => 'If Task',
    'interactive_task'      => 'Interactive Task',
    'noninteractive_task'   => 'Non-Interactive Task',
    'enter_task_title'      => 'Enter the task name:',
    'animation'             => 'Enable Animation',
    'task_type'             => 'Task Type',
    'nobody_assigned'       => 'Nobody Assigned...',
    'assigned_to'           => 'Assigned To',
    'new_task'              => 'New Task'
);

$LANG_NF03 = array (
    'save'                  => 'Save',
    'cancel'                => 'Close',
    'available_users'       => 'Available Users',
    'users_assigned'        => 'Users Assigned',
    'available_variables'   => 'Available Variables',
    'variables_assigned'    => 'Variables Assigned',
    'message'               => 'Message',
    'assign_by_user'        => 'Assign By User',
    'assign_by_variable'    => 'Assign By Variable',
    'notify_on_assign'      => 'Notify On Assignment',
    'notify_on_complete'    => 'Notify On Completion',
    'manual_web'            => 'Manual Web',
    'and'                   => 'And',
    'batch'                 => 'Batch',
    'if'                    => 'If',
    'batch_function'        => 'Batch Function',
    'interactive_function'  => 'Interactive Function',
    'nexform'               => 'nexForm',
    'set_process_variable'  => 'Set Process Variable',
    'task_reminders'        => 'Task Reminders',
    'no_escalation'         => 'No Escalation',
    'task_name'             => 'Task Name',
    'use_dynamic_name'      => 'Use Dynamic Task Name',
    'select_variable'       => 'Select Variable',
    'task_handler'          => 'Task Handler',
    'task_optional_parm'    => 'Task Optional Parm',
    'regenerate_this_task'  => 'Regenerate This Task',
    'regenerate_all_tasks'  => 'Regenerate All In-Production Tasks',
    'if_condition'          => 'If Condition',
    'or'                    => '- OR -',
    'on_success_blue'       => 'On Success, Follow Blue Line(s)',
    'on_fail_red'           => 'On Fail, Follow Red Line(s)',
    'task_function'         => 'Task Function',
    'choose_form'           => 'Choose a Form...',
    'choose_field'          => 'Choose a Field...',
    'task_form'             => 'Task Form',
    'initial_reminder'      => 'Initial Reminder Interval',
    'subsequent_reminder'   => 'Subsequent Reminder Interval',
    'escalate_after'        => 'Escalate after',
    'notifications_sent'    => 'notifications have been sent',
    'escalation_user'       => 'Escalation User',
    'variable'              => 'Variable',
    'set_to_input_value'    => 'Set To Input Value',
    'set_to_form_result'    => 'Set To Form Result',
    'form'                  => 'Form',
    'field'                 => 'Field',
    'inc_dec'               => 'Increment/Decrement',
    'by_how_much'           => 'By How Much',
    'can_be_negative'       => 'Integer value, can be negative.'
);

$CONF_NF['reassignment_message'] = 'Hello %s,<br><br>%s has requested the task %s from the the project %s back.  If you have already started working on this task, you can keep it, or at your approval, %s can have the task back.<br><br><a href="%s">Keep This Task</a>&nbsp;&nbsp;&nbsp;<a href="%s">Return To %s</a><br><br>Regards,<br>Administrator';

$CONF_NF['prenotify_default_message'] = 'You have a new task: [taskname].[newline]Click [here] to access the task console.';
$CONF_NF['postnotify_default_message'] = 'Task [taskname] has been completed by [taskowner]';
$CONF_NF['reminder_default_message'] = 'You have an overdue task: [taskname].[newline]It was assigned: [dateassigned].[newline]Click [here] to access the task console.';
$CONF_NF['escalation_message'] = "You are receiving this escalation notice regarding the task [taskname], because the assigned user [taskowner] has failed to complete the task in the allotted time.[newline]It was assigned: [dateassigned].[newline]Click [here] to access the task console.[newline][newline]Regards,[newline]Administrator";


$LANG_NF_MESSAGE = array (
    'msg1'      => "Supported message tags:<br />\n<b>[taskname]</b>: Task Name<br />\n<b>[taskowner]</b>: Name of user assigned to task<br />\n<b>[user]</b>: Name of user getting notification<br />\n<b>[dateassigned]</b>: Date task was assigned<br />\n<b>[newline]</b>: Insert a new line<br />\n<b>[here]</b>: Link to access task console<br />\n<b>[project]</b>: project id<br />\n<b>[projectname]</b>: Name of project<br />\n<b>[projectlink]</b>: Link to project detail page<br />\n<b>[siteurl]</b>: Link to site",
    'msg2'      => 'Task Owner changed to: %s, from: %s for task: %s',
    'hlp1'      => 'Checking this will alert the engine to regenerate this task upon a loop back condition',
    'hlp2'      => 'Checking this will assign this task to a variable\'s value regardless of who\'s physically assigned to it above',
    'hlp3'      => 'Checking this in combimation with the \'Regenerate This Task\' option will signal the nexFlow engine to carry all<br />currently in-production tasks to the newly regenerated process.'
);

$PLG_nexflow_MESSAGE10 = 'You must first install the nexpro plugin before installing any other nexpro related plugins.';
$PLG_nexflow_MESSAGE11 = 'nexFlow Plugin Upgrade completed - no errors';
$PLG_nexflow_MESSAGE12 = 'nexFlow Plugin Upgrade failed - check error.log';

// Localization of the Admin Configuration UI
$LANG_configsections['nexflow'] = array(
    'label' => 'Nexflow',
    'title' => 'Nexflow Configuration'
);

$LANG_confignames['nexflow'] = array(
    'debug'                         => 'Debug - set true for extra error.log detail',
    'TaskConsole_URL'               => 'Taskconsole URL',
    'RequestDetailLink_URL'         => 'Request Details URL',
    'export_dir'                    => 'Workflow Export Directory',
    'email_notifications_enabled'   => 'Email Notifications Enabled',
    'orchestrator_using_cron'       => 'Use cron to schedule Workflow Orchestrator',
    'allrequestsloginrequired'      => 'All requests require login',
    'taskconsolepermrequired'       => 'Taskconsole Perms Required',
    'allow_task_delete'             => 'Allow Task Delete',
    'uploadpath'                    => 'Upload Path',
    'downloadURL'                   => 'Download URL',
    'max_uploadfile_size'           => 'Max upload size in MB',
    'allowablefiletypes'            => 'Allowable Attachments'
);

$LANG_configsubgroups['nexflow'] = array(
    'sg_main'        => 'Main Settings'
);

$LANG_fs['nexflow'] = array(
    'fs_main'        => 'Main Settings',
    'fs_attachments' => 'Attachment Support Settings'
);

// Note: entries 0, 1 and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['nexflow'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
    20 => array('Public Access' => 2, 'Site Members' => 13)
);



?>