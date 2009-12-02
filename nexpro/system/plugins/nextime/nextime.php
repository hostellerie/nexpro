<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                          |
// | Date: Sept. 23, 2009                                                        |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca      |
// +-----------------------------------------------------------------------------+
// | nextime.php - nexTime main configuration script                             |
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

require_once $_CONF['path_system'] . 'classes/config.class.php';

$CONF_NEXTIME=array();
$CONF_NEXTIME['version']='1.2.0';
$CONF_NEXTIME['gl_version']='1.6.1';
$CONF_NEXTIME['pi_name']='nextime';
$CONF_NEXTIME['pi_display_name']='nexTime';

$CONF_NEXTIME['dependent_plugins']=array(
    'nexpro'    => '2.1.0',
    'nexlist'   => '2.2.0'

);

//The following settings are not to be altered.
//You do not have to alter anything below here.

$CONF_NEXTIME['number_of_days_after']=5; //these settings are used IF the start/end dates are not passed in properly
$CONF_NEXTIME['number_of_days_before']=5; ////these settings are used IF the start/end dates are not passed in properly

$CONF_NEXTIME['table_class_array']=array(
    'TABLE_timesheet_entry',
    'VIEW_nextime_data',
    'TABLE_nextime_locked_timesheets'
);

$CONF_NEXTIME['day_offsets']=array(
    '0' => 'sunday',
    '1' => 'monday',
    '2' => 'tuesday',
    '3' => 'wednesday',
    '4' => 'thursday',
    '5' => 'friday',
    '6' => 'saturday'
);

$CONF_NEXTIME['report_columns']=array( 'SPACER',
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
    'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
    'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
    'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'
);

//DB table definitions
$_TABLES['nextime_timesheet_entry']             = $_DB_table_prefix . 'nxtime_timesheet_entry';
$_TABLES['nextime_extra_user_data']             = $_DB_table_prefix . 'nxtime_extra_user_data';
$_TABLES['nextime_locked_timesheets']           = $_DB_table_prefix . 'nxtime_locked_timesheets';
$_TABLES['nextime_vars']                        = $_DB_table_prefix . 'nxtime_vars';

$nextime_config = config::get_instance();
$CONF_NEXTIME_2 = $nextime_config->get_config('nextime');
if(is_array($CONF_NEXTIME_2)) $CONF_NEXTIME=@array_merge($CONF_NEXTIME_2,$CONF_NEXTIME);
if(@isset($CONF_NEXTIME['report_format'])){
    if($CONF_NEXTIME['report_format']=="Excel2007"){
        $CONF_NEXTIME['report_output_format']="PHPExcel_Writer_Excel2007";
        $CONF_NEXTIME['report_extension']='xlsx';
    }else{
        $CONF_NEXTIME['report_output_format']="PHPExcel_Writer_Excel5";
        $CONF_NEXTIME['report_extension']='xls';
    }
}


?>