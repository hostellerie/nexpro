<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Sept. 23, 2009                                                      |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | base_table_class.php - base table class for nextime                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |  Author: Randy Kolenko   - randy@nextide.ca                               |
// +---------------------------------------------------------------------------+
// | Base table class which each table will simply extend                      |
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

class TABLE{
    var $fulltablename;     // table name
    var $tablename;         // alias name
    var $fieldlist;         // list of fields in this table
    var $fieldvals;         //list of values for fields
    var $numberOfColumns;   //holds the number of columns dynamically determined in this variable
    var $fetchedData;        //holds the last DB_fetchArray data


    function clear_column_values($setValueTo=NULL){
        $this->fieldvals=array();
        for($cntr=0;$cntr<$this->numberOfColumns;$cntr++){
            $this->fieldvals[$this->fieldlist[$cntr]]=$setValueTo;
        }
    }

    function set_column_value($col,$setValueTo=NULL){
        $this->fieldvals[$col]=$setValueTo;
    }

    function fetchNextRow($skipFetch=false,$alreadyFetchedData=NULL){
        if(isset($alreadyFetchedData)){ //if we're passing in an array of fetched data, set our current table's fetched data to it
            $this->fetchedData=$alreadyFetchedData;
        }
        if(isset($this->resultSet) || isset($alreadyFetchedData)){  //essentially if we've got an explicitly set fetched data set, enter into the loop
            if(!$skipFetch) {  //we'll skip the fetch if we've forced in some data
                $this->fetchedData=DB_fetchArray($this->resultSet);
            }
            $this->fieldvals=array();
                for($cntr=0;$cntr<$this->numberOfColumns;$cntr++){
                    $this->fieldvals[$this->fieldlist[$cntr]]=$this->fetchedData[$this->fieldlist[$cntr]];
                }
        }
    }


    function populateNextFetchedDataArray(){
        $this->fetchedData=DB_fetchArray($this->resultSet);
    }


    function returnColumns(){
        $retval='';
        for($cntr=0;$cntr<$this->numberOfColumns;$cntr++){
            if($retval!='') $retval .=",";
            $retval.=$this->fieldlist[$cntr];
        }
        return $retval;
    }

}


?>