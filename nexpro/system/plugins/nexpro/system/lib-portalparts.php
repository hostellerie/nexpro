<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | lib-portalparts.php                                                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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

/* PortalPart Navbar Function */

function ppNavbar ($menuitems, $selected='', $parms='') {
    global $_CONF;

    $navbar = new Template($_CONF['path_layout'] . 'navbar');
    $navbar->set_file (array (
        'navbar'       => 'navbar.thtml',
        'menuitem'     => 'menuitem.thtml',
        ));
    for ($i=1; $i <= count($menuitems); $i++)  {
        $parms = explode( "=",current($menuitems) );
        $navbar->set_var( 'link',   current($menuitems));
        if (key($menuitems) == $selected) {
            $navbar->set_var( 'cssactive', ' id="active"');
            $navbar->set_var( 'csscurrent',' id="current"');
        } else {
            $navbar->set_var( 'cssactive', '');
            $navbar->set_var( 'csscurrent','');
        }
        $navbar->set_var( 'label',  key($menuitems));
        $navbar->parse( 'menuitems', 'menuitem', true );
        next($menuitems);
    }
    $navbar->parse ('output', 'navbar');
    $retval = $navbar->finish($navbar->get_var('output'));
    return $retval;
}

// Callback Function for the array walk function below to apply the data filters to clean any posted data
function ppCleanField(&$field) {
    if (gettype($field) == "string" ) {
        $field = ppPrepareForDB($field);
    }
}

// Function to clean any posted data 
function ppCleanData($postarray) {
    array_walk($postarray,'ppCleanField');
    return $postarray;
}

function ppPrepareForDB($var,$checkhtml=true,$maxlength='') {
    if ($checkhtml) {
        // Need to call addslashes again as COM_checkHTML stips it out
        $var = COM_checkHTML($var);
        $var = addslashes($var);
    } else {
        $var = COM_checkWords(COM_killJS($var));
        if (!get_magic_quotes_gpc()) {
            $var = addslashes($var);
        }
    }
    if ($maxlength != '') {
        $var = substr($var, 0, $maxlength);
    }
    return $var;
}


function ppApplyFilter( $parameter, $isnumeric = false ,$returnzero=true) {

    $p = COM_stripslashes( $parameter );
    $p = strip_tags( $p );
    $p = COM_killJS( $p );

    if( $isnumeric ) {
        // Note: PHP's is_numeric() accepts values like 4e4 as numeric
        // Strip out any common number formatting characters
        $p = preg_replace('/[\s-\(\)]+/', '', $p );
        if( !is_numeric( $p ) || ( preg_match( '/^([0-9]+)$/', $p ) == 0 )) {
            if ($returnzero) {
                $p = 0;
            } else {
                $p = '';
            }
        }
    } else {
        $pa = explode( "'", $p );
        $pa = explode( '"', $pa['0'] );
        $pa = explode( '`', $pa['0'] );
        $p = $pa['0'];
    }

    return $p;
}

/* Filter variable if only Text is expected which may need to contain quote's or other special characters */
function ppFilterText( $parameter ) {
    // Need to call addslashes again as COM_checkHTML stips it out
    $var = COM_checkHTML($parameter);
    $var = COM_checkWords($var);
    $var = COM_killJS($var);
    $var = addslashes($var);
    return $var;
    
}

function ppGetData($vars,$setglobal=false,$type='',$option='')  {
    $return_data = array();
    if (!is_array($vars)) $vars = array($vars);

    #setup common reference to SuperGlobals depending which array is needed
    if ($type == 'GET' OR $type == 'POST') {
        if ($type == 'GET') { $SG_Array =& $_GET; }
        if ($type == 'POST') { $SG_Array =& $_POST; }

        # loop through SuperGlobal data array and grab out data for allowed fields if found
        foreach($vars as $key)  {
            if (array_key_exists($key,$SG_Array)) { $return_data[$key]=$SG_Array[$key]; }
        }

    } else {     
        foreach ($vars as $key) {
          if (array_key_exists($key, $_POST)) { 
            $return_data[$key] = $_POST[$key];
          } elseif (array_key_exists($key, $_GET)) { 
            $return_data[$key] = $_GET[$key];
          }
        }
    }

    # loop through $vars array and apply the filter
    foreach($vars as $value)  {
        if ($option == 'text') {
            // Check if this variable is an array - maybe a checkbox or multiple select
            if (is_array($return_data[$value])) {
                $subvalues_array = array();
                foreach ($return_data[$value] as $subvalue) {
                    $subvalues_array[] = ppFilterText($subvalue);
                }
                $return_data[$value] = $subvalues_array;
            } else {
                $return_data[$value] = ppFilterText($return_data[$value]);
            }
        } else {
            // Check if this variable is an array - maybe a checkbox or multiple select
            if (is_array($return_data[$value])) {
                $subvalues_array = array();
                foreach ($return_data[$value] as $subvalue) {
                    if ($option == 'int') {
                        $subvalues_array[] = ppApplyFilter($subvalue,true,true);
                    } else {
                        $subvalues_array[] = ppApplyFilter($subvalue);
                    }
                }
                $return_data[$value] = $subvalues_array;                
            } else {
                if ($option == 'int') {                  
                    $return_data[$value] = ppApplyFilter($return_data[$value],true);
                } else {
                    $return_data[$value] = ppApplyFilter($return_data[$value]);                    
                }
            }
        }
    }

    // Optionally set $GLOBALS or return the array
    if ($setglobal) {
      # loop through final data and define all the variables using the $GLOBALS array
      foreach ($return_data as $key=>$value)  {
        $GLOBALS[$key]=$value;
      }
    } else {
      return $return_data;
    }

}

/* Convert a text based date MM/DD/YYYY to a unix timestamp integer value */
/* Aug 23/2005: Changed format from YYYY-MM-DD to MM/DD/YYYY */
function ppConvertDate($date,$time='') {
    
        if (trim($date) == '') {
            return 0;
        }
        // Breakup the string using either a space, fwd slash, bkwd slash or colon as a delimiter
        $atok = strtok($date," /-\\:");
        while ($atok !== FALSE) {
            $atoks[] = $atok;
            $atok = strtok(" /-\\:");  // get the next token
        }
        if ($time == '') {
            $timestamp = mktime(0,0,0,$atoks[0],$atoks[1],$atoks[2]);
        } else {
            $btok = strtok($time," /-\\:");
            while ($btok !== FALSE) {
                $btoks[] = $btok;
                $btok = strtok(" /-\\:");
            }
            $timestamp = mktime($btoks[0],$btoks[1],$btoks[2],$atoks[0],$atoks[1],$atoks[2]);
        }
        return $timestamp;
}


function ppGetUserBlocks(&$blocks) {
    global $_TABLES, $_CONF, $_USER, $LANG21, $HTTP_SERVER_VARS, $topic, $page, $newstories;

    $retval = '';
    $sql = "SELECT name,owner_id,group_id,perm_group,perm_members,perm_anon FROM {$_TABLES['blocks']} WHERE onleft = 1 AND is_enabled = 1";

    // Get user preferences on blocks
    if( !isset( $_USER['noboxes'] ) || !isset( $_USER['boxes'] )) {
        if( !empty( $_USER['uid'] )) {
            $result = DB_query( "SELECT boxes,noboxes FROM {$_TABLES['userindex']} WHERE uid = '{$_USER['uid']}'" );
            list($_USER['boxes'], $_USER['noboxes']) = DB_fetchArray( $result );
        } else {
            $_USER['boxes'] = '';
            $_USER['noboxes'] = 0;
        }
    }
    $sql .= " AND (tid = 'all' AND type <> 'layout')";
    if( !empty( $_USER['boxes'] )) {
        $BOXES = str_replace( ' ', ',', $_USER['boxes'] );
        $sql .= " AND (bid NOT IN ($BOXES) OR bid = '-1')";
    }

    $sql .= ' ORDER BY blockorder,title asc';
    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 1; $i <= $nrows; $i++ ) {
        $A = DB_fetchArray( $result );
        if( SEC_hasAccess( $A['owner_id'], $A['group_id'], $A['perm_owner'], $A['perm_group'], $A['perm_members'], $A['perm_anon']) > 0 ) {
            $blocks[] = $A['name'];
        }
    }

    return $blocks;

}


/**
 * Delete a file, or a folder and its contents
 * Will delete recursively all files and folders
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.2
 * @param       string   $dirname    Directory to delete
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function ppRmdir($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }
 
    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recurse
        ppRmdir("$dirname/$entry");
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
}



function ppRandomFilename() {

    $length=10;
    srand((double)microtime()*1000000); 
    $possible_charactors = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
    $string = ""; 
    while(strlen($string)<$length) { 
        $string .= substr($possible_charactors, rand()%(strlen($possible_charactors)),1); 
    }
    $string .= date('mdHms');   // Now add the numerical MonthDayHourSecond just to ensure no possible duplicate
    return($string); 

}


function print_r_html($arr, $style = "display: none; margin-left: 10px;") { 
    static $i = 0; $i++;
    echo "\n<div id=\"array_tree_$i\" class=\"array_tree\">\n";
    foreach($arr as $key => $val) { 
        switch (gettype($val)) { 
        case "array":
            echo "<a onclick=\"document.getElementById('";
            echo "array_tree_element_$i').style.display = ";
            echo "document.getElementById('array_tree_element_$i";
            echo "').style.display == 'block' ?";
            echo "'none' : 'block';\"\n";
            echo "name=\"array_tree_link_$i\" href=\"#array_tree_link_$i\">".htmlspecialchars($key)."</a><br />\n";
            echo "<div class=\"array_tree_element_\" id=\"array_tree_element_$i\" style=\"$style\">";
            echo print_r_html($val);
            echo "</div>";
            break;
        case "integer":
            echo "<b>".htmlspecialchars($key)."</b> => <i>".htmlspecialchars($val)."</i><br />";
            break;
        case "double":
            echo "<b>".htmlspecialchars($key)."</b> => <i>".htmlspecialchars($val)."</i><br />";
            break;
        case "boolean":
            echo "<b>".htmlspecialchars($key)."</b> => ";
            if ($val) { 
                echo "true"; 
            } else { 
                echo "false"; 
            }
            echo  "<br />\n";
            break;
        case "string":
            echo "<b>".htmlspecialchars($key)."</b> => <code>".htmlspecialchars($val)."</code><br />";
            break;
        default:
            echo "<b>".htmlspecialchars($key)."</b> => ".gettype($val)."<br />";
            break;
    }
    echo "\n"; }
    echo "</div>\n"; 
}

/* Function needed to remove french accent characters which are generating a Javascript Error when being returned via AJAX */
function ppRemoveAccents($string) {
  return strtr($string,
   "���������������������������������������������������������������������",
   "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy"
   );
}

?>