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
            $objPHPExcel->getActiveSheet()->setCellValue('H1', $LANG_NEXTIME_REPORTS['title_by_task']);
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

            //start columns for output..
            //starts at A7

            $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(3);
            $objPHPExcel->getActiveSheet()->getStyle('A7')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A7')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10.71);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('B7', $LANG_NEXTIME_REPORTS['date']);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->setCellValue('C7', $LANG_NEXTIME_REPORTS['task']);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(4.14);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C7')->getAlignment()->setTextRotation(-90);

            $colnum=4;
            $skipheadercol=0;
            foreach($LANG_NEXTIME_REPORT_COLUMNS as $key=>$val){
                if($skipheadercol>3){
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getAlignment()->setTextRotation(-90);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getFont()->setName('Arial');
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$colnum].'7', $LANG_NEXTIME_REPORT_COLUMNS[$key]);
                    if($key=='comment'){
                        $objPHPExcel->getActiveSheet()->getColumnDimension($CONF_NEXTIME['report_columns'][$colnum])->setWidth(12);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getAlignment()->setTextRotation(0);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }else{
                        $objPHPExcel->getActiveSheet()->getColumnDimension($CONF_NEXTIME['report_columns'][$colnum])->setWidth(4.14);
                    }
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$colnum].'7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $colnum+=1;
                }
                $skipheadercol+=1;
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(6.5);
            //now to add the data
            $oneDay=86400;
            $rowCounter=9; //we start the output at row 9

            //we now cycle through each user, generating outputtable rows for each user for THIS task
            //$sql="Select * from {$_TABLES['users']} where uid in ($csv)";
            $sql  ="Select {$_TABLES['users']}.uid,{$_TABLES['users']}.fullname from {$_TABLES['users']} left join {$_TABLES['nextime_extra_user_data']} on {$_TABLES['users']}.uid = {$_TABLES['nextime_extra_user_data']}.uid where {$_TABLES['users']}.uid in ($csv) AND ";
            $sql .="( {$_TABLES['nextime_extra_user_data']}.special_exclusion <>1 OR {$_TABLES['nextime_extra_user_data']}.special_exclusion is null)";
            $res=DB_query($sql);

            while($A=DB_fetchArray($res)){
                //echo out who this is
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowCounter, $A['fullname']);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCounter)->getFont()->setBold(true);
                $rowCounter+=1;
                //now, for THIS user, select out of the timesheet records where the TASKID==$nexlistID
                //we will also use our filter information
                $sql  ="SELECT * FROM {$_TABLES['nextime_timesheet_entry']} WHERE uid='{$A['uid']}' AND task_id='{$nexlistID}' ";
                $sql .="AND datestamp>=({$startStamp}-4600) AND datestamp<=({$endStamp}+4600) AND (approved=1 ";
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
                //now cycle thrue each of these..

                $taskcount=1;
                $startingRowCount=$rowCounter;
                while($X=DB_fetchArray($perUserRes)){
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowCounter, date('l',$X['datestamp']));
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowCounter, date('Y/m/d',$X['datestamp']));
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowCounter, $taskcount);
                    //and now for each column, starting at offset 5
                    $colcount=0;
                    $xlscol=4;  //column d
                    foreach($LANG_NEXTIME_REPORT_COLUMNS as $dbcol=>$name){
                        if($colcount>3){ //skip out 0-4
                            if($dbcol=='total_reg_hours'){
                                $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $ts->getTotalHRSFromID($X['id']));
                            }else{
                                $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, $X[$dbcol]);
                            }
                            $xlscol+=1;
                        }
                        $colcount+=1;
                    }

                    $taskcount+=1;
                    $rowCounter+=1;
                }
                $endRowCounter=$rowCounter-1;
                //give a totals line....
                //totals line goes from $startingRowCount to $endRowCounter
                $colcount=0;
                $xlscol=4;  //column d
                foreach($LANG_NEXTIME_REPORT_COLUMNS as $dbcol=>$name){
                    if($colcount>3 && $dbcol!='comment' && $startingRowCount != $rowCounter){ //skip out 0-4
                        $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, "=SUM({$CONF_NEXTIME['report_columns'][$xlscol]}{$startingRowCount}..{$CONF_NEXTIME['report_columns'][$xlscol]}{$endRowCounter})");
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $xlscol+=1;
                    }elseif($colcount>4 && $dbcol!='comment' && $startingRowCount == $rowCounter){
                        $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, "0");
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $xlscol+=1;
                    }
                    $colcount+=1;
                }
                //RK - this is the A.. column that can prefix the row with TOTALS for each per user entry..
                //$objPHPExcel->getActiveSheet()->setCellValue('A' . $rowCounter, "TOTALS");
                //trap this row in our "totals" array
                $totalsArray[]=$rowCounter;
                $rowCounter+=2;
            }//end while $A    //cycling thru each user



            //$rowCounter holds the LAST row count.
            //now show the totals area
            $finalRowCount=$rowCounter-1;
            $rowCounter+=2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowCounter, 'Overall Total:');
            //now for each column that is being displayed...

            $colcount=0;
            $xlscol=4;  //column d
            foreach($LANG_NEXTIME_REPORT_COLUMNS as $dbcol=>$name){
                if($colcount>4 && $dbcol!='comment'){ //skip out 0-4
                    $sumstring='';
                    foreach($totalsArray as $val){
                        if($sumstring!='') $sumstring.=",";
                        $sumstring.=$CONF_NEXTIME['report_columns'][$xlscol].$val;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter, "=SUM({$sumstring})");

                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($CONF_NEXTIME['report_columns'][$xlscol] . $rowCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $xlscol+=1;
                }
                $colcount+=1;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('I' .($finalRowCount+10), $LANG_NEXTIME_REPORTS['date']);
            $objPHPExcel->getActiveSheet()->setCellValue('M' .($finalRowCount+10), date('Y/m/d h:m A'));
            $objPHPExcel->getActiveSheet()->mergeCells("M" .($finalRowCount+10).":Q".($finalRowCount+10));
            $objPHPExcel->getActiveSheet()->getStyle("M" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("N" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("O" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("P" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle("Q" .($finalRowCount+10))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          //  $timesthru+=1;
        //}//end of each task loop



?>