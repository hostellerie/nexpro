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
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version	1.5.5, 2007-12-24
 */


/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Style_NumberFormat */
require_once 'PHPExcel/Style/NumberFormat.php';


/**
 * PHPExcel_Shared_Date
 *
 * @category   PHPExcel
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Date
{
	/** constants */
	const CALENDAR_WINDOWS_1900 = 1900;
	const CALENDAR_MAC_1904 = 1904;


	private static $ExcelBaseDate	= self::CALENDAR_WINDOWS_1900;


	/**
	 * Set the Excel calendar
	 *
	 * @param	 integer	$baseDate			Excel base date
	 * @return	 boolean
	 */
	public static function setExcelCalendar($baseDate) {
		if (($baseDate == self::CALENDAR_WINDOWS_1900) || ($baseDate == self::CALENDAR_MAC_1904)) {
			self::$ExcelBaseDate = $baseDate;
			return True;
		}
		return False;
	}

	/**
	 * Return the Excel calendar
	 *
	 * @return	 integer	$baseDate			Excel base date
	 */
	public static function getExcelCalendar() {
		return self::$ExcelBaseDate;
	}

	/**
	 * Convert a date from Excel to PHP
	 *
	 * @param	 long	 $dateValue		Excel date/time value
	 * @return	 long	 				PHP date/time
	 */
	public static function ExcelToPHP($dateValue = 0) {
		if (self::$ExcelBaseDate == self::CALENDAR_WINDOWS_1900) {
			$myExcelBaseDate = 25569;
			//	Adjust for the spurious 29-Feb-1900 (Day 60)
			if ($dateValue < 60) {
				$myExcelBaseDate--;
			}
		} else {
			$myExcelBaseDate = 24107;
		}

		// Returnvalue
		$returnValue = 0;

		// Perform conversion
		if ($dateValue >= 1) {
			$utcDays = $dateValue - $myExcelBaseDate;
			$returnValue = round($utcDays * 24 * 60 * 60);
		} else {
			$hours = floor($dateValue * 24);
			$mins = floor($dateValue * 24 * 60) - $hours * 60;
			$secs = floor($dateValue * 24 * 60 * 60) - $hours * 60 * 60 - $mins * 60;
			$returnValue = mktime($hours, $mins, $secs);
		}

		// Return
		return $returnValue;
	}

	/**
	 * Convert a date from Excel to a PHP Date/Time object
	 *
	 * @param	 long	 $dateValue		Excel date/time value
	 * @return	 long	 				PHP date/time object
	 */
	public static function ExcelToPHPObject($dateValue = 0) {
		$dateTime = self::ExcelToPHP($dateValue);
		$days = floor($dateTime / 86400);
		$time = round((($dateTime / 86400) - $days) * 86400);
		$hours = floor($time / 3600);
		$minutes = floor($time / 60) - ($hours * 60);
		$seconds = floor($time) - ($hours * 3600) - ($minutes * 60);
		$dateObj = date_create('1-Jan-1970+'.$days.' days');
		$dateObj->setTime($hours,$minutes,$seconds);
		return $dateObj;
	}

	/**
	 * Convert a date from PHP to Excel
	 *
	 * @param	 long	 $dateValue 	PHP date/time or date object
	 * @return	 long					Excel date value
	 */
	public static function PHPToExcel($dateValue = 0) {
		static $dateTimeObjectType = 'DateTime';

		if ((is_object($dateValue)) && ($dateValue instanceof $dateTimeObjectType)) {
			return self::FormattedPHPToExcel( $dateValue->format('Y'),
											  $dateValue->format('m'),
											  $dateValue->format('d'),
											  $dateValue->format('H'),
											  $dateValue->format('i'),
											  $dateValue->format('s')
											);
		} elseif (is_numeric($dateValue)) {
			return self::FormattedPHPToExcel( date('Y',$dateValue),
											  date('m',$dateValue),
											  date('d',$dateValue),
											  date('H',$dateValue),
											  date('i',$dateValue),
											  date('s',$dateValue)
											);
		}
		return False;
	}

	/**
	 * FormattedPHPToExcel
	 *
	 * @param	long	$year
	 * @param	long	$month
	 * @param	long	$day
	 * @return  long				Excel date value
	 */
	public static function FormattedPHPToExcel($year, $month, $day, $hours=0, $minutes=0, $seconds=0) {
		if (self::$ExcelBaseDate == self::CALENDAR_WINDOWS_1900) {
			//
			//	Fudge factor for the erroneous fact that the year 1900 is treated as a Leap Year in MS Excel
			//	This affects every date following 28th February 1900
			//
			$excel1900isLeapYear = True;
			if (($year == 1900) && ($month <= 2)) { $excel1900isLeapYear = False; }
			$myExcelBaseDate = 2415020;
		} else {
			$myExcelBaseDate = 2416481;
			$excel1900isLeapYear = False;
		}

		//	Julian base date Adjustment
		if ($month > 2) {
			$month = $month - 3;
		} else {
			$month = $month + 9;
			$year--;
		}

		//	Calculate the Julian Date, then subtract the Excel base date (JD 2415020 = 31-Dec-1899 Giving Excel Date of 0)
		$century = substr($year,0,2);
		$decade = substr($year,2,2);
		$excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $myExcelBaseDate + $excel1900isLeapYear;

		$excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

		return $excelDate + $excelTime;
	}

	/**
	 * Is a given cell a date/time?
	 *
	 * @param	 PHPExcel_Cell	$pCell
	 * @return	 boolean
	 */
	public static function isDateTime(PHPExcel_Cell $pCell) {
		return self::isDateTimeFormat($pCell->getParent()->getStyle($pCell->getCoordinate())->getNumberFormat());
	}

	/**
	 * Is a given number format a date/time?
	 *
	 * @param	 PHPExcel_Style_NumberFormat	$pFormat
	 * @return	 boolean
	 */
	public static function isDateTimeFormat(PHPExcel_Style_NumberFormat $pFormat) {
		return self::isDateTimeFormatCode($pFormat->getFormatCode());
	}

	/**
	 * Is a given number format code a date/time?
	 *
	 * @param	 string	$pFormatCode
	 * @return	 boolean
	 */
	public static function isDateTimeFormatCode($pFormatCode = '') {
		// Switch on formatcode
		switch ($pFormatCode) {
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DMMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_MYMINUS:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME1:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME6:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME7:
			case PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:
				return true;
		}

		// Try checking all possible characters
		$possibleCharacters = array('y', 'm', 'd', 'H', 'i', 's');
		$matches = 0;
		for ($i = 0; $i < count($possibleCharacters); $i++) {
			if (eregi($possibleCharacters[$i], $pFormatCode)) {
				$matches++;
			}
		}
		if ($matches > 0) {
			return true;
		}

		// No date...
		return false;
	}
}
