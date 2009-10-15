<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.0 for the nexPro Portal Server                        |
// | Oct 15, 2009                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexform.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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


// Plugin information
$CONF_NF['pi_display_name'] = 'nexflow';
$CONF_NF['pi_name']         = 'nexflow';
$CONF_NF['gl_version']      = '1.6.1';
$CONF_NF['version']         = '3.1.0';          // Plugin Version
$CONF_NF['pi_url']          = 'http://www.nextide.ca/';


$CONF_NF['final_edit_tasks'] = array(188);

$CONF_NF['fileperms'] = '0755';  // Needs to be a string for the upload class use.

/* Coders should append any lookup lists that are being used to this array
 * Refer to function nexlist_delitem_nexflow in functions.inc
 * Array should be an associative array
 *   $NF_LISTS['workflow_roles'] = 5;
*/
$NF_LISTS = array();


/* Do not change anything below this line  */

// Task Status values used in the nexflow Queue table
$CONF_NF['taskstatus'] = array(
    0   => 'Un-completed',
    1   => 'Completed',
    2   => 'On-hold',
    3   => 'Rejected',
    4   => 'If Condition False'
);

$CONF_NF['processstatus'] = array(
    0   => 'Active',
    1   => 'Completed',
    2   => 'Regenerated',
    3   => 'On Hold',
    4   => 'Cancelled'
);


$CONF_NF['NFProjectStatus'] = array(
    0   => 'Active',
    1   => 'Completed',
    2   => 'Regenerated',
    7   => 'On Hold',
    4   => 'Cancelled',
    5   => '',
    6   => 'Recycle'
);


$CONF_NF['formstatus'] = array (
    0       =>  'Draft',         // Not submitted
    1       =>  'Submitted',     // Submitted for approval
    2       =>  'Edit',          // Submitted form is open for edit
    3       =>  'Accepted',      // Form has been accepted
    4       =>  'Distributed',   // Form accepted and now distributed
    5       =>  'Archive',       // Form has been archived
    6       =>  'Rejected',      // Form was rejected
    7       =>  'Deleted'        // Marked for deletion
);


$CONF_NF['sortOptions'] = array(
    'cdate'             => 'Created Date',
    'taskname'          => 'Task name'
);


$_TABLES['nf_process']               = $_DB_table_prefix . 'nf_process';
$_TABLES['nf_queue']                 = $_DB_table_prefix . 'nf_queue';
$_TABLES['nf_template']              = $_DB_table_prefix . 'nf_template';
$_TABLES['nf_templatedata']          = $_DB_table_prefix . 'nf_templatedata';
$_TABLES['nf_templateassignment']    = $_DB_table_prefix . 'nf_templateassignment';
$_TABLES['nf_handlers']              = $_DB_table_prefix . 'nf_handlers';
$_TABLES['nf_steptype']              = $_DB_table_prefix . 'nf_steptype';
$_TABLES['nf_templatedatanextstep']  = $_DB_table_prefix . 'nf_templatedatanextstep';
$_TABLES['nf_processvariables']      = $_DB_table_prefix . 'nf_processvariables';
$_TABLES['nf_templatevariables']     = $_DB_table_prefix . 'nf_templatevariables';
$_TABLES['nf_ifprocessarguments']    = $_DB_table_prefix . 'nf_ifprocessarguments';
$_TABLES['nf_ifoperators']           = $_DB_table_prefix . 'nf_ifoperators';
$_TABLES['nf_queuefrom']             = $_DB_table_prefix . 'nf_queuefrom';
$_TABLES['nf_notifications']         = $_DB_table_prefix . 'nf_notifications';
$_TABLES['nf_productionassignments'] = $_DB_table_prefix . 'nf_productionassignments';
$_TABLES['nf_useraway']              = $_DB_table_prefix . 'nf_useraway';
$_TABLES['nf_appgroups']             = $_DB_table_prefix . 'nf_appgroups';

/* Task Console Tables */
$_TABLES['nf_projects']             = $_DB_table_prefix . 'nf_projects';
$_TABLES['nf_projectforms']         = $_DB_table_prefix . 'nf_projectforms';
$_TABLES['nf_projecttimestamps']    = $_DB_table_prefix . 'nf_projecttimestamps';
$_TABLES['nf_projectcomments']      = $_DB_table_prefix . 'nf_projectcomments';
$_TABLES['nf_projecttaskhistory']   = $_DB_table_prefix . 'nf_projecttaskhistory';
$_TABLES['nf_projectapprovals']     = $_DB_table_prefix . 'nf_projectapprovals';
$_TABLES['nf_projectattachments']   = $_DB_table_prefix . 'nf_projectattachments';

/* Misc Tables for custom workflows */
$_TABLES['nf_projectdatafields']    = $_DB_table_prefix . 'nf_projectdatafields';
$_TABLES['nf_projectdataresults']   = $_DB_table_prefix . 'nf_projectdataresults';

?>