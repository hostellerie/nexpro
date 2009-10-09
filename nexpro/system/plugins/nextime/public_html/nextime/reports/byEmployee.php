<?php

$_glob_fit_height = 3;
$_glob_fit_width = 1;

$ts=new nexTime();
        if($whichManager>0){
            $csv=$ts->getCSVListOfAssignedEmployees($whichManager,true);
        }else{
            $csv=$ts->getAllUIDsWhichHaveSupervisors();
        }


        if($csv=='') $csv='0';

        $sql  ="Select {$_TABLES['users']}.uid,{$_TABLES['users']}.fullname from {$_TABLES['users']} left outer join {$_TABLES['nextime_extra_user_data']} on {$_TABLES['users']}.uid = {$_TABLES['nextime_extra_user_data']}.uid where {$_TABLES['users']}.uid in ($csv) AND ";
        $sql .="( {$_TABLES['nextime_extra_user_data']}.special_exclusion <>1 OR {$_TABLES['nextime_extra_user_data']}.special_exclusion is null)";

        $res=DB_query($sql);
        $nrows=DB_numRows($res);
        $timesthru=0;
        while($A=DB_fetchArray($res)){
            if($timesthru>0){
                $objPHPExcel->createSheet();
                //$objPHPExcel->setActiveSheetIndex($timesthru);
            }
            $objPHPExcel->setActiveSheetIndex($timesthru);
            $techNumber=DB_getItem($_TABLES['nextime_extra_user_data'],"tech_number","uid={$A['uid']}");
            $objPHPExcel->getActiveSheet()->setTitle($A['fullname'] . ' - #' . $techNumber);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(3);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 9);
            $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&P of &N');
            $objPHPExcel->getActiveSheet()->getHeaderFooter()->setEvenFooter('&L&P of &N');
            $tech_number = DB_getItem($_TABLES['nextime_extra_user_data'],'tech_number','uid='.$A['uid']);
            $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader("&R&14 {$tech_number}\n{$A['fullname']}");
            $objPHPExcel->getActiveSheet()->getHeaderFooter()->setEvenHeader("&R&14 {$tech_number}\n{$A['fullname']}");

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
            $objPHPExcel->getActiveSheet()->setCellValue('Q3', $LANG_NEXTIME_REPORTS['technician_name']);
            $objPHPExcel->getActiveSheet()->getStyle('Q3')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', $LANG_NEXTIME_REPORTS['title']);
            $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->setCellValue('Q2', $LANG_NEXTIME_REPORTS['technician_number']);
            $objPHPExcel->getActiveSheet()->getStyle('Q2')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('Q5', $LANG_NEXTIME_REPORTS['technician_region']);
            //$objPHPExcel->getActiveSheet()->getStyle('Q5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('Q5')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('Q4', $LANG_NEXTIME_REPORTS['supervisor_name']);
            //$objPHPExcel->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('Q4')->getFont()->setName('Arial');
            //$objPHPExcel->getActiveSheet()->setCellValue('A4', $LANG_NEXTIME_REPORTS['supervisor_title']);
            //$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
            //$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setName('Arial');



            $objPHPExcel->getActiveSheet()->getStyle('U3')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('U3', $A['fullname']);
            $objPHPExcel->getActiveSheet()->getStyle('U3')->getFont()->setSize(14);
            //$objPHPExcel->getActiveSheet()->getStyle('U3')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('U4')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('U4', DB_getItem($_TABLES['users'],'fullname','uid='.$ts->getSupervisorUID($A['uid'])));
            //$objPHPExcel->getActiveSheet()->getStyle('D4')->getFont()->setName('Arial');
            //$objPHPExcel->getActiveSheet()->setCellValue('D4','');
            $objPHPExcel->getActiveSheet()->getStyle('U2')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('U2', $tech_number);
            $objPHPExcel->getActiveSheet()->getStyle('U2')->getFont()->setSize(14);
            //$objPHPExcel->getActiveSheet()->getStyle('U2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('U5')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('U5', nexlistValue($CONF_NEXTIME['nexlist_user_locations'],  DB_getItem($_TABLES['nextime_extra_user_data'],'region','uid='.$A['uid'])   ,0));

            $objPHPExcel->getActiveSheet()->getStyle('Q6')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('Q6', $LANG_NEXTIME_REPORTS['period_from']);
            $objPHPExcel->getActiveSheet()->getStyle('Q7')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('Q7', $LANG_NEXTIME_REPORTS['period_to']);

            $objPHPExcel->getActiveSheet()->getStyle('U6')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('U6', $start_date);
            $objPHPExcel->getActiveSheet()->getStyle('U7')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('U7', $end_date);

            $objPHPExcel->getActiveSheet()->mergeCells('Q2:T2');
            $objPHPExcel->getActiveSheet()->mergeCells('Q3:T3');
            $objPHPExcel->getActiveSheet()->mergeCells('Q4:T4');
            $objPHPExcel->getActiveSheet()->mergeCells('Q5:T5');
            $objPHPExcel->getActiveSheet()->mergeCells('Q6:T6');
            $objPHPExcel->getActiveSheet()->mergeCells('Q7:T7');

            $objPHPExcel->getActiveSheet()->getStyle('Q2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('Q3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('Q5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('Q7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('U2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('U3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('U4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('U5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('U6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('U7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            //end of header information....

            //start columns for output..
            //starts at A7

            $objPHPExcel->getActiveSheet()->getRowDimension('10')->setRowHeight(3);
            $objPHPExcel->getActiveSheet()->getStyle('A9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A9')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A9')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10.71);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('B9', $LANG_NEXTIME_REPORTS['date']);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $colnum=3;
            foreach($LANG_NEXTIME_REPORT_COLUMNS as $key=>$val){
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getAlignment()->setTextRotation(-90);
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getAlignment()->setHorizontal('center');
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getFont()->setName('Arial');
                $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum].'9', $LANG_NEXTIME_REPORT_COLUMNS[$key]);
                if($key=='comment'){
                    $objPHPExcel->getActiveSheet()->getColumnDimension($CONF_NEXTIME['report_columns'][$colnum])->setWidth(36);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getAlignment()->setTextRotation(0);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }else{
                    $objPHPExcel->getActiveSheet()->getColumnDimension($CONF_NEXTIME['report_columns'][$colnum])->setWidth(6.57);
                }
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'9')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $colnum+=1;
            }
            //special cases to the for loop above
            $objPHPExcel->getActiveSheet()->getStyle('F9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('R9')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('R9')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('R10')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('R10')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('A11')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B11')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('R9')->getFont()->setBold(true);
            for ($colnum=2; $colnum<=6; $colnum++) {
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . '9')->getAlignment()->setTextRotation(0);
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . '9')->getAlignment()->setHorizontal('center');
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . '9')->getAlignment()->setVertical('center');
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . '9')->getFont()->setBold(true);
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(6.5);
            //now to add the data
            $oneDay=86400;
            $rowCounter=11; //we start the output at row 9
            for($cntr=0;$cntr<$numberOfDays;$cntr++){
                //formulate the date
                $dt=$startStamp+($oneDay*$cntr);

                $testdate=strftime("%Y/%m/%d 00:00:00",$dt);
                $testintdate=strtotime($testdate);

                if($testintdate==$dt-3600){
	                $dt=testintdate;
                }elseif($testintdate==$dt){

                }else{
	                $dt+=3600;
                }

                $today=date("w",$dt);
                $day=$LANG_NEXTIME[$CONF_NEXTIME['day_offsets'][$today]];
                $daystring=date("Y/m/d",$dt);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowCounter, $day);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowCounter, $daystring);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('A'. $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('B'. $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                //now, fetch each date's entries here
                //but take the filters into account...
				$lowerextent=$dt-14400;
				$upperextent=$dt+14400;

                $sql  ="SELECT * FROM {$_TABLES['nextime_timesheet_entry']} WHERE uid='{$A['uid']}'  ";
                $sql .="AND datestamp<='{$upperextent}' AND datestamp>='{$lowerextent}' AND ( approved=1 ";
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
                $perUserRes=DB_query($sql);
                $numrows=DB_numRows($perUserRes);



                $colnum=4;  //set it to the next writable column we're after
                if($numrows==0){
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowCounter, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('C'. $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    foreach($LANG_NEXTIME_REPORT_COLUMNS as $key=>$value){  //cycle thru the available columns
                        if($key!='task_number'){
                            switch($key){
                                case 'comment':    //these case items are lookups... thus we'll just leave them blank
                                case 'nextime_activity_id':
                                case 'project_id':
                                case 'task_id':
                                    break;
                                default:
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, '');
                                    break;
                            }
                            if ($key == 'total_reg_hours') {
                                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setBold(true);
                            }
                            if ($colnum >= 7 || $colnum <= 20) {
                                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                            }
                            if ($key == 'standby') {
                                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getNumberFormat()->setFormatCode('"$"#0_-');
                            }

                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $day)->getAlignment()->setHorizontal('center');
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $day)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $day)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $day)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $day)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $colnum+=1;
                        }//end if
                    } //end foreach
                    $rowCounter+=1;
                }else{ //cycle thru the rows
                    $XX=DB_fetchArray($perUserRes);
                    for($taskCounter=0;$taskCounter<$numrows;$taskCounter++){

                        $colnum=4;  //again, setting it to the next writable column we're after

                        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowCounter, $taskCounter+1); //task number column
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('C'. $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                        foreach($LANG_NEXTIME_REPORT_COLUMNS as $key=>$value){  //we now fill in each column
                        if($key!='task_number'){
                            switch($key){
                                case 'project_number':
                                    $output=nexlistValue($CONF_NEXTIME['nexlist_nextime_projects'],$XX['project_id'],0);
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $output );
                                    break;
                                case 'nextime_activity_id':
                                    $output=nexlistValue($CONF_NEXTIME['nexlist_nextime_activities'],$XX['nextime_activity_id'],0);
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $output );
                                    break;
                                case 'project_id':
                                    $output=nexlistValue($CONF_NEXTIME['nexlist_nextime_projects'],$XX['project_id'],1);
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $output );
                                    break;
                                case 'task_id':
                                    $output=nexlistValue($CONF_NEXTIME['nexlist_timesheet_tasks'],$XX['task_id'],1);
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, $output );
                                    break;
                                case 'total_reg_hours':
                                    $output=$ts->getTotalHRSFromID($XX['id']);
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, ($output==0)?'':$output );
                                    break;
                                default:
                                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, ($XX[$key]==0)?'':$XX[$key]);
                                    break;
                            }
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            if ($key == 'total_reg_hours') {
                                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setBold(true);
                            }
                            if ($colnum >= 7 || $colnum <= 20) {
                                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                            }
                            if ($key == 'standby') {
                                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getNumberFormat()->setFormatCode('"$"#0_-');
                            }
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getAlignment()->setHorizontal('center');
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                            $colnum+=1;
                        }//end if
                    } //end foreach
                    $XX=DB_fetchArray($perUserRes);
                    $rowCounter+=1;
                    }//end for loop
                }//end else
            }//end loop for days

            //$rowCounter holds the LAST row count.
            //now show the totals area
            $finalRowCount=$rowCounter-1;
            //put a thick border around the entire main protion
            for ($colnum = 1; $colnum <= 21; $colnum++) {
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . '9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $finalRowCount)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            }
            for ($rownum = 9; $rownum < $rowCounter; $rownum++) {
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rownum)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                $objPHPExcel->getActiveSheet()->getStyle('U' . $rownum)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                $objPHPExcel->getActiveSheet()->getStyle('R' . $rownum)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                $objPHPExcel->getActiveSheet()->getStyle('R' . $rownum)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $rownum)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            }

            $rowCounter++;
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowCounter, 'Total Hours');
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getFont()->setSize(12);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            //now for each column that is being displayed...
            $colnum=7;
            foreach($LANG_NEXTIME_REPORT_COLUMNS as $key=>$value){
                switch($key){
                    case 'nextime_activity_id':    //these case items are lookups... thus we'll just leave them blank
                    case 'project_id':
                    case 'task_id':
                    case 'task_number':
                    case 'project_number':
                        break;
                    case 'comment':    //these case items are lookups... thus we'll just leave them blank
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $colnum+=1;
                        break;
                    default:
                        $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter, "=SUM({$CONF_NEXTIME['report_columns'][$colnum]}9..{$CONF_NEXTIME['report_columns'][$colnum]}{$finalRowCount})");
                        if ($key != 'standby') {
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                        }
                        else {
                            $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getNumberFormat()->setFormatCode('"$"#0_-');
                        }
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setSize(12);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getAlignment()->setHorizontal('center');
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $colnum+=1;
                        break;
                }
            }//end foreach
            $objPHPExcel->getActiveSheet()->getStyle('R' . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('R' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);


            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getFont()->setSize(12);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('F' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            //now for each column that is being displayed...
            $colnum=7;

            foreach($LANG_NEXTIME_REPORT_COLUMNS as $key=>$value){
                switch($key){
                    case 'nextime_activity_id':    //these case items are lookups... thus we'll just leave them blank
                    case 'project_id':
                    case 'task_id':
                    case 'task_number':
                    case 'project_number':
                        break;
                    case 'comment':    //these case items are lookups... thus we'll just leave them blank
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $colnum+=1;
                        break;
                    default:
                        $objPHPExcel->getActiveSheet()->getCell($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getFont()->setSize(12);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getAlignment()->setHorizontal('center');
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $colnum+=1;
                        break;
                }
            }//end foreach
            $objPHPExcel->getActiveSheet()->getStyle('R' . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
            $objPHPExcel->getActiveSheet()->getStyle('R' . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);


            //and now for the by-project hours breakdown area
            $output=$ts->selectByProjectHoursByUser($A['uid'],$startStamp,$endStamp);
            //m29 is the project number, n29 is the total hours and so on
            $prjrow=$finalRowCount+2;
            $objPHPExcel->getActiveSheet()->mergeCells("A{$prjrow}:D{$prjrow}");
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $prjrow, 'TOTAL PER PROJECT');
            $objPHPExcel->getActiveSheet()->getStyle('A' . $prjrow)->getFont()->setUnderline(UNDERLINE_SINGLE);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $prjrow)->getFont()->setSize(12);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $prjrow)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $prjrow)->getAlignment()->setHorizontal('center');
            $prjrow++;
            $prowStart = $prjrow;
            $flag = false;
            foreach($output as $key=>$value){
                $objPHPExcel->getActiveSheet()->mergeCells("A{$prjrow}:C{$prjrow}");
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $prjrow, $key);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $prjrow, $value);
                $prjrow+=1;
                $flag = true;
            }
            if ($flag) {
                $prowEnd = $prjrow - 1;
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $prjrow, "=SUM(D$prowStart:D$prowEnd)");
                $objPHPExcel->getActiveSheet()->getStyle('D' . $prjrow)->getFont()->setBold(true);
            }

            /*signatures and dates area
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($finalRowCount+8), $LANG_NEXTIME_REPORTS['employee_signature']);
            $objPHPExcel->getActiveSheet()->mergeCells("C" .($finalRowCount+8).":g".($finalRowCount+8));
            $objPHPExcel->getActiveSheet()->getStyle("C" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("D" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("E" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("F" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("G" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $objPHPExcel->getActiveSheet()->setCellValue('A' .($finalRowCount+10), $LANG_NEXTIME_REPORTS['supervisor_signature']);
            $objPHPExcel->getActiveSheet()->mergeCells("C" .($finalRowCount+10).":g".($finalRowCount+10));
            $objPHPExcel->getActiveSheet()->getStyle("C" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("D" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("E" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("F" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("G" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $objPHPExcel->getActiveSheet()->setCellValue('I' . ($finalRowCount+8), $LANG_NEXTIME_REPORTS['telephone_number']);
            $objPHPExcel->getActiveSheet()->mergeCells("M" .($finalRowCount+8).":Q".($finalRowCount+8));
            $objPHPExcel->getActiveSheet()->getStyle("M" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("N" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("O" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("P" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("Q" .($finalRowCount+8))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $objPHPExcel->getActiveSheet()->setCellValue('I' .($finalRowCount+10), $LANG_NEXTIME_REPORTS['date']);
            $objPHPExcel->getActiveSheet()->setCellValue('M' .($finalRowCount+10), date('Y/m/d h:m A'));
            $objPHPExcel->getActiveSheet()->mergeCells("M" .($finalRowCount+10).":Q".($finalRowCount+10));
            $objPHPExcel->getActiveSheet()->getStyle("M" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("N" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("O" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("P" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("Q" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            */

            $timesthru+=1;
        }//end of each user loop



?>