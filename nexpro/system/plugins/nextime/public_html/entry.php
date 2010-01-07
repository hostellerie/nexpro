<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | entry.php                                                                 |
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

//this page will provide a list of abilities for the end user to do.
require_once('../lib-common.php');
require_once("nextime.class.php");

echo COM_siteHeader('none');

$uid=$_USER['uid'];
if($uid<1){
    echo $LANG_NEXTIME['not_logged_in'];  
    echo COM_siteFooter();
    exit(0);  
}

$start_date=COM_applyFilter($_GET['start_date']);
$end_date=COM_applyFilter($_GET['end_date']);
$startStamp=strtotime($start_date);
$endStamp=strtotime($end_date);
$numberOfDays=intval(($endStamp-$startStamp)/60/60/24)+1;



if($endStamp<$startStamp){ //Hey! You can't have an end date thats less than the start date...
    echo '<br><br><a href="index.php">' . $LANG_NEXTIME['end_date_before_start'];
    echo  $LANG_NEXTIME['back_link_label'] . '</a>' ;
    echo COM_siteFooter();
    exit(0);
}

$ts=new nexTime();

$T = new Template($CONF_NEXTIME['template_path']);
$T->set_file (array (
    'page'        => 'entry.thtml',
    'javascript'  => 'nextime.js.thtml',
    ));
$T->set_var('site_url',$_CONF['site_url']);
$T->set_var($LANG_NEXTIME);
$T->set_var('allfields',$ts->getTableColumns('nextime_timesheet_entry'));
$T->set_var('start_date',$start_date);
$T->set_var('end_date',$end_date);

$T->set_var('emp',$_USER['uid']);
$T->set_var('approved_by',$_USER['uid']);
$T->parse('output','javascript');


$output='';
$output.=$ts->generateTableHeader(false, false, $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$uid));
$retval=$ts->generateTimesheetRows($uid,$startStamp,$numberOfDays);
$output.=$retval[1];
$output.=$ts->generateTableFooter(false,$retval[2],$retval[3], $retval[4], $retval[5], $ts->determineIfItemIsInLockRangeByDateStamp($startStamp,$uid));
$output.=$ts->generateTotalRowCount($retval[0]);
$T->set_var('timesheet',$output);

$T->parse('output','page',true);
echo $T->finish($T->get_var('output'));





echo "<br>";
echo COM_siteFooter();


?>