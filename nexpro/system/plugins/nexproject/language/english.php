<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.1.1 for the nexPro Portal Server                     |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
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
$LANG_PRJ = array (
    'admin_only'                => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'                    => 'Plugin',
    'searchlabel'               => 'Projects',
    'statslabel'                => 'Project Plugin - Label',
    'statsheading1'             => 'Project Plugin - Heading1',
    'searchresults'             => 'Project Search Results',
    'useradminmenu'             => 'Project Plugin Settings',
    'useradmintitle'            => 'Project Plugin User Preferences',
    'access_denied'             => 'Access Denied',
    'access_denied_msg'         => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'                     => 'Plugin Admin',
    'install_header'            => 'Install/Uninstall Plugin',
    'installed'                 => 'The Plugin and Block are now installed,<p><i>Enjoy,<br><a href="MAILTO:support@nextide.ca">Nextide</a></i>',
    'uninstalled'               => 'The Plugin is Not Installed',
    'install_success'           => 'Installation Successful',
    'install_failed'            => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'             => 'Plugin Successfully Uninstalled',
    'install'                   => 'Install',
    'uninstall'                 => 'UnInstall',
    'enabled'                   => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'                   => 'Plugin De-Install Warning',
    'PROJECT'                   => 'Project',
    'DATE'                      => 'Date',
    'OWNER'                     => 'Project Manager'
);

$LANG_PRJ_CONFIG = array (
    'nexlist_location'      => 'nexList Location List ID',
    'nexlist_department'    => 'nexList Department List ID',
    'nexlist_categories'    => 'nexList Category List ID',
    'nexlist_objectives'    => 'nexList Objectives List ID',
    'nexfile_parent_cat'    => 'nexFile Parent Category ID',
    'forum_parent_cat'      => 'Forum Parent Category ID',
);

/* Error Messages */
$PLG_nexproject_MESSAGE10 = 'Unable to load required Projects Plugin language file ';

/* Admin Navbar Setup */
$LANG_PRJ_ADMIN_NAVBAR = array (
    1   => 'View Records',
    2   => 'Category Admin',
    3   => 'Department Admin',
    4   => 'Location Admin',
    5   => 'Objective Admin'
);

$LANG_PRJ_ADMIN = array (
    'nexproject_config'     => 'nexProject Configuration',
    'nexproject_admin'      => 'Projects'
);

$LANG_PRJ01 = array ('1','2','3');
$LANG_PRJ01['1'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "Project Update",
    'LINE1'          => "Project manager %s has modified the project %s\n",
    'LINE2'          => "The project status log can be accessed here: %s\n\n",
    'LINE3'          => "You are receiving this message because the project manager has choosen to update all project resources of project changes.\n",
    'LINE4'          => "\nHave a great day!\n"
);
// Msg2: New Project is created
$LANG_PRJ01['2'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New Project Created",
    'LINE1'          => "Project manager %s has created the project %s\n",
    'LINE2'          => "The project status log can be accessed here: %s\n\n",
    'LINE3'          => "You are receiving this message because you are a resource on this project.\n",
    'LINE4'          => "\nHave a great day!\n"
);
// Msg3: New Task is Created. Send to Task Owner
$LANG_PRJ01['3'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New Task Created",
    'LINE1'          => "A new task has been assigned to %s called %s\n",
    'LINE2'          => "The task details can be accessed here: %s\n\n",
    'LINE3'          => "You are receiving this message because you have been assigned as the owner of this task.\n",
    'LINE4'          => "\nHave a great day!\n"
);

// Msg4: New Task is Created. Send to Project Manager
$LANG_PRJ01['4'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "New Task Created",
    'LINE1'          => "A new task has been assigned to %s called %s\n",
    'LINE2'          => "The task details can be accessed here: %s\n\n",
    'LINE3'          => "You are receiving this message because you are the project manager for this project.\n",
    'LINE4'          => "\nHave a great day!\n"
);

$LANG_PRJ01['5'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "Task Updated",
    'LINE1'          => "The task called %s and assigned to %s has been modified\n",
    'LINE2'          => "The task details and status log of the changes can be accessed here: %s\n\n",
    'LINE3'          => "You are receiving this message because the task is configured to update the task owner of changes to this task.\n",
    'LINE4'          => "\nHave a great day!\n"
);

$LANG_PRJ01['6'] = array (
    'HELLO'          => "Hello",
    'ADMIN'          => "Site Administrator",
    'SUBJECT'        => "Task Updated",
    'LINE1'          => "The task called %s and assigned to %s has been modified\n",
    'LINE2'          => "The task details and status log of the changes can be accessed here: %s\n\n",
    'LINE3'          => "You are receiving this message because the task owner has choosen to update the project mananger of changes to the task.\n",
    'LINE4'          => "\nHave a great day!\n"
);



$PLG_nexproject_MESSAGE1 = 'You must first install the nexPro plugin before installing nexProject.';
$PLG_nexproject_MESSAGE2 = 'You must first install the nexFile plugin before installing nexProject.';
$PLG_nexproject_MESSAGE3 = 'You must first install the nexList plugin before installing nexProject.';
$PLG_nexproject_MESSAGE4 = 'You must first install the Forums plugin before installing nexProject.';
$PLG_nexproject_MESSAGE11 = 'nexProject Plugin Upgrade completed - no errors';
$PLG_nexproject_MESSAGE12 = 'nexProject Plugin Upgrade failed - check error.log';
$PLG_nexproject_MESSAGE13 = '<span style="color:red;font-weight:bold;font-size:13pt;">nexProject Plugin Upgrade completed - Please ensure that you update the online configuration for nexProject.  Ensure to use your pre-existing nexFile IDs and nexList IDs.<br>
                            <br>Failure to do so will render nexProject unusable.</span>';




/* General Language used in Plugin */

$setCharset = "ISO-8859-1";

$byteUnits = array('Bytes', 'KB', 'MB', 'GB');

$dayNameArray = array(1 =>"Monday", 2 =>"Tuesday", 3 =>"Wednesday", 4 =>"Thursday", 5 =>"Friday", 6 =>"Saturday", 7 =>"Sunday");

$monthNameArray = array(1=> "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

$status = array(0 => "New", 1 => "Comp", 2 => "Cancel", 3 => "Active", 4 => "Susp");

$profil = array(0 => "Administrator", 1 => "Project Manager", 2 => "User", 3 => "Client User", 4 => "Disabled", 5 => "Project Manager Administrator");

$priority = array(0 => "None", 1 => "Very low", 2 => "Low", 3 =>  "Medium", 4 => "High", 5 => "Very high");

$progress = array(0 => "Green", 1 => "Yellow", 2=> "Red");

$duration = array(0 => "Hours", 1 => "Days", 2=> "Weeks");

$statusTopic = array(0 => "Closed", 1 => "Open");
$statusTopicBis = array(0 => "Yes", 1 => "No");

$statusPublish = array(0 => "Yes", 1 => "No");

$statusFile = array(0 => "Approved", 1 => "Approved With Changes", 2 => "Needs Approval", 3 => "No Approvals Needed", 4 => "Not Approved");

$phaseStatus = array(0 => "Not started", 1 => "Open", 2 => "Complete", 3 => "Suspended");

$requestStatus = array(0 => "New", 1 => "Open", 2 => "Complete");

$strings["please_login"] = "Please log in";
$strings["requirements"] = "System Requirements";
$strings["login"] = "Log In";
$strings["no_items"] = "No items to display";
$strings["logout"] = "Log Out";
$strings["preferences"] = "Preferences";
$strings["my_tasks"] = "My Tasks";
$strings["edit_task"] = "Edit Task";
$strings["edit_subtask"] = "Edit SubTask";
$strings["copy_task"] = "Copy Task";
$strings["copy_subtask"] = "Copy SubTask";
$strings["add_task"] = "Add Task";
$strings["delete_task"] = "Delete Task";
$strings["delete_subtasks"] = "Delete Task";
$strings['view_task']   = 'View Task';
$strings['delete_parent_task'] = 'Delete Task and associated subtasks';
$strings["assignment_history"] = "Assignment History";
$strings["assigned_on"] = "Assigned On";
$strings["assigned_by"] = "Assigned By";
$strings["to"] = "To";
$strings["task_assigned"] = "Task assigned to ";
$strings["task_unassigned"] = "Task assigned to Unassigned (Unassigned)";
$strings["edit_multiple_tasks"] = "Edit Multiple Tasks";
$strings["tasks_selected"] = "tasks selected. Choose new values for these tasks, or select [No Change] to retain current values.";
$strings["assignment_comment"] = "Assignment Comment";
$strings["no_change"] = "[No Change]";
$strings["my_discussions"] = "My Discussions";
$strings["discussions"] = "Discussions";
$strings["delete_discussions"] = "Delete Discussions";
$strings["delete_discussions_note"] = "Note: Discussions cannot be reopened once they are deleted.";
$strings["topic"] = "Topic";
$strings["posts"] = "Posts";
$strings["latest_post"] = "Latest Post";
$strings["my_reports"] = "My Reports";
$strings["reports"] = "Reports";
$strings["create_report"] = "Create Report";
$strings["report_intro"] = "Select your task reporting parameters here and save the query on the results page after running your report.";
$strings["admin_intro"] = "Project settings and configuration.";
$strings["copy_of"] = "Copy of ";
$strings["add"] = "Add";
$strings["delete"] = "Delete";
$strings["remove"] = "Remove";
$strings["copy"] = "Copy";
$strings["view"] = "View";
$strings["edit"] = "Edit";
$strings["update"] = "Update";
$strings["details"] = "Details";
$strings["none"] = "None";
$strings["close"] = "Close";
$strings["new"] = "New";
$strings["select_all"] = "Select All";
$strings["unassigned"] = "Unassigned";
$strings["administrator"] = "Administrator";
$strings["my_projects"] = "My Projects";
$strings["project"] = "Project";
$strings["allprojects"] = "All Projects";
$strings["active"] = "Active";
$strings["inactive"] = "Inactive";
$strings["project_id"] = "Project ID";
$strings["task_id"] = "Task ID";
$strings['view_project'] = 'View Project';
$strings["edit_project"] = "Edit Project";
$strings["copy_project"] = "Copy Project";
$strings["add_project"] = "Add Project";
$strings["clients"] = "Clients";
$strings["progress"] = "Progress";
$strings["client_projects"] = "Client Projects";
$strings["client_users"] = "Client Users";
$strings["edit_organization"] = "Edit Client Organization";
$strings["add_organization"] = "Add Client Organization";
$strings["organizations"] = "Client Organizations";
$strings["info"] = "Info";
$strings["status"] = "Status";
$strings["owner"] = "Owner";
$strings["home"] = "My Projects";
$strings["projects"] = "All Projects";
$strings["files"] = "Files";
$strings["search"] = "Search";
$strings["admin"] = "Admin";
$strings["user"] = "User";
$strings["project_manager"] = "Project Manager";
$strings["due"] = "Due";
$strings["task"] = "Task";
$strings["tasks"] = "Tasks";
$strings["team"] = "Team";
$strings["add_team"] = "Add Team Members";
$strings["team_members"] = "Team Members";
$strings["full_name"] = "Full name";
$strings["title"] = "Title";
$strings["user_name"] = "User Name";
$strings["work_phone"] = "Work Phone";
$strings["priority"] = "Priority&nbsp;";
$strings["name"] = "Name";
$strings["id"] = "ID";
$strings["description"] = "Description";
$strings["phone"] = "Phone";
$strings["url"] = "URL";
$strings["address"] = "Address";
$strings["comments"] = "Comments";
$strings["created"] = "Created";
$strings["assigned"] = "Assigned";
$strings["modified"] = "Modified";
$strings["assigned_to"] = "Assigned to";
$strings["due_date"] = "Due Date";
$strings["estimated_time"] = "Estimated Time";
$strings["actual_time"] = "Actual Time";
$strings["delete_following_task"] = "Confirm you want to delete this task";
$strings["delete_following_tasks"] = "Select which tasks to delete";
$strings["copy_following"] = "Copy the following?";
$strings["cancel"] = "Cancel";
$strings["and"] = "and";
$strings["administration"] = "Administration";
$strings["user_management"] = "User Management";
$strings["system_information"] = "System Information";
$strings["product_information"] = "Product Information";
$strings["system_properties"] = "System Properties";
$strings["create"] = "Create";
$strings["report_save"] = "Save this report query to your homepage so you can run the query again.";
$strings["report_name"] = "Report Name";
$strings["save"] = "Save";
$strings["matches"] = "Matches";
$strings["match"] = "Match";
$strings["success"] = "Success";
$strings['warning'] = 'Warning';
$strings["addition_succeeded"] = "Addition succeeded";
$strings["deletion_succeeded"] = "Deletion succeeded";
$strings["report_created"] = "Created report";
$strings["deleted_reports"] = "Deleted reports";
$strings["modification_succeeded"] = "Modification succeeded";
$strings["errors"] = "Errors found!";
$strings["blank_user"] = "The user cannot be found.";
$strings["blank_organization"] = "The client organization cannot be located.";
$strings["blank_project"] = "The project cannot be located.";
$strings["user_profile"] = "User Profile";
$strings["change_password"] = "Change Password";
$strings["change_password_user"] = "Change the user's password.";
$strings["old_password_error"] = "The old password you entered is incorrect. Please re-enter the old password.";
$strings["new_password_error"] = "The two passwords you entered did not match. Please re-enter your new password.";
$strings["notifications"] = "Notifications";
$strings["change_password_intro"] = "Enter your old password then enter and confirm your new password.";
$strings["old_password"] = "Old Password";
$strings["password"] = "Password";
$strings["new_password"] = "New Password";
$strings["confirm_password"] = "Confirm Password";
$strings["email"] = "E-Mail";
$strings["home_phone"] = "Home Phone";
$strings["mobile_phone"] = "Mobile Phone";
$strings["fax"] = "Fax";
$strings["permissions"] = "Permissions";
$strings["administrator_permissions"] = "Administrator Permissions";
$strings["project_manager_permissions"] = "Project Manager Permissions";
$strings["user_permissions"] = "User Permissions";
$strings["account_created"] = "Account Created";
$strings["edit_user"] = "Edit User";
$strings["edit_user_details"] = "Edit the user account details.";
$strings["change_user_password"] = "Change the user's password.";
$strings["select_permissions"] = "Select permissions for this user";
$strings["add_user"] = "Add User";
$strings["enter_user_details"] = "Enter details for the user account you are creating.";
$strings["enter_password"] = "Enter the user's password.";
$strings["success_logout"] = "You have successfully logged out. You can log back in by typing your user name and password below.";
$strings["invalid_login"] = "The user name and/or password you entered is invalid. Please re-enter your login information.";
$strings["profile"] = "Profile";
$strings["user_details"] = "User account details.";
$strings["edit_user_account"] = "Edit your account information.";
$strings["no_permissions"] = "You do not have sufficient permissions to perform that action.";
$strings["discussion"] = "Discussion";
$strings["retired"] = "Retired";
$strings["last_post"] = "Last Post";
$strings["post_reply"] = "Post Reply";
$strings["posted_by"] = "Posted By";
$strings["when"] = "When";
$strings["post_to_discussion"] = "Post to Discussion";
$strings["message"] = "Message";
$strings["delete_reports"] = "Delete Reports";
$strings["delete_project"] = "Delete Project";
$strings["delete_projects"] = "Delete Projects";
$strings["delete_organizations"] = "Delete Client Organizations";
$strings["delete_organizations_note"] = "Note: This will delete all client users for these client organizations, and disassociate all open projects from these client organizations.";
$strings["delete_messages"] = "Delete Messages";
$strings["attention"] = "Attention";
$strings["delete_teamownermix"] = "Removal successful, but the project owner cannot be removed from the project team.";
$strings["delete_teamowner"] = "You cannot remove the project owner from the project team.";
$strings["enter_keywords"] = "Enter keywords";
$strings["search_options"] = "Keyword and Search Options";
$strings["search_note"] = "You must enter information in the Search for field.";
$strings["search_results"] = "Search Results";
$strings["users"] = "Users";
$strings["search_for"] = "Search for";
$strings["results_for_keywords"] = "Search results for keywords";
$strings["add_discussion"] = "Add Discussion";
$strings["delete_users"] = "Delete User Accounts";
$strings["reassignment_user"] = "Project and Task Reassignment";
$strings["there"] = "There are";
$strings["owned_by"] = "owned by the users above.";
$strings["reassign_to"] = "Before deleting users, reassign these to";
$strings["no_files"] = "No files linked";
$strings["published"] = "Published";
$strings["project_site"] = "Project Site";
$strings["approval_tracking"] = "Approval Tracking";
$strings["size"] = "Size";
$strings["add_project_site"] = "Add to Project Site";
$strings["remove_project_site"] = "Remove from Project Site";
$strings["more_search"] = "More search options";
$strings["results_with"] = "Find Results With";
$strings["search_topics"] = "Search Topics";
$strings["search_properties"] = "Search Properties";
$strings["date_restrictions"] = "Date Restrictions";
$strings["case_sensitive"] = "Case Sensitive";
$strings["yes"] = "Yes";
$strings["no"] = "No";
$strings["sort_by"] = "Sort by";
$strings["type"] = "Type";
$strings["date"] = "Date";
$strings["all_words"] = "all of the words";
$strings["any_words"] = "any of the words";
$strings["exact_match"] = "exact match";
$strings["all_dates"] = "All dates";
$strings["between_dates"] = "Between dates";
$strings["all_content"] = "All content";
$strings["all_properties"] = "All properties";
$strings["no_results_search"] = "The search returned no results.";
$strings["no_results_report"] = "The report returned no results.";
$strings["schema_date"] = "YYYY/MM/DD";
$strings["hours"] = "hours";
$strings["choice"] = "Choice";
$strings["missing_file"] = "Missing file !";
$strings["project_site_deleted"] = "The project site was successfully deleted.";
$strings["add_user_project_site"] = "The user was successfully granted permission to access the Project Site.";
$strings["remove_user_project_site"] = "User permission was successfully removed.";
$strings["add_project_site_success"] = "The addition to the project site succeeded.";
$strings["remove_project_site_success"] = "The removal from the project site succeeded.";
$strings["add_file_success"] = "Linked 1 content item.";
$strings["delete_file_success"] = "Unlinking succeeded.";
$strings["update_comment_file"] = "The file comment was updated successfully.";
$strings["session_false"] = "Session error";
$strings["logs"] = "Logs";
$strings["logout_time"] = "Auto Log Out";
$strings["noti_foot1"] = "This notification was generated by PhpCollab.";
$strings["noti_foot2"] = "To view your PhpCollab Home Page, visit:";
$strings["noti_taskassignment1"] = "New task:";
$strings["noti_taskassignment2"] = "A task has been assigned to you:";
$strings["noti_moreinfo"] = "For more information, please visit:";
$strings["noti_prioritytaskchange1"] = "Task priority changed:";
$strings["noti_prioritytaskchange2"] = "The priority of the following task has changed:";
$strings["noti_statustaskchange1"] = "Task status changed:";
$strings["noti_statustaskchange2"] = "The status of the following task has changed:";
$strings["login_username"] = "You must enter a user name.";
$strings["login_password"] = "Please enter a password.";
$strings["login_clientuser"] = "This is a client user account. You cannot access PhpCollab with a client user account.";
$strings["user_already_exists"] = "There is already a user with this name. Please choose a variation of the user's name.";
$strings["noti_duedatetaskchange1"] = "Task due date changed:";
$strings["noti_duedatetaskchange2"] = "The due date of the following task has changed:";
$strings["company"] = "Company";
$strings["show_all"] = "Show All";
$strings["information"] = "Information";
$strings["delete_message"] = "Delete this message";
$strings["project_team"] = "Project Team";
$strings["document_list"] = "Document List";
$strings["bulletin_board"] = "Bulletin Board";
$strings["bulletin_board_topic"] = "Bulletin Board Topic";
$strings["create_topic"] = "Create a New Topic";
$strings["topic_form"] = "Topic Form";
$strings["enter_message"] = "Enter your message";
$strings["upload_file"] = "Upload a File";
$strings["upload_form"] = "Upload Form";
$strings["upload"] = "Upload";
$strings["document"] = "Linked Content";
$strings["approval_comments"] = "Approval Comments";
$strings["client_tasks"] = "Client Tasks";
$strings["team_tasks"] = "Team Tasks";
$strings["team_member_details"] = "Project Team Member Details";
$strings["client_task_details"] = "Client Task Details";
$strings["team_task_details"] = "Team Task Details";
$strings["language"] = "Language";
$strings["welcome"] = "Welcome";
$strings["your_projectsite"] = "to Your Project Site";
$strings["contact_projectsite"] = "If you have any questions about the extranet or the information found here, please contact the project lead";
$strings["company_details"] = "Company Details";
$strings["database"] = "Backup and restore database";
$strings["company_info"] = "Edit your company informations";
$strings["create_projectsite"] = "Create Project Site";
$strings["projectsite_url"] = "Project Site URL";
$strings["design_template"] = "Design Template";
$strings["preview_design_template"] = "Preview Template Design";
$strings["delete_projectsite"] = "Delete Project Site";
$strings["add_file"] = "Add File";
$strings["linked_content"] = "Linked Content";
$strings["edit_file"] = "Edit file details";
$strings["permitted_client"] = "Permitted Client Users";
$strings["grant_client"] = "Grant Permission to View Project Site";
$strings["add_client_user"] = "Add Client User";
$strings["edit_client_user"] = "Edit Client User";
$strings["client_user"] = "Client User";
$strings["client_change_status"] = "Change your status below when you have completed this task";
$strings["project_status"] = "Project Status";
$strings["view_projectsite"] = "View Project Site";
$strings["enter_login"] = "Enter your login to receive new password";
$strings["send"] = "Send";
$strings["no_login"] = "Login not found in database";
$strings["email_pwd"] = "Password sent";
$strings["no_email"] = "User without email";
$strings["forgot_pwd"] = "Forgot password ?";
$strings["project_owner"] = "You can only change project owner by editting the project.";
$strings["connected"] = "Connected";
$strings["session"] = "Session";
$strings["last_visit"] = "Last visit";
$strings["compteur"] = "Count";
$strings["ip"] = "Ip";
$strings["task_owner"] = "You are not a team member in this project";
$strings["export"] = "Export";
$strings["browse_cvs"] = "Browse CVS";
$strings["repository"] = "CVS Repository";
$strings["reassignment_clientuser"] = "Task Reassignment";
$strings["organization_already_exists"] = "That name is already in use in the system. Please choose another.";
$strings["blank_organization_field"] = "You must enter the client organization name.";
$strings["blank_fields"] = "mandatory fiels";
$strings["projectsite_login_fails"] = "We are unable to confirm the user name and password combination.";
$strings["start_date"] = "Start&nbsp;date&nbsp;&nbsp;";
$strings["completion"] = "Completion";
$strings["update_available"] = "An update is available!";
$strings["version_current"] = "You are currently using version";
$strings["version_latest"] = "The latest version is";
$strings["sourceforge_link"] = "See project page on Sourceforge";
$strings["demo_mode"] = "Demo mode. Action not allowed.";
$strings["setup_erase"] = "Erase the file setup.php!!";
$strings["no_file"] = "No file selected";
$strings["exceed_size"] = "Exceed max file size";
$strings["no_php"] = "Php file not allowed";
$strings["approval_date"] = "Approval date";
$strings["approver"] = "Approver";
$strings["error_database"] = "Can't connect to database";
$strings["error_server"] = "Can't connect to server";
$strings["version_control"] = "Version Control";
$strings["vc_status"] = "Status";
$strings["vc_last_in"] = "Date last modified";
$strings["ifa_comments"] = "Approval comments";
$strings["ifa_command"] = "Change approval status";
$strings["vc_version"] = "Version";
$strings["ifc_revisions"] = "Peer Reviews";
$strings["ifc_revision_of"] = "Review of version";
$strings["ifc_add_revision"] = "Add Peer Review";
$strings["ifc_update_file"] = "Update file";
$strings["ifc_last_date"] = "Date last modified";
$strings["ifc_version_history"] = "Version History";
$strings["ifc_delete_file"] = "Delete file and all child versions & reviews";
$strings["ifc_delete_version"] = "Delete Selected Version";
$strings["ifc_delete_review"] = "Delete Selected Review";
$strings["ifc_no_revisions"] = "There are currently no revisions of this document";
$strings["unlink_files"] = "Unlink Files";
$strings["remove_team"] = "Remove Team Members";
$strings["remove_team_info"] = "Remove these users from the project team?";
$strings["remove_team_client"] = "Remove Permission to View Project Site";
$strings["note"] = "Note";
$strings["notes"] = "Notes";
$strings["subject"] = "Subject";
$strings["delete_note"] = "Delete Notes Entries";
$strings["add_note"] = "Add Note Entry";
$strings["edit_note"] = "Edit Note Entry";
$strings["version_increm"] = "Select the version change to apply:";
$strings["url_dev"] = "Development site url";
$strings["url_prod"] = "Final site url";
$strings["note_owner"] = "You can make changes only on your own notes.";
$strings["alpha_only"] = "Alpha-numeric only in login";
$strings["edit_notifications"] = "Edit E-mail Notifications";
$strings["edit_notifications_info"] = "Select events for which you wish to receive E-mail notification.";
$strings["select_deselect"] = "Select/Deselect All";
$strings["noti_addprojectteam1"] = "Added to project team :";
$strings["noti_addprojectteam2"] = "You have been added to the project team for :";
$strings["noti_removeprojectteam1"] = "Removed from project team :";
$strings["noti_removeprojectteam2"] = "You have been removed from the project team for :";
$strings["noti_newpost1"] = "New post :";
$strings["noti_newpost2"] = "A post was added to the following discussion :";
$strings["edit_noti_taskassignment"] = "I am assigned to a new task.";
$strings["edit_noti_statustaskchange"] = "The status of one of my tasks changes.";
$strings["edit_noti_prioritytaskchange"] = "The priority of one of my tasks changes.";
$strings["edit_noti_duedatetaskchange"] = "The due date of one of my tasks changes.";
$strings["edit_noti_addprojectteam"] = "I am added to a project team.";
$strings["edit_noti_removeprojectteam"] = "I am removed from a project team.";
$strings["edit_noti_newpost"] = "A new post is made to a discussion.";
$strings["add_optional"] = "Add an optional";
$strings["assignment_comment_info"] = "Add comments about the assignment of this task";
$strings["my_notes"] = "My Notes";
$strings["edit_settings"] = "Edit settings";
$strings["max_upload"] = "Max file size";
$strings["project_folder_size"] = "Project folder size";
$strings["calendar"] = "Calendar";
$strings["date_start"] = "Start date";
$strings["date_end"] = "End date";
$strings["time_start"] = "Start time";
$strings["time_end"] = "End time";
$strings["calendar_reminder"] = "Reminder";
$strings["shortname"] = "Short name";
$strings["calendar_recurring"] = "Event recurs every week on this day";
$strings["edit_database"] = "Edit database";
$strings["noti_newtopic1"] = "New discussion :";
$strings["noti_newtopic2"] = "A new discussion was added to the following project :";
$strings["edit_noti_newtopic"] = "A new discussion topic was created.";
$strings["today"] = "Today";
$strings["previous"] = "Previous";
$strings["next"] = "Next";
$strings["help"] = "Help";
$strings["complete_date"] = "Complete date";
$strings["scope_creep"] = "Scope creep";
$strings["days"] = "Days";
$strings["logo"] = "Logo";
$strings["remember_password"] = "Remember Password";
$strings["client_add_task_note"] = "Note: The entered task is registered into the data base, appears here however only if it one assigned to a team member!";
$strings["noti_clientaddtask1"] = "Task added by client :";
$strings["noti_clientaddtask2"] = "A new task was added by client from project site to the following project :";
$strings["phase"] = "Phase";
$strings["phases"] = "Phases";
$strings["phase_id"] = "Phase ID";
$strings["current_phase"] = "Active phase(s)";
$strings["total_tasks"] = "Total Tasks";
$strings["uncomplete_tasks"] = "Uncompleted Tasks";
$strings["no_current_phase"] = "No phase is currently active";
$strings["true"] = "True";
$strings["false"] = "False";
$strings["enable_phases"] = "Enable Phases";
$strings["phase_enabled"] = "Phase Enabled";
$strings["order"] = "Order";
$strings["options"] = "Options";
$strings["support"] = "Support";
$strings["support_request"] = "Support Request";
$strings["support_requests"] = "Support Requests";
$strings["support_id"] = "Request ID";
$strings["my_support_request"] = "My Support Requests";
$strings["introduction"] = "Introduction";
$strings["submit"] = "Submit";
$strings["support_management"] = "Support Management";
$strings["date_open"] = "Date Opened";
$strings["date_close"] = "Date Closed";
$strings["add_support_request"] = "Add Support Request";
$strings["add_support_response"] = "Add Support Response";
$strings["respond"] = "Respond";
$strings["delete_support_request"] = "Support request deleted";
$strings["delete_request"] = "Delete support request";
$strings["delete_support_post"] = "Delete support post";
$strings["new_requests"] = "New requests";
$strings["open_requests"] = "Open requests";
$strings["closed_requests"] = "Complete requests";
$strings["manage_new_requests"] = "Manage new requests";
$strings["manage_open_requests"] = "Manage open requests";
$strings["manage_closed_requests"] = "Manage complete requests";
$strings["responses"] = "Responses";
$strings["edit_status"] = "Edit Status";
$strings["noti_support_request_new2"] = "You have submited a support request regarding: ";
$strings["noti_support_post2"] = "A new response has been added to your support request. Please review the details bellow.";
$strings["noti_support_status2"] = "Your support request has been updated. Please review the details bellow.";
$strings["noti_support_team_new2"] = "A new support request has been added to project: ";
//2.0
$strings["delete_subtasks"] = "Delete subtasks";
$strings["add_subtask"] = "Add subtask";
$strings["edit_subtask"] = "Edit subtask";
$strings["subtask"] = "Subtask";
$strings["subtasks"] = "Subtasks";
$strings["show_details"] = "Show details";
$strings["updates_task"] = "Task update history";
$strings["updates_subtask"] = "Subtask update history";
//2.1
$strings["go_projects_site"] = "Go to projects site";
$strings["bookmark"] = "Bookmark";
$strings["bookmarks"] = "Bookmarks";
$strings["bookmark_category"] = "Category";
$strings["bookmark_category_new"] = "New category";
$strings["bookmarks_all"] = "All";
$strings["bookmarks_my"] = "My Bookmarks";
$strings["my"] = "My";
$strings["bookmarks_private"] = "Private";
$strings["shared"] = "Shared";
$strings["private"] = "Private";
$strings["add_bookmark"] = "Add bookmark";
$strings["edit_bookmark"] = "Edit bookmark";
$strings["delete_bookmarks"] = "Delete bookmarks";
$strings["team_subtask_details"] = "Team Subtask Details";
$strings["client_subtask_details"] = "Client Subtask Details";
$strings["client_change_status_subtask"] = "Change your status below when you have completed this subtask";
$strings["disabled_permissions"] = "Disabled account";
$strings["user_timezone"] = "Timezone (GMT)";
//2.2
$strings["project_manager_administrator_permissions"] = "Project Manager Administrator";
$strings["bug"] = "Bug Tracking";
//2.3
$strings["report"] = "Report";
$strings["license"] = "License";
//2.4
$strings["settings_notwritable"] = "Settings.php file is not writable";

// Added by
$strings["percent"] = "Effort";
$strings["effort"] = "Effort";
$strings["percentcomplete"] = "Percent Complete";
$strings["objective"] = "Objective";
$strings["private"] = "Private Project";
$strings["template"] = "Project Template";
$strings["forum"] = "Discussion Board";
$strings["keywords"] = "Keywords";
$strings["estimated_end_date"] = "Est End Date";
$strings["actual_end_date"] = "Actual End Date";
$strings["create_date"] = "Create Date";
$strings["lastupdated"] = "Last Updated";
$strings["location"] = "Location(s)";
$strings["department"] = "Department(s)";
$strings["category"] = "Category(s)";
$strings["projectteam"] = "Project Team";
$strings["loginname"] = "Login Name";
$strings["fullname"] = "Full Name";
$strings["role"] = "Project Role";
$strings["duedate"] = "Due Date";
$strings["file_name"] = "File Name";
$strings["file_size"] = "Size";
$strings["file_description"] = "File Description";
$strings["file_version"] = "File Version";
$strings["Subject"] = "Subject";
$strings["Author"] = "Author";
$strings["Date"] = "Date";
$strings["Replies"] = "Replies";
$strings["reply"] = "Reply to Discussion";
$strings["duration"] =  "Duration Type";
$strings["back"] = "Go Back";
$strings["removeTask"] = "Task deleted from project";
$strings["removeTasks"] = "Selected multiple tasks deleted from project";
$strings["noremoveTask"] = "No Tasks Deleted";
$strings["addTask"] = "Add Task";
$strings["addSubtask"] = "Add Subtask";
$strings["file_submitter"] = "File Submitter";
$strings["lastUpdated"] = "Last Updated";
$strings["modify_project_team"] = "Modify Project Team";
$strings["edit_project_role"] = "Edit Project Permissions";
$strings["edit_project_role_resource"] = "Change this user's project permissions to Moderator?";
$strings["edit_project_role_moderator"] = "Change this user's project permissions to Resource?";
$strings["delete_resource"] = "Remove Project Resource";
$strings["remove_resource"] = "Remove the following resource from the project";
$strings["remove_resource"] = "Remove Resource";
$strings["statuslog"] = "Change Log";
$strings['updatedon'] = " - Updated on ";
$strings["comment"] = "Comment: ";
$strings["noresults"] = "No changes to date";
$strings["sequence_id"] = "Task Order";
$strings["EnddateLabel"] = "End Date";
$strings["SeqLabel"] = "&#35;";
$strings["ProgressLabel"] = 'Progress';
$strings["ProgressBlankLabel"] = '<span style="background-color:#C8CDEC;padding-right:5px;"></span>';
$strings["resources"] = "Resources";
$strings["notifaction"] = "Enable Notification";
$strings["taskorder"] = "Task Order";
$strings["nocomment"] = "Edit Project completed, no comment entered";
$strings["moderator"] = "Moderator";
$strings["resource"] = "Resource";
$strings["available_members"] = "Available Members";
$strings["current_members"] = "Current Members";
$strings["all_projects"] = "All Projects";
$strings["by_category"] = "By Categories";
$strings["by_location"] = "By Location";
$strings["by_department"] = "By Departments";
$strings["by_objective"] = "By Objectives";
$strings["by_progress"] = "By Progress";
$strings["by_priority"] = "By Priority";
$strings["by_status"] = "By Status";
$strings["by_custom"] = "By Custom Filter";
$strings["status_log_des"] = "Log Entry";
$strings["taskonly"] = "Tasks Only";
$strings["subtaskonly"] = "Subtasks Only";
$strings["taskandsubtask"] = "Both Tasks and Subtasks";
$strings['RETURN2PROJECT'] = 'Return to Project';


$LANG_configsubgroups['nexproject'] = array(
    'sg_main' => 'Main Settings',
    'sg_list' => 'nexList Plugin Linkages',
    'sg_forum' => 'Forum Plugin Linkages',
    'sg_file' => 'nexFile Plugin Linkages'

);

$LANG_fs['nexproject'] = array(
    'prj_main'      => 'Main Settings',
    'prj_list'      => 'nexList plugin Linkages',
    'prj_forum'     => 'Forum plugin Linkage',
    'prj_file'      => 'nexFile plugin Linkage',
);


$LANG_configselects['nexproject'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE)
);

$LANG_confignames['nexproject'] = array(
    'debug'                   => 'Debug?',
    'notifications_enabled'   => 'Notifications Enabled?',
    'fonts_directory'         => 'Fonts Directory?',
    'leftblocks'              => 'Left Blocks on Project Page?',
    'project_name_length'     => 'Project Name length?',
    'lockduration'            => 'Lock Duration?',
    'min_graph_width'         => 'Min. Graph Width?',
    'project_block_rows'      => 'Project Block # of Rows?',
    'task_block_rows'         => 'Task Block # of Rows?',
    'project_task_block_rows' => 'Project Task Block # of Rows?',
    'subTaskImg'              => 'Sub-Task Image?',
    'subTaskOrderImg'         => 'Sub-Task Order Image?',
    'THEME'                   => 'nexProject Theme location?',
    'ROWLIMIT'                => 'Row limit to show?',
    'TTF_DIR'                 => 'Location for True Type Fonts?',
    'nexlist_locations'        => 'Location nexList ID?',
    'nexlist_departments'      => 'Department nexList ID?',
    'nexlist_category'      => 'Categories nexList ID?',
    'nexlist_objective'      => 'Objectives nexList ID?',
    'nexfile_parent'      => 'Nexfile Parent category for nexProject?',
    'forum_parent'        => 'Forum Parent category for nexProject?',
);

?>