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
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.5, 2007-12-24
 */


/** PHPExcel_Shared_PasswordHasher */
require_once 'PHPExcel/Shared/PasswordHasher.php';


/**
 * PHPExcel_DocumentSecurity
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_DocumentSecurity
{
	/**
	 * LockRevision
	 *
	 * @var boolean
	 */
	private $_lockRevision;
	
	/**
	 * LockStructure
	 *
	 * @var boolean
	 */
	private $_lockStructure;
	
	/**
	 * LockWindows
	 *
	 * @var boolean
	 */
	private $_lockWindows;
	
	/**
	 * RevisionsPassword
	 *
	 * @var string
	 */
	private $_revisionsPassword;
	
	/**
	 * WorkbookPassword
	 *
	 * @var string
	 */
	private $_workbookPassword;
	
    /**
     * Create a new PHPExcel_DocumentSecurity
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_lockRevision		= false;
    	$this->_lockStructure		= false;
    	$this->_lockWindows			= false;
    	$this->_revisionsPassword	= '';
    	$this->_workbookPassword	= '';
    }
    
    /**
     * Is some sort of dcument security enabled?
     *
     * @return boolean
     */
    function isSecurityEnabled() {
    	return 	$this->_lockRevision ||
		    	$this->_lockStructure ||
		    	$this->_lockWindows;
    }
    
    /**
     * Get LockRevision
     *
     * @return boolean
     */
    function getLockRevision() {
    	return $this->_lockRevision;
    }
    
    /**
     * Set LockRevision
     *
     * @param boolean $pValue
     */
    function setLockRevision($pValue = false) {
    	$this->_lockRevision = $pValue;
    }
    
    /**
     * Get LockStructure
     *
     * @return boolean
     */
    function getLockStructure() {
    	return $this->_lockStructure;
    }
    
    /**
     * Set LockStructure
     *
     * @param boolean $pValue
     */
    function setLockStructure($pValue = false) {
		$this->_lockStructure = $pValue;
    }
    
    /**
     * Get LockWindows
     *
     * @return boolean
     */
    function getLockWindows() {
    	return $this->_lockWindows;
    }
    
    /**
     * Set LockWindows
     *
     * @param boolean $pValue
     */
    function setLockWindows($pValue = false) {
    	$this->_lockWindows = $pValue;
    }
    
    /**
     * Get RevisionsPassword (hashed)
     *
     * @return string
     */
    function getRevisionsPassword() {
    	return $this->_revisionsPassword;
    }
    
    /**
     * Set RevisionsPassword
     *
     * @param string 	$pValue
     * @param boolean 	$pAlreadyHashed If the password has already been hashed, set this to true
     */
    function setRevisionsPassword($pValue = '', $pAlreadyHashed = false) {
    	if (!$pAlreadyHashed) {
    		$pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
    	}
    	$this->_revisionsPassword = $pValue;
    }
    
    /**
     * Get WorkbookPassword (hashed)
     *
     * @return string
     */
    function getWorkbookPassword() {
    	return $this->_workbookPassword;
    }

    /**
     * Set WorkbookPassword
     *
     * @param string 	$pValue
     * @param boolean 	$pAlreadyHashed If the password has already been hashed, set this to true
     */
    function setWorkbookPassword($pValue = '', $pAlreadyHashed = false) {
    	if (!$pAlreadyHashed) {
    		$pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
    	}
		$this->_workbookPassword = $pValue;
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
