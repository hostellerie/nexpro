<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $CONF_NEXTIME_DEFAULT;


//Report output format
$CONF_NEXTIME_DEFAULT['report_format']="Excel5";  //set this to Excel2007 for Office 2k7.  Options are Excel5 or Excel2007
//$CONF_NEXTIME_DEFAULT['report_format']="Excel2007";  //set this to Excel2007 for Office 2k7.  Options are Excel5 or Excel2007

//Set the show_center_block setting to true to show the main centerblock with the alerts.
$CONF_NEXTIME_DEFAULT['show_center_block']=true;  //set to false to hide the centrerblock


//path information
$CONF_NEXTIME_DEFAULT['base_path']=$_CONF['site_url'].'/nextime/';
$CONF_NEXTIME_DEFAULT['template_path']=$_CONF['path'].'plugins/nextime/templates/';
$CONF_NEXTIME_DEFAULT['path_to_reports']=$_CONF['path_html'] .'nextime/reports/output/';

//configuration options to manipulate the start/end offset of days/periods
$CONF_NEXTIME_DEFAULT['approval_page_date_span']=13;  //0-13 means there's actually 14 days in there...
$CONF_NEXTIME_DEFAULT['payroll_start_date']='2007/12/23';
$CONF_NEXTIME_DEFAULT['payroll_date_span']=14;
$CONF_NEXTIME_DEFAULT['approval_history_span']=3888000; //number in seconds as to how many "days" back the approval page goes.  this is set to 45 days.

//set the following setting to true to allow the system to automatically select the end date based on your payroll_date_span setting above
$CONF_NEXTIME_DEFAULT['enable_auto_end_date']=false;  //set to false to allow end-user selected start/end dates


//Please do not alter any code below here!

$CONF_NEXTIME_DEFAULT['number_of_days_after']=5; //these settings are used IF the start/end dates are not passed in properly
$CONF_NEXTIME_DEFAULT['number_of_days_before']=5; ////these settings are used IF the start/end dates are not passed in properly




/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_NEXPRO if available (e.g. from
* an old config.php), uses $_NEXPRO_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nextime()
{
    global $CONF_NEXTIME, $CONF_NEXTIME_DEFAULT;

    if (is_array($CONF_NEXTIME) && (count($CONF_NEXTIME) > 1)) {
          $CONF_NEXTIME_DEFAULT = array_merge($CONF_NEXTIME_DEFAULT, $CONF_NEXTIME);
    }

      $c = config::get_instance();
      if (!$c->group_exists('nextime')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nextime');
        $c->add('nt_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nextime');

        $c->add('report_format', $CONF_NEXTIME_DEFAULT['report_format'],
                'text', 0, 0, 0, 10, true, 'nextime');
        $c->add('show_center_block', $CONF_NEXTIME_DEFAULT['show_center_block'],
                'select',0, 0, 0, 20, true, 'nextime');
        $c->add('base_path', $CONF_NEXTIME_DEFAULT['base_path'],
                'text', 0, 0, 0, 30, true, 'nextime');
        $c->add('template_path', $CONF_NEXTIME_DEFAULT['template_path'],
                'text', 0, 0, 0, 40, true, 'nextime');
        $c->add('path_to_reports', $CONF_NEXTIME_DEFAULT['path_to_reports'],
                'text', 0, 0, 0, 50, true, 'nextime');
        $c->add('approval_page_date_span', $CONF_NEXTIME_DEFAULT['approval_page_date_span'],
                'text', 0, 0, 0, 60, true, 'nextime');
        $c->add('payroll_start_date', $CONF_NEXTIME_DEFAULT['payroll_start_date'],
                'text', 0, 0, 0, 70, true, 'nextime');
        $c->add('payroll_date_span', $CONF_NEXTIME_DEFAULT['payroll_date_span'],
                'text', 0, 0, 0, 80, true, 'nextime');
        $c->add('approval_history_span', $CONF_NEXTIME_DEFAULT['approval_history_span'],
                'text', 0, 0, 0, 90, true, 'nextime');
        $c->add('enable_auto_end_date', $CONF_NEXTIME_DEFAULT['enable_auto_end_date'],
                'select',0, 0, 0, 100, true, 'nextime');
        return true;
      }
}