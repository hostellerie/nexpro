/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | api.php                                                                   |
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


/**
* Called from nf_formatEmailMessage in plugins/nexflow/library.php
* to allow modification of the notification subject and/or message.
*
* The parameters passed include the workflow queue record id and the template task id
* which are sufficent to be able to look up template or process details and form results.
*
* @param    array   $parms      array('type' => $type, 'tid' => $tid, 'qid' => $qid, 'user' => $user);
* @param    string  $subject    current formatted subject
* @param    string  $message    current formatted message
* @return   array   containg the subject and message
*
*/

function PLG_Nexflow_tasknotification($parms,$subject,$message) {
    global $_TABLES,$_CONF,$NF_MYAPP;

    $retval = array('subject' => $subject, 'message' => $message);

    // Example implementation using $NF_MYAPP which is an array I setup to contain custom app config settings
    if ($parms['tid'] > 0 AND $parms['qid'] > 0) {
        $processid = DB_getItem($_TABLES['nf_queue'],'nf_processID',"id={$parms['qid']}");
        if ($processid > 0) {
            $fasttrack = 0;
            $nfclass= new nexflow($processid);
            $project_id = $nfclass->get_processVariable('PID');
            $request_result = DB_getItem($_TABLES['nf_projectforms'],'results_id',
                "project_id='$project_id' AND form_id={$NF_MYAPP['forms']['myform']}");
            if ($request_result > 0) {
                $fasttrack = nf_getFormResultData($request_result,$NF_MYAPP['formfield']['fast_track'] );
            }
            if ($fasttrack == 1) {
                $retval['subject'] = "$subject - Fast Track Requested";
            }
        }
    }
    return $retval;

}



/**
* Called from nf_formatEmailMessage in plugins/nexflow/library.php
* to allow modification of the notification subject and/or message.
*
* The parameters passed include the workflow process id, queue record id and the template task id
* which are sufficent to be able to look up template or process details and form results.
*
* @param    array   $parms      array('pid' => $processId, 'tid' => $tid, 'qid' => $qid, 'user' => $user);
* @param    string  $taskname   current taskname
* @return   array   containg the taskname
*
*/
function PLG_Nexflow_taskname($parms,$taskname) {
    global $_TABLES,$_CONF,$NF_MYAPP;

    $retval = array('taskname' => $taskame);

    if ($parms['tid'] == $NF_MYAPP['tasks']['mytask']) {
        if ($parms['pid'] > 0) {
            $fasttrack = 0;
            $nfclass= new nexflow($parms['pid']);
            $project_id = $nfclass->get_processVariable('PID');
            $request_result = DB_getItem($_TABLES['nf_projectforms'],'results_id',
                "project_id='$project_id' AND form_id={$NF_COGECO['forms']['capital_request']}");
            if ($request_result > 0) {
                $fasttrack = nf_getFormResultData($request_result,$NF_MYAPP['formfield']['fasttrack'] );
            }
            if ($fasttrack == 1) {
                $retval['taskname'] = "$taskname (Fast Track)";
            }
            COM_errorLog("project:$project_id, fasttrack:$fasttrack");
        }
    }
    return $retval;

}