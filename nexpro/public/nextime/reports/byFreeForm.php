<?php
$totalsArray=array();
$ts=new nexTime();
        if($whichManager>0){
            $csv=$ts->getCSVListOfAssignedEmployees($whichManager,true);
        }else{
            $csv=$ts->getAllUIDsWhichHaveSupervisors();
        }
        if($csv=='') $csv='0';

        $timesthru=0;




    $objPHPExcel->setActiveSheetIndex($timesthru);
    $objPHPExcel->getActiveSheet()->setTitle($task);

    //set the top left A1 cell to the logo image
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('Logo');
    $objDrawing->setDescription('Logo');
    $objDrawing->setPath('./images/logo.png');
    $objDrawing->setHeight(36);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(27.75);
    //end top left image

    //generate header of report
    $objPHPExcel->getActiveSheet()->setCellValue('H1', $LANG_NEXTIME_REPORTS['title_by_freeform']);
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('S2')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->setCellValue('S2', $LANG_NEXTIME_REPORTS['period_from']);
    $objPHPExcel->getActiveSheet()->getStyle('S3')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->setCellValue('S3', $LANG_NEXTIME_REPORTS['period_to']);
    $objPHPExcel->getActiveSheet()->getStyle('U2')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->setCellValue('U2', $start_date);
    $objPHPExcel->getActiveSheet()->getStyle('U3')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->setCellValue('U3', $end_date);
    $objPHPExcel->getActiveSheet()->mergeCells('O2:P2');
    $objPHPExcel->getActiveSheet()->mergeCells('O3:P3');
    //end of header information....

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10.71);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10.71);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10.71);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.71);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10.71);
    //column names:
    $rowCounter=8;
    $xlscol=1;
    foreach($LANG_NEXTIME_REPORT_FREE_FORM_COLUMNS as $dbcol=>$label){
        $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $label);
        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getFont()->setBold(true);

        $xlscol+=1;
     }


    //now to add the data
    $oneDay=86400;
    $rowCounter=9; //we start the output at row 9

    //we will select all of the data from the timesheet table and dump it out
    $sql  ="SELECT  FROM_UNIXTIME(a.datestamp,'%Y/%m/%d') as 'thedate',b.fullname, b.uid, c.tech_number,a.* ";
    $sql .="FROM {$_TABLES['nextime_timesheet_entry']} a ";
    $sql .="INNER JOIN {$_TABLES['users']} b ON a.uid=b.uid ";
    $sql .="LEFT JOIN {$_TABLES['nextime_extra_user_data']} c on a.uid=c.uid ";
    $sql .="WHERE a.uid in ($csv) ";
    $sql .="AND datestamp>=({$startStamp}-4600) AND datestamp<=({$endStamp}+4600) AND ( approved=1 ";
    if($showUNApproved!=1){ //if $showUNApproved==1 then do not filter on approved
        //$sql .= "AND approved=1 ";
    }else{
        $sql .=" OR (approved=1 OR approved=0) ";
    }
    if($showRejected==1){
        $sql .="OR rejected=1 ";
    }else{
        $sql .="OR rejected=0 ";
    }
    $sql .=") ORDER BY datestamp ASC";
    $res=DB_query($sql);
    while($A=DB_fetchArray($res)){
        //now cycle thru each element in the select
        $xlscol=1;
        foreach($LANG_NEXTIME_REPORT_FREE_FORM_COLUMNS as $dbcol=>$label){

            switch($dbcol){
                case 'total_reg_hours':
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $ts->getTotalHRSFromID($A['id']));
                    break;
                case 'project_number':
                    $output=nexlistValue($CONF_NEXTIME['nexlist_nextime_projects'],$A['project_id'],0);
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $output );
                    break;
                case 'nextime_activity_id':
                    $output=nexlistValue($CONF_NEXTIME['nexlist_nextime_activities'],$A['nextime_activity_id'],0);
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $output );
                    break;
                case 'project_id':
                    $output=nexlistValue($CONF_NEXTIME['nexlist_nextime_projects'],$A['project_id'],1);
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $output );
                    break;
                case 'task_id':
                    $output=nexlistValue($CONF_NEXTIME['nexlist_timesheet_tasks'],$A['task_id'],1);
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $output );
                    break;

                default:
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $A[$dbcol]);
                    break;
            }


            $xlscol+=1;
        }
        $rowCounter +=1;
    }
    $endRowCounter=$rowCounter-1;







?>