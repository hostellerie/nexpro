<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | reports.php                                                               |
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


require_once('../lib-common.php');
require_once("nextime.class.php");


require_once("PHPExcel.php");
require_once('PHPExcel/RichText.php');
require_once('PHPExcel/Cell.php');
require_once('PHPExcel/Writer/Excel5.php');
require_once('PHPExcel/Writer/Excel2007.php');


$op=COM_applyFilter($_REQUEST['op']);
$start_date=COM_applyFilter($_REQUEST['start_date']);
$end_date=COM_applyFilter($_REQUEST['end_date']);
$startStamp=strtotime($start_date);
$endStamp=strtotime($end_date);
$numberOfDays=intval(($endStamp-$startStamp)/60/60/24)+1;
$whichManager=COM_applyFilter($_REQUEST['whichmanager'],true);

$showUNApproved=COM_applyFilter($_REQUEST['showunapproved'],true);
$showRejected=COM_applyFilter($_REQUEST['showrejected'],true);


$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("nexTime Timesheet System");
$objPHPExcel->getProperties()->setLastModifiedBy("nexTime Timesheet System");
$objPHPExcel->getProperties()->setTitle("Office XLS Document");
$objPHPExcel->getProperties()->setSubject("Office XLS Document");
$objPHPExcel->getProperties()->setDescription("Timesheet report");
$objPHPExcel->getProperties()->setKeywords("Timesheet Report");
$objPHPExcel->getProperties()->setCategory("Timesheet report result file");



if($start_date==''){
    $startStamp=time()-(86400*$CONF_NEXTIME['number_of_days_before']);
}else{
    $startStamp=strtotime($start_date);
}
if($end_date==''){
    if($start_date!=''){
        $endStamp=$startStamp+(86400*$CONF_NEXTIME['number_of_days_after']);
    }else{
        $endStamp=time()+(86400*$CONF_NEXTIME['number_of_days_after']);
    }
}else{
    $endStamp=strtotime($end_date);
}
$numberOfDays=intval(($endStamp-$startStamp)/60/60/24)+1;




switch($op){
    case 'byemployee':
        include('reports/byEmployee.php');
        $objWriter = new $CONF_NEXTIME['report_output_format']($objPHPExcel);
        $reportname=  date('Y-m-d H.i') .' By Employee Report.'.$CONF_NEXTIME['report_extension'];
        $url=$CONF_NEXTIME['base_path'] . 'reports/output/'.$reportname;
        $objWriter->save($CONF_NEXTIME['path_to_reports'].$reportname);
        $output="<a href='{$url}' target='_new' onclick='closeByEmployeePanel();'>Click here to view the report</a>";
        $error="";
        break;


    case 'bytask':
        $nexlistID = COM_applyFilter($_REQUEST['whichtask'],true);
        com_errorlog($nexlistID);
        $task = COM_applyFilter($_REQUEST['taskname'],false);

        include('reports/byTask.php');
        $objWriter = new $CONF_NEXTIME['report_output_format']($objPHPExcel);
        $reportname=  date('Y-m-d H.i') .' By Task Report.'.$CONF_NEXTIME['report_extension'];
        $url=$CONF_NEXTIME['base_path'] . 'reports/output/'.$reportname;
        $objWriter->save($CONF_NEXTIME['path_to_reports'].$reportname);
        $output="<a href='{$url}' target='_new' onclick='closeByTaskPanel();'>Click here to view the report</a>";

        $error="";
        break;


    case 'byproject':

        $nexlistID = COM_applyFilter($_REQUEST['whichproject'],true);
        $task = COM_applyFilter($_REQUEST['projectname'],false);

        include('reports/byProject.php');

        $objWriter = new $CONF_NEXTIME['report_output_format']($objPHPExcel);

        $reportname=  date('Y-m-d H.i') .' By Project Report.'.$CONF_NEXTIME['report_extension'];
        $url=$CONF_NEXTIME['base_path'] . 'reports/output/'.$reportname;
        $objWriter->save($CONF_NEXTIME['path_to_reports'].$reportname);
        $output="<a href='{$url}' target='_new' onclick='closeByProjectPanel();'>Click here to view the report</a>";

        $error="";
        break;

    case 'byfreeform':
        include('reports/byFreeForm.php');
        $objWriter = new $CONF_NEXTIME['report_output_format']($objPHPExcel);
        $reportname=  date('Y-m-d H.i') .' Free Form Report.'.$CONF_NEXTIME['report_extension'];
        $url=$CONF_NEXTIME['base_path'] . 'reports/output/'.$reportname;
        $objWriter->save($CONF_NEXTIME['path_to_reports'].$reportname);
        $output="<a href='{$url}' target='_new' onclick='closeByFreeFormPanel();'>Click here to view the report</a>";
        $error="";
        break;


}

//self pruning mechanism that will delete any report from the directory if its
//older than 10 minutes.
if ($handle = opendir($CONF_NEXTIME['path_to_reports'])) {
   while (false !== ($file = readdir($handle))) {
        if($file!='.' && $file!='..'){
             $fp = @fopen($CONF_NEXTIME['path_to_reports'].$file, "r");
             $fstat = @fstat($fp);
             @fclose($fp);
             if($fstat[ctime]<(time()-600)){
                @unlink($CONF_NEXTIME['path_to_reports'].$file);
             }
         
        }
   }
   closedir($handle);
}



$output=htmlentities($output);
$error=htmlentities($error);
$op=htmlentities($op);

$retval = "<result>";
$retval .= "<error>$error</error>";
$retval .= "<op>$op</op>";
$retval .= "<output>$output</output>";
$retval .= "</result>";

header("Cache-Control: no-store, no-cache, must-revalidate");
header("content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
echo $retval;



?>