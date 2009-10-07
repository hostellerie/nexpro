<?php


class nexfileTagCloud  extends tagcloud {



    function __construct () {
        global $_DB_table_prefix;

        parent::__construct();
        $this->_type = 'nexfile';
    }

}

?>
