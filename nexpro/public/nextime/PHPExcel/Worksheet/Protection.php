<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.5, 2007-12-24
 */


/** PHPExcel_Shared_PasswordHasher */
require_once 'PHPExcel/Shared/PasswordHasher.php';


/**
 * PHPExcel_Worksheet_Protection
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_Protection
{
	/**
	 * Sheet
	 *
	 * @var boolean
	 */
	private $_sheet;
	
	/**
	 * Objects
	 *
	 * @var boolean
	 */
	private $_objects;
	
	/**
	 * Scenarios
	 *
	 * @var boolean
	 */
	private $_scenarios;
	
	/**
	 * Format cells
	 *
	 * @var boolean
	 */
	private $_formatCells;
	
	/**
	 * Format columns
	 *
	 * @var boolean
	 */
	private $_formatColumns;
	
	/**
	 * Format rows
	 *
	 * @var boolean
	 */
	private $_formatRows;
	
	/**
	 * Insert columns
	 *
	 * @var boolean
	 */
	private $_insertColumns;
	
	/**
	 * Insert rows
	 *
	 * @var boolean
	 */
	private $_insertRows;
	
	/**
	 * Insert hyperlinks
	 *
	 * @var boolean
	 */
	private $_insertHyperlinks;
	
	/**
	 * Delete columns
	 *
	 * @var boolean
	 */
	private $_deleteColumns;
	
	/**
	 * Delete rows
	 *
	 * @var boolean
	 */
	private $_deleteRows;
	
	/**
	 * Select locked cells
	 *
	 * @var boolean
	 */
	private $_selectLockedCells;
	
	/**
	 * Sort
	 *
	 * @var boolean
	 */
	private $_sort;
	
	/**
	 * AutoFilter
	 *
	 * @var boolean
	 */
	private $_autoFilter;
	
	/**
	 * Pivot tables
	 *
	 * @var boolean
	 */
	private $_pivotTables;
	
	/**
	 * Select unlocked cells
	 *
	 * @var boolean
	 */
	private $_selectUnlockedCells;
			
	/**
	 * Password
	 *
	 * @var string
	 */
	private $_password;
	
    /**
     * Create a new PHPExcel_Worksheet_Protection
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_sheet					= false;
    	$this->_objects					= false;
    	$this->_scenarios				= false;
    	$this->_formatCells				= false;
    	$this->_formatColumns			= false;
    	$this->_formatRows				= false;
    	$this->_insertColumns			= false;
    	$this->_insertRows				= false;
    	$this->_insertHyperlinks		= false;
    	$this->_deleteColumns			= false;
    	$this->_deleteRows				= false;
    	$this->_selectLockedCells		= false;
    	$this->_sort					= false;
    	$this->_autoFilter				= false;
    	$this->_pivotTables				= false;
    	$this->_selectUnlockedCells		= false;
    	$this->_password				= '';
    }

    /**
     * Is some sort of protection enabled?
     *
     * @return boolean
     */
    function isProtectionEnabled() {
    	return 	$this->_sheet ||
				$this->_objects ||
				$this->_scenarios ||
				$this->_formatCells ||
				$this->_formatColumns ||
				$this->_formatRows ||
				$this->_insertColumns ||
				$this->_insertRows ||
				$this->_insertHyperlinks ||
				$this->_deleteColumns ||
				$this->_deleteRows ||
				$this->_selectLockedCells ||
				$this->_sort ||
				$this->_autoFilter ||
				$this->_pivotTables ||
				$this->_selectUnlockedCells;
    }
    
    /**
     * Get Sheet
     *
     * @return boolean
     */
    function getSheet() {
    	return $this->_sheet;
    }
    
    /**
     * Set Sheet
     *
     * @param boolean $pValue
     */
    function setSheet($pValue = false) {
    	$this->_sheet = $pValue;
    }

    /**
     * Get Objects
     *
     * @return boolean
     */
    function getObjects() {
    	return $this->_objects;
    }
    
    /**
     * Set Objects
     *
     * @param boolean $pValue
     */
    function setObjects($pValue = false) {
    	$this->_objects = $pValue;
    }

    /**
     * Get Scenarios
     *
     * @return boolean
     */
    function getScenarios() {
    	return $this->_scenarios;
    }
    
    /**
     * Set Scenarios
     *
     * @param boolean $pValue
     */
    function setScenarios($pValue = false) {
    	$this->_scenarios = $pValue;
    }

    /**
     * Get FormatCells
     *
     * @return boolean
     */
    function getFormatCells() {
    	return $this->_formatCells;
    }
    
    /**
     * Set FormatCells
     *
     * @param boolean $pValue
     */
    function setFormatCells($pValue = false) {
    	$this->_formatCells = $pValue;
    }

    /**
     * Get FormatColumns
     *
     * @return boolean
     */
    function getFormatColumns() {
    	return $this->_formatColumns;
    }
    
    /**
     * Set FormatColumns
     *
     * @param boolean $pValue
     */
    function setFormatColumns($pValue = false) {
    	$this->_formatColumns = $pValue;
    }

    /**
     * Get FormatRows
     *
     * @return boolean
     */
    function getFormatRows() {
    	return $this->_formatRows;
    }
    
    /**
     * Set FormatRows
     *
     * @param boolean $pValue
     */
    function setFormatRows($pValue = false) {
    	$this->_formatRows = $pValue;
    }

    /**
     * Get InsertColumns
     *
     * @return boolean
     */
    function getInsertColumns() {
    	return $this->_insertColumns;
    }
    
    /**
     * Set InsertColumns
     *
     * @param boolean $pValue
     */
    function setInsertColumns($pValue = false) {
    	$this->_insertColumns = $pValue;
    }

    /**
     * Get InsertRows
     *
     * @return boolean
     */
    function getInsertRows() {
    	return $this->_insertRows;
    }
    
    /**
     * Set InsertRows
     *
     * @param boolean $pValue
     */
    function setInsertRows($pValue = false) {
    	$this->_insertRows = $pValue;
    }

    /**
     * Get InsertHyperlinks
     *
     * @return boolean
     */
    function getInsertHyperlinks() {
    	return $this->_insertHyperlinks;
    }
    
    /**
     * Set InsertHyperlinks
     *
     * @param boolean $pValue
     */
    function setInsertHyperlinks($pValue = false) {
    	$this->_insertHyperlinks = $pValue;
    }

    /**
     * Get DeleteColumns
     *
     * @return boolean
     */
    function getDeleteColumns() {
    	return $this->_deleteColumns;
    }
    
    /**
     * Set DeleteColumns
     *
     * @param boolean $pValue
     */
    function setDeleteColumns($pValue = false) {
    	$this->_deleteColumns = $pValue;
    }

    /**
     * Get DeleteRows
     *
     * @return boolean
     */
    function getDeleteRows() {
    	return $this->_deleteRows;
    }
    
    /**
     * Set DeleteRows
     *
     * @param boolean $pValue
     */
    function setDeleteRows($pValue = false) {
    	$this->_deleteRows = $pValue;
    }

    /**
     * Get SelectLockedCells
     *
     * @return boolean
     */
    function getSelectLockedCells() {
    	return $this->_selectLockedCells;
    }
    
    /**
     * Set SelectLockedCells
     *
     * @param boolean $pValue
     */
    function setSelectLockedCells($pValue = false) {
    	$this->_selectLockedCells = $pValue;
    }

    /**
     * Get Sort
     *
     * @return boolean
     */
    function getSort() {
    	return $this->_sort;
    }
    
    /**
     * Set Sort
     *
     * @param boolean $pValue
     */
    function setSort($pValue = false) {
    	$this->_sort = $pValue;
    }

    /**
     * Get AutoFilter
     *
     * @return boolean
     */
    function getAutoFilter() {
    	return $this->_autoFilter;
    }
    
    /**
     * Set AutoFilter
     *
     * @param boolean $pValue
     */
    function setAutoFilter($pValue = false) {
    	$this->_autoFilter = $pValue;
    }

    /**
     * Get PivotTables
     *
     * @return boolean
     */
    function getPivotTables() {
    	return $this->_pivotTables;
    }
    
    /**
     * Set PivotTables
     *
     * @param boolean $pValue
     */
    function setPivotTables($pValue = false) {
    	$this->_pivotTables = $pValue;
    }

    /**
     * Get SelectUnlockedCells
     *
     * @return boolean
     */
    function getSelectUnlockedCells() {
    	return $this->_selectUnlockedCells;
    }
    
    /**
     * Set SelectUnlockedCells
     *
     * @param boolean $pValue
     */
    function setSelectUnlockedCells($pValue = false) {
    	$this->_selectUnlockedCells = $pValue;
    }
    
    /**
     * Get Password (hashed)
     *
     * @return string
     */
    function getPassword() {
    	return $this->_password;
    }

    /**
     * Set Password
     *
     * @param string 	$pValue
     * @param boolean 	$pAlreadyHashed If the password has already been hashed, set this to true
     */
    function setPassword($pValue = '', $pAlreadyHashed = false) {
    	if (!$pAlreadyHashed) {
    		$pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
    	}
		$this->_password = $pValue;
    }

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
