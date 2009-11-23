<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Sept. 23, 2009                                                      |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php - base landing page for the timesheet plugin                    |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |  Author: Randy Kolenko   - randy@nextide.ca                               |
// +---------------------------------------------------------------------------+
// | This page provides a list of abilities that the end user can do           |
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

//this page will provide a list of abilities for the end user to do.
require_once('../lib-common.php');
require_once('nextime.class.php');

echo COM_siteHeader();

$uid=$_USER['uid'];
if($uid<1){
    echo $LANG_NEXTIME['not_logged_in'];
    echo COM_siteFooter();
    exit(0);
}
$T = new Template($CONF_NEXTIME['template_path']);
$T->set_file (array (
    'page'          => 'index.thtml',
    'jspage'        => 'nextime.js.thtml',
    'reportspage'   => 'reports_row.thtml',
    ));
$T->set_var('site_url', $_CONF['site_url']);
$T->set_var($LANG_NEXTIME);
$T->parse('jscontent','jspage');
$T->set_var('js', $T->get_var('jscontent'));
$T->set_var('approval_row', 'style="display:none"');
$ts=new nexTime();
$T->set_var('delegate_row', 'style="display:none"');
if($CONF_NEXTIME['enable_auto_end_date']){
    $T->set_var('enable_auto_end_date', "true");
}else{
    $T->set_var('enable_auto_end_date', "false");
}


//this section for supervisors and/or Root
if(SEC_inGroup('Root') || SEC_inGroup('nexTime Supervisors') || SEC_inGroup('nexTime Admin')){
    $T->parse('reportscontent','reportspage');
    $T->set_var('reports_row', $T->get_var('reportscontent'));
    $T->set_var('approval_row', 'style=""');
    //now set the employee_dropdown

    $output=$ts->getOptionListOfAssignedEmployees($uid);

    $T->set_var('employee_dropdown', $output);

    $output=$ts->getOptionListOfAssignedEmployees($uid,true);
    $T->set_var('other_employee_dropdown', $output);


    $T->set_var('delegated_employee_dropdown', '');
    if (SEC_inGroup('nexTime Supervisors')) {
        $output = $ts->getOptionListOfDelegatedEmployees($uid, true);
        if (($output != '<option value=""></option>') && ($output != '<option value=""></option><option value="0">View All</option>')) {
            $T->set_var('delegated_employee_dropdown', $output);
            $T->set_var('delegate_row', 'style=""');
        }
    }

    $T->set_var('uid', $_USER['uid']);

    $output=$ts->getOptionListOfHierarchyEmployees($uid);
    if($output!='' && !SEC_inGroup('nexTime Supervisors')){
        $T->set_var('supervisor_dropdown', $output);
        $T->set_var('has_supervisors', '');
    }else{
        $T->set_var('has_supervisors', 'none');
    }
    $T->set_var('is_finance_show_supervisors', 'none');
    $T->set_var('get_supervisors', '');
}

if(SEC_inGroup('nexTime Finance') || SEC_inGroup('Root') || SEC_inGroup('nexTime Admin') || SEC_inGroup('nexTime Supervisors') ){

   $T->parse('reportscontent','reportspage');
   $T->set_var('reports_row', $T->get_var('reportscontent'));
   $T->set_var('is_finance_show_supervisors', '');
   $T->set_var('get_supervisors', $ts->getSupervisorsDropDownList());
   $T->set_var('get_tasks', $ts->getTasksDropDown(false, true, 'options'));
   $T->set_var('get_projects', $ts->getExpandedProjectDropDown(true));
}

$T->parse('output','page');
echo $T->finish($T->get_var('output'));
echo COM_siteFooter();
?>