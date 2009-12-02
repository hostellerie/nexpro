<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +-----------------------------------------------------------------------------+
// | Nexpro Project -  Portal Solutions by Nextide Inc.                          |
// | Date: June, 2007                                                            |
// +-----------------------------------------------------------------------------+
// | userreport.php     -- Example report using the nexpro reporting class       |
// +-----------------------------------------------------------------------------+
// | Copyright (C) 2005 by Nextide Inc.                                          |
// |                                                                             |
// | Author: Blaine Lang    -  blaine.lang@nextide.ca                            |
// +-----------------------------------------------------------------------------+
// |                                                                             |
// | This program is licensed under the terms of the GNU General Public License  |
// | as published by the Free Software Foundation; either version 2              |
// | of the License, or (at your option) any later version.                      |
// |                                                                             |
// | This program is OpenSource but not FREE. Unauthorized distribution is       |
// | illegal. You may not remove the copyright or redistribute this script       |
// | in any form.                                                                |
// |                                                                             |
// | This program is distributed in the hope that it will be useful,             |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        |
// | See the GNU General Public License for more details.                        |
// |                                                                             |
// | You should have received a copy of the GNU General Public License           |
// | along with this program; if not, write to the Free Software Foundation,     |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             |
// |                                                                             |
// +-----------------------------------------------------------------------------+
// 
  
require_once ('../lib-common.php');
require_once ($_CONF['path_system'] . 'nexpro/classes/nexreport/report.php');

if (SEC_inGroup('Root')) {

    /* Instantiate new report class - extending the base reporting class */
    $report = & report::factory('users');

    if (isset($_GET['mode']) AND $_GET['mode'] == 'export') {
        $report->set_pagesize(0);
        $report->set_mode('export');
    } else {
        $report->set_pagesize(30);
    }  


    // Generate UNIX Timestamp equivelent of the search date fields
    $searchdate1 = NXCOM_convertDate(COM_applyFilter($_REQUEST['searchdate1']));
    // Need to add 24hrs of time to end date so we include selected day
    if (!empty($_REQUEST['searchdate2'])) {
        $searchdate2 = NXCOM_convertDate(COM_applyFilter($_REQUEST['searchdate2'])) + (60 * 60 * 24);
    }

    $title = 'Sample reporting using the nexPro reporting class';
    $report->set_title($title);

    /* Create the SQL query that you want for report */
    $sql  = "SELECT a.uid, a.username, a.fullname as member, regdate, a.status, b.showonline FROM {$_TABLES['users']} a ";
    $sql .= "LEFT JOIN {$_TABLES['userprefs']} b on b.uid=a.uid ";

    /* Modify SQL query to use passed in Date Filter if used */
    if ($searchdate1 > 0) {
        $sql .= "WHERE UNIX_TIMESTAMP(regdate) >= $searchdate1 ";
        $report->set_filterparms("searchdate1={$_REQUEST['searchdate1']}");
        $report->set_title("Sample reporting using the nexPro reporting class where Registration Date ");
        if ($searchdate2 > 0) {
            $sql .= "AND UNIX_TIMESTAMP(regdate) <= $searchdate2 AND UNIX_TIMESTAMP(regdate) > 0 ";
            $report->set_filterparms("&searchdate2={$_REQUEST['searchdate2']}");
        }                        
    } elseif ($searchdate2 > 0) {
        $sql .= "WHERE UNIX_TIMESTAMP(regdate) <= $searchdate2 AND UNIX_TIMESTAMP(regdate) > 0 ";
        $report->set_title("Sample reporting using the nexPro reporting class where Registration Date ");
        $report->set_filterparms("searchdate2={$_REQUEST['searchdate2']}");
    }

    $sql .= "ORDER BY uid ASC";                 

    /* Add fields that will be used in report */
    $report->add_field('uid'); 
    $report->add_field('username'); 
    $report->add_field('member');
    $report->add_field('regdate');
    $report->add_field('status');       // user record result will be formatted in 'users' report class to show descriptive status
    $report->add_field('showonline');

    $report->set_reportdatefilter('on');  // Set to off to disable date filter in report.

    $report->set_sqlstmt($sql);
    $report->set_filterreport('on');
    $report->getReport();

    if (isset($_GET['mode']) AND $_GET['mode'] == 'export') {   
        echo $report->generate_report('excel');              // Create an Excel Export      
    } else {
        echo COM_siteHeader(); 
        echo $report->generate_report('display');            // Format final report page for display
        echo COM_siteFooter();
    }

} else {
	echo COM_refresh($_CONF['site_url']);
	exit;
}
    
?>