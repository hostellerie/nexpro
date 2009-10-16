<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | Sept 16, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

function nexmenu_upgrade_25() {

    return 0;

}

function nexmenu_upgrade_252() {
    COM_errorLog("Success - Completed nexContent plugin version 2.5.2 update",1);

    return 0;
}

function nexmenu_upgrade_26() {
    global $_TABLES;

    $_SQL = array();

    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD `theme` VARCHAR( 64 ) NOT NULL FIRST;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE `mode` `header_style` VARCHAR(32) default NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD `block_style` VARCHAR( 32 ) NULL AFTER header_style;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE targetfeatures targetfeatures VARCHAR(255) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE blockmenu_style blockmenu_style VARCHAR(64) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE blocksubmenu_style blocksubmenu_style VARCHAR(64) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE headermenu_style headermenu_style VARCHAR(64) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE headersubmenu_style headersubmenu_style VARCHAR(64) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE headermenu_properties headermenu_properties VARCHAR(255) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE blockmenu_properties blockmenu_properties VARCHAR(255) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE headerbg headerbg VARCHAR(8) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE headerfg headerfg VARCHAR(8) NULL ;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE blockbg blockbg VARCHAR(8) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE blockfg blockfg VARCHAR(8) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE onhover_headerbg onhover_headerbg VARCHAR(8) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE onhover_headerfg onhover_headerfg VARCHAR(8) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE onhover_blockbg onhover_blockbg VARCHAR(8) NULL;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} CHANGE onhover_blockfg onhover_blockfg VARCHAR(8) NULL ;";

    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD headersubmenufg VARCHAR(8) NULL AFTER onhover_blockfg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD headersubmenubg VARCHAR(8) NULL AFTER headersubmenufg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD onhover_headersubmenufg VARCHAR(8) NULL AFTER headersubmenubg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD onhover_headersubmenubg VARCHAR(8) NULL AFTER onhover_headersubmenufg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD blocksubmenufg VARCHAR(8) NULL AFTER onhover_headersubmenubg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD blocksubmenubg VARCHAR(8) NULL AFTER blocksubmenufg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD onhover_blocksubmenufg VARCHAR(8) NULL AFTER blocksubmenubg;";
    $_SQL[] = "ALTER TABLE {$_TABLES['nexmenu_config']} ADD onhover_blocksubmenubg VARCHAR(8) NULL AFTER onhover_blocksubmenufg;";

    $_SQL[] = "UPDATE {$_TABLES['nexmenu_config']} SET block_style='CSS', theme='default', headersubmenufg='#F4F3F7',headersubmenubg='#237536',onhover_headersubmenufg='#FFFFFF',onhover_headersubmenubg='#237536',blocksubmenufg='#172BB0',blocksubmenubg='#BBC9EE',onhover_blocksubmenufg='#FCFCFC',onhover_blocksubmenubg='#145FF5';";



    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("NexMenu Plugin 3.1 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during NexMenu plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    COM_errorLog("Success - Completed NexMenu plugin version 3.1 update",1);
    return 0;

}



function nexmenu_upgrade_32() {
    global $_CONF,$_TABLES;
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $plugin_path = $_CONF['path'] . 'plugins/nexmenu/';
    require_once $plugin_path . 'install_defaults.php';
    if (file_exists($plugin_path . 'config.php')) {
        global $_DB_table_prefix, $CONF_NEXMENU;
        @include_once $plugin_path . 'config.php';
    }
    if (plugin_initconfig_nexmenu()) {
        return 0;
    } else {
        COM_errorLog('upgrade_32: There was an error upgrading the nexmenu plugin');
        return 1;
    }

}

?>