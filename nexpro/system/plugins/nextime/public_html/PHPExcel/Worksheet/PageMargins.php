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


/**
 * PHPExcel_Worksheet_PageMargins
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_PageMargins
{			
	/**
	 * Left
	 *
	 * @var double
	 */
	private $_left;
	
	/**
	 * Right
	 *
	 * @var double
	 */
	private $_right;
	
	/**
	 * Top
	 *
	 * @var double
	 */
	private $_top;
	
	/**
	 * Bottom
	 *
	 * @var double
	 */
	private $_bottom;
	
	/**
	 * Header
	 *
	 * @var double
	 */
	private $_header;
	
	/**
	 * Footer
	 *
	 * @var double
	 */
	private $_footer;
	
    /**
     * Create a new PHPExcel_Worksheet_PageMargins
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_left 	= 0.7;
    	$this->_right 	= 0.7;
    	$this->_top 	= 0.75;
    	$this->_bottom 	= 0.75;
    	$this->_header 	= 0.3;
    	$this->_footer 	= 0.3;
    }
    
    /**
     * Get Left
     *
     * @return double
     */
    public function getLeft() {
    	return $this->_left;
    }
    
    /**
     * Set Left
     *
     * @param double $pValue
     */
    public function setLeft($pValue) {
    	$this->_left = $pValue;
    }
    
    /**
     * Get Right
     *
     * @return double
     */
    public function getRight() {
    	return $this->_right;
    }
    
    /**
     * Set Right
     *
     * @param double $pValue
     */
    public function setRight($pValue) {
    	$this->_right = $pValue;
    }
    
    /**
     * Get Top
     *
     * @return double
     */
    public function getTop() {
    	return $this->_top;
    }
    
    /**
     * Set Top
     *
     * @param double $pValue
     */
    public function setTop($pValue) {
    	$this->_top = $pValue;
    }
    
    /**
     * Get Bottom
     *
     * @return double
     */
    public function getBottom() {
    	return $this->_bottom;
    }
    
    /**
     * Set Bottom
     *
     * @param double $pValue
     */
    public function setBottom($pValue) {
    	$this->_bottom = $pValue;
    }
    
    /**
     * Get Header
     *
     * @return double
     */
    public function getHeader() {
    	return $this->_header;
    }
    
    /**
     * Set Header
     *
     * @param double $pValue
     */
    public function setHeader($pValue) {
    	$this->_header = $pValue;
    }
    
    /**
     * Get Footer
     *
     * @return double
     */
    public function getFooter() {
    	return $this->_footer;
    }
    
    /**
     * Set Footer
     *
     * @param double $pValue
     */
    public function setFooter($pValue) {
    	$this->_footer = $pValue;
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
