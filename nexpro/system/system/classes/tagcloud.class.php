<?php


class tagcloud {

    public $_tagwords;
    public $_tagitems;
    public $_tagmetrics;
    public $_filter;
    public $_newtags;
    public $_activetags;            // Active search tags - don't show in tag cloud
    public $_type;
    public $_fontmultiplier = 160;  // Used as a multiplier in displaycloud() function - Increase to see a wider range of font sizes
    public $_maxclouditems = 200;
    public $_allusers = 2;          // Group ID that includes all users (including anonymous)
    public $_sitemembers = 13;      // Group ID that includes only site members


    function __construct () {
        global $_DB_table_prefix,$_USER;

        $this->_filter = new sanitizer();
        $this->_tagwords = $_DB_table_prefix . 'tagwords';
        $this->_tagitems = $_DB_table_prefix . 'tagword_items';
        $this->_tagmetrics = $_DB_table_prefix . 'tagword_metrics';

        if (DB_count($this->_tagwords) < 30) $this->_fontmultiplier = 100;

        if ($_USER['uid'] > 1) {
            $this->_uid = $_USER['uid'];
        } else {
            $this->_uid = 1;
        }
    }


    /* Function added so I could isolate out the filtering logic.
     * It is using the new filtering class which some implementations may not use
     * All thats needed would be to update this function and not all the places filtering is done.
     * Returns filtered tagword in lowercase
   */
    private function filtertag($tag,$dbmode=false) {
        if($dbmode) {
            $this->_filter->initFilter();
            return strtolower($this->_filter->getDbData('text',$tag));
        } else {
            return strtolower($this->_filter->getCleanData('text',$tag));
        }
    }


    /* This function needs to defined in the plugin specific class to return the item perms
     * Refer to example commented out which is for stories
     * Values of 2 or 3 for perm_members or perm_anon indicate view and edit.
     * We ony are concerned about view access and view access to an item determines how we create tag access record
     * so that we only show tags with their relative metric depending on your access
     * If item is restricted to a group - then we need the assigned group id returned
     *
     * @param string $itemid  - id that identifies the plugin item, example sid for a story
     * @return array          - Return needs to be an associative array with the 3 permission related values
     *                          $A['group_id','perm_members','perm_anon');
    */
    function get_itemperms($itemid) {
        global $_TABLES;
        // $query = DB_query("SELECT group_id,perm_members,perm_anon FROM {$_TABLES['stories']} WHERE sid='$sid'");
        // $A = DB_fetchArray($query,false);
        // return $A;
        return false;
    }

    // Return an array of tagids for the passed in comma separated list of tagwords
    private function get_tagids($tagwords) {

        if (!empty($tagwords)) {
            $tagwords = explode(',',$tagwords);
            // Build a comma separated list of tagwords that we can use in a SQL statements below
            $allTagWords = array();
            foreach($tagwords as $word) {
                $tag = "'" . addslashes($word) . "'";
                $allTagWords[] = $tag;
            }
            $tagwords = implode(',',$allTagWords);  // build a comma separated list of words

            if (!empty($tagwords)) {
                $query = DB_query("SELECT id FROM {$this->_tagwords} where tagword in ($tagwords)");
                $tagids = array();
                while (list($id) = DB_fetchArray($query)) {
                    $tagids[] = $id;
                }
                return $tagids;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
    * @param string $itemid  - Item id, used to get the access permissions
    * @param array $tagids   - array of tag id's
    */
    private function add_accessmetrics($itemid,$tagids) {

        // Test that a valid array of tag id's is passed in
        if (is_array($tagids) AND count($tagids) > 0) {
            // Test that a valid item record exist
            if (DB_count($this->_tagitems,array('type','itemid'),array($this->_type,$itemid))) {
                // Get item permissions to determine what rights to use for tag metrics record
                $perms = $this->get_itemperms($itemid);

                // Add any new tags
                foreach($tagids as $id) {
                    if (!empty($id)) {
                        if ($perms['perm_anon']) {
                            DB_query("UPDATE {$this->_tagwords} SET metric=metric+1 WHERE id=$id");
                            if (DB_COUNT($this->_tagmetrics,array('tagid','type','grpid'), array($id,$this->_type,$this->_allusers))) {
                                $sql  = "UPDATE {$this->_tagmetrics} set metric=metric+1, last_updated=now() ";
                                $sql .= "WHERE tagid=$id AND type='{$this->_type}' and grpid={$this->_allusers}";
                             } else {
                                $sql  = "INSERT INTO {$this->_tagmetrics} (tagid,type,grpid,metric,last_updated) ";
                                $sql .= "VALUES ($id,'{$this->_type}',{$this->_allusers},1,NOW())";
                            }
                            DB_query($sql);
                        } elseif ($perms['perm_members']) {
                            DB_query("UPDATE {$this->_tagwords} SET metric=metric+1 WHERE id=$id");
                            if (DB_COUNT($this->_tagmetrics,array('tagid','type','grpid'), array($id,$this->_type,$this->_sitemembers))) {
                                $sql  = "UPDATE {$this->_tagmetrics} set metric=metric+1, last_updated=now() ";
                                $sql .= "WHERE tagid=$id AND type='{$this->_type}' and grpid={$this->_sitemembers}";
                             } else {
                                $sql  = "INSERT INTO {$this->_tagmetrics} (tagid,type,grpid,metric,last_updated) ";
                                $sql .= "VALUES ($id,'{$this->_type}',{$this->_sitemembers},1,NOW())";
                            }
                            DB_query($sql);

                        } elseif ($perms['group_id'] >= 2) {
                            DB_query("UPDATE {$this->_tagwords} SET metric=metric+1 WHERE id=$id");
                            if (DB_COUNT($this->_tagmetrics,array('tagid','type','grpid'), array($id,$this->_type,$perms['group_id']))) {
                                $sql  = "UPDATE {$this->_tagmetrics} set metric=metric+1, last_updated=now() ";
                                $sql .= "WHERE tagid=$id AND type='{$this->_type}' and grpid={$perms['group_id']}";
                             } else {
                                $sql  = "INSERT INTO {$this->_tagmetrics} (tagid,type,grpid,metric,last_updated) ";
                                $sql .= "VALUES ($id,'{$this->_type}',{$perms['group_id']},1,NOW())";
                            }
                            DB_query($sql);
                        }
                    }
                }
            }
        }
    }


    /*
    * @param string $itemid  - Item id, used to get the access permissions
    * @param array $tagids   - array of tag id's
    */
    private function update_accessmetrics($itemid,$tagids) {

        // Test that a valid array of tag id's is passed in
        if (is_array($tagids) AND count($tagids) > 0) {
            // Test that a valid item record exist
            if (DB_count($this->_tagitems,array('type','itemid'),array($this->_type,$itemid))) {
                // Get item permissions to determine what rights to use for tag metrics record
                $perms = $this->get_itemperms($itemid);

                 // Remove the unused tag related records for this item
                foreach($tagids as $id) {
                    if (!empty($id)) {
                        if ($perms['perm_anon']) {
                            // Delete the tag metric access record if metric = 1 else decrement the metric count
                            DB_query("DELETE FROM {$this->_tagmetrics} WHERE tagid=$id AND type='{$this->_type}' AND grpid={$this->_allusers} AND metric=1");
                            $sql  = "UPDATE {$this->_tagmetrics} set metric=metric-1, last_updated=now() ";
                            $sql .= "WHERE tagid=$id AND type='{$this->_type}' and grpid={$this->_allusers}";
                            DB_query($sql);
                        } elseif ($perms['perm_members']) {
                            DB_query("DELETE FROM {$this->_tagmetrics} WHERE tagid=$id AND type='{$this->_type}' AND grpid={$this->_sitemembers} AND metric=1");
                            $sql  = "UPDATE {$this->_tagmetrics} set metric=metric-1, last_updated=now() ";
                            $sql .= "WHERE tagid=$id AND type='{$this->_type}' and grpid={$this->_sitemembers}";
                            DB_query($sql);

                        } elseif ($perms['group_id'] >= 2) {
                            DB_query("DELETE FROM {$this->_tagmetrics} WHERE tagid=$id AND type='{$this->_type}' AND grpid={$perms['group_id']} AND metric=1");
                            $sql  = "UPDATE {$this->_tagmetrics} set metric=metric-1, last_updated=now() ";
                            $sql .= "WHERE tagid=$id AND type='{$this->_type}' and grpid={$perms['group_id']}";
                            DB_query($sql);

                        }
                        DB_query("DELETE FROM {$this->_tagwords} WHERE id=$id and metric=1");
                        DB_query("UPDATE {$this->_tagwords} SET metric=metric-1 WHERE id=$id");
                    }
                }
            }
        }
    }



    /* Update tag metrics for an existing item.
     * Should work for all plugins - adding tags or updating tags
     * @param string $itemid    - Example Story ID (sid) relates to itemid in the tagitems table
     * @param string $tagwords  - Single tagword or comma separated list of tagwords.
     *                            Tagwords can be unfilterd if passed in.
     *                            The set_newtags function will filter and prepare tags for DB insertion
    */
    public function update_tags($itemid,$tagwords='') {

        if (!empty($tagwords)) {
            $this->set_newtags($tagwords);
        }

        $perms = $this->get_itemperms($itemid);
        if($perms['perm_anon'] OR $perms['perm_members'] OR $perms['group_id'] >= 2) {
            if (!empty($this->_newtags)) {
                // If item record does not yet exist - create it.
                if (!DB_count($this->_tagitems,array('type','itemid'),array($this->_type,$itemid))) {
                    DB_query("INSERT INTO {$this->_tagitems} (itemid,type) VALUES ('{$itemid}','{$this->_type}')");
                }
                // Need to build list of tagid's for these tag words and if tagword does not yet exist then add it
                $tagwords = explode(',',$this->_newtags);
                $tags = array();
                foreach ($tagwords as $word) {
                    $word = addslashes(trim($word));
                    $id = DB_getItem($this->_tagwords,'id',"tagword='$word'");
                    if (empty($id)) {
                        DB_query("INSERT INTO {$this->_tagwords} (tagword,metric,last_updated) VALUES ('$word',0,NOW())");
                        $id = DB_insertID();
                    }
                    $tags[] = $id;
                }

                // Retrieve the current assigned tags to compare against new tags
                $currentTags = DB_getItem($this->_tagitems,'tags',"type='{$this->_type}' AND itemid='$itemid'");
                $currentTags = explode(',',$currentTags);

                $unusedTags = array_diff($currentTags,$tags);
                $newTags = array_diff($tags,$currentTags);

                $this->update_accessmetrics($itemid,$unusedTags);
                $this->add_accessmetrics($itemid,$newTags);

                $tagids = implode(',',$tags);
                if ($currentTags != $tags) {
                    DB_query("UPDATE {$this->_tagitems} SET tags = '{$tagids}' WHERE itemid = '$itemid'");
                }
                return true;

            } else {
                $this->clear_tags($itemid);
                return true;
            }
        } else {
            return false;
        }
    }


    /* Clear the tags used for this item and update tag access metrics
     * Typically called when item is deleted
     * @param string $itemid    - Example Story ID (sid) relates to itemid in the tagitems table
    */
    public function clear_tags($itemid) {
        // Retrieve the current assigned tags - these are the tags to update
        $currentTags = DB_getItem($this->_tagitems,'tags',"type='{$this->_type}' AND itemid='$itemid'");
        $currentTags = explode(',',$currentTags);
        $this->update_accessmetrics($itemid,$currentTags);
        DB_query("UPDATE {$this->_tagitems} SET tags = '' WHERE itemid = '$itemid'");
    }



    public function set_newtags($newtags) {
        $newtags = $this->filtertag($newtags);
        if (!empty($newtags)) {
            $this->_newtags = str_replace(array("\n",';'), ',', $newtags);
        }
    }

    public function get_newtags($dbmode=true) {
        if ($dbmode) {
            return $this->filtertag($this->_newtags,true);
        } else {
            return $this->_newtags;
        }
    }

    public function get_itemtags($itemid) {
        $tags = '';
        $tagids = DB_getItem($this->_tagitems,'tags',"type='{$this->_type}' AND itemid='$itemid'");
        if (!empty($tagids)) {
            $query = DB_query("SELECT tagword FROM {$this->_tagwords} WHERE id IN ($tagids)");
            if (DB_numRows($query) > 0) {
                while ($A = DB_fetchArray($query)) {
                    $tagwords[] = $A['tagword'];
                }
            }
            $tags = implode(',',$tagwords);
        }
        return $tags;
    }

    /* Search the defined tagwords across all plugins for any tag words matching query
     * Typically would be used in a AJAX driven lookup to populate a dropdown list dynamically as user enters tags
     *
     * @param string $query  - tag words to search on. Can be a list but only the last word will be used for search
     * @return array         - Returns an array of matching tag words
    */
    public function get_matchingtags($query) {
        $matches = array();;
        $query = strtolower($this->_filter->getCleanData('text',$query));
        // User may be looking for more then 1 tag - pull of the last word in the query to search against
        $tags = explode(',',$query);
        $lookup = array_pop($tags);
        $sql = "SELECT tagword FROM {$this->_tagwords} WHERE tagword REGEXP '^{$lookup}' ORDER BY metric DESC";
        $q = DB_query($sql);
        while (list($tag) = DB_fetchArray($q)) {
            $matches[] = $tag;
        }
        return $matches;
    }

    /* Return an array of item id's matching tag query */
    public function search($query) {

        $itemids = array();
        // Get a list of Tag ID's for the tag words in the query
        $sql = "SELECT id,tagword FROM {$this->_tagwords} ";
        $asearchtags = explode(',',stripslashes($query));
        if (count($asearchtags) > 1) {
            $sql .= "WHERE ";
            $i = 1;
            foreach ($asearchtags as $tag) {
                $tag = addslashes($tag);
                if ($i > 1) {
                    $sql .= "OR tagword = '$tag' ";
                } else {
                    $sql .= "tagword = '$tag' ";
                }
                $i++;
            }
        } else {
            $sql .= "WHERE tagword = '$query' ";
        }

        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            $tagids = array();
            $sql = "SELECT itemid FROM {$this->_tagitems} WHERE type='{$this->_type}' AND ";
            $i = 1;
            while ($A = DB_fetchArray($query)) {
                $tagids[] = $A['id'];
                if ($i > 1) {
                    $sql .= "AND tags REGEXP '{$A['id']}' ";
                } else {
                    $sql .= "tags REGEXP '{$A['id']}' ";
                }
                $i++;;
            }
            $this->_activetags = implode (',',$tagids);
            $query = DB_query($sql);
            if (DB_numRows($query) > 0) {
                while ($A = DB_fetchArray($query)) {
                    $itemids[] = $A['itemid'];
                }
                return $itemids;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    function displaycloud() {
        global $_TABLES,$_CONF,$_USER;

        if (isset($_USER) AND $_USER['uid'] > 1) {
            $_GROUPS = SEC_getUserGroups($_USER['uid']);
        } else {
            $_GROUPS = SEC_getUserGroups(1);
        }

        $grouplist = implode(',',$_GROUPS);

        $tpl = new Template($_CONF['path_layout'] . 'tagcloud');
        $tpl->set_file(array(
            'cloud'                 =>  'tagcloud.thtml',
            'tagcloud_rec'          =>  'tagcloud_record.thtml'
            ));

        // Retrieve the Maximum Metric

        $sql  = "SELECT metric from {$this->_tagmetrics} WHERE type='{$this->_type}' ";
        $sql .= "AND grpid IN ($grouplist) ";
        $sql .= "ORDER BY metric DESC limit 1";
        $qmaxm = DB_query($sql);
        list ($maxm) = DB_fetchArray($qmaxm);
        $sql = "SELECT a.tagid,b.tagword,a.metric from {$this->_tagmetrics} a ";
        $sql .= "LEFT JOIN {$this->_tagwords} b ON a.tagid=b.id ";
        $sql .= "WHERE type='{$this->_type}' AND grpid IN ($grouplist) ";
        if (!empty($this->_activetags)) {
            $sql .= "AND a.tagid NOT in ({$this->_activetags}) ";
        }
        $sql .= "GROUP BY tagid ORDER BY a.tagid ASC, metric DESC";
        $query = DB_query($sql);
        $numrecords = DB_numRows($query);

        while ($A = DB_fetchArray($query)) {
            // Using a Linear Interpolation equation to create a relative font size
            $ranking = $this->_fontmultiplier * ( 1.0 + ( 1.5 * $A['metric'] - ($maxm/2) )/ $maxm);
            $tpl->set_var('fontsize',$ranking);
            $tpl->set_var('search_url','#');
            $tpl->set_var('metric',$A['metric']);
            $tpl->set_var('tag',$A['tagword']);
            $tpl->parse('tag_words','tagcloud_rec',true);
        }
        $tpl->parse ('output', 'cloud');
        return $tpl->finish ($tpl->get_var('output'));
    }

}


class nexfileTagCloud  extends tagcloud {

    function __construct () {
        parent::__construct();
        $this->_type = 'nexfile';
    }

    function get_itemperms($fid) {
        global $_TABLES;

        $perms = array();
        $cid = DB_getItem($_TABLES['nxfile_files'],'cid',"fid=$fid");
        if ($cid > 0) {
            $query = DB_query("SELECT view from {$_TABLES['nxfile_access']} WHERE catid=$cid AND grp_id={$this->_allusers}");
            $perms['perm_anon'] = DB_fetchArray($query,false);
            $query = DB_query("SELECT view from {$_TABLES['nxfile_access']} WHERE catid=$cid AND grp_id={$this->_sitemembers}");
            $perms['perm_members'] = DB_fetchArray($query,false);

            $commongroups = array();
            $commongroups[] = $this->_allusers;
            $commongroups[] = $this->_sitemembers;
            $commongroups = implode(',',$commongroups);
            $query = DB_query("SELECT grp_id from {$_TABLES['nxfile_access']} WHERE catid=$cid AND view = 1 AND grp_id > 1 AND grp_id NOT IN ($commongroups)");
            $A = DB_fetchArray($query,false);
            $perms['group_id'] = $A['grp_id'];
        }

        return $perms;
    }

}

class storyTagCloud  extends tagcloud {

    function __construct () {
        parent::__construct();
        $this->_type = 'story';
    }


    function get_itemperms($sid) {
        global $_TABLES;

        $query = DB_query("SELECT perm_group,group_id,perm_members,perm_anon FROM {$_TABLES['stories']} WHERE sid='$sid'");
        return DB_fetchArray($query,false);
    }

    function displaycloud() {
        global $_TABLES,$_CONF,$_USER;

        if (isset($_USER) AND $_USER['uid'] > 1) {
            $_GROUPS = SEC_getUserGroups($_USER['uid']);
        } else {
            $_GROUPS = SEC_getUserGroups(1);
        }

        $grouplist = implode(',',$_GROUPS);

        $tpl = new Template($_CONF['path_layout'] . 'tagcloud');
        $tpl->set_file(array(
            'cloud'                 =>  'tagcloud.thtml',
            'tagcloud_rec'          =>  'tagcloud_record.thtml'
            ));

        // Retrieve the Maximum Metric
        $sql  = "SELECT metric from {$this->_tagmetrics} WHERE type='{$this->_type}' ";
        $sql .= "AND grpid IN ($grouplist) ";
        $sql .= "ORDER BY metric DESC limit 1";
        $qmaxm = DB_query($sql);
        list ($maxm) = DB_fetchArray($qmaxm);
        $sql = "SELECT a.tagid,b.tagword,sum(a.metric) as metric from {$this->_tagmetrics} a ";
        $sql .= "LEFT JOIN {$this->_tagwords} b ON a.tagid=b.id ";
        $sql .= "WHERE type='{$this->_type}' AND grpid IN ($grouplist) GROUP BY tagid";

        $query = DB_query($sql);
        $numrecords = DB_numRows($query);

        $i = 0;
        while ($A = DB_fetchArray($query)) {
            // Using a Linear Interpolation equation to create a relative font size
            $ranking = $this->_fontmultiplier * ( 1.0 + ( 1.5 * $A['metric'] - ($maxm/2) )/ $maxm);
            $tpl->set_var('fontsize',$ranking);
            $tpl->set_var('search_url',"{$_CONF['site_url']}/search.php?query={$A['tagword']}&type=stories&mode=search&results=150");
            $tpl->set_var('metric',$A['metric']);
            $tpl->set_var('tag',$A['tagword']);
            $tpl->parse('tag_words','tagcloud_rec',true);
            if ($i >= $this->_maxclouditems) break;
            $i++;
        }
        $tpl->parse ('output', 'cloud');
        return $tpl->finish ($tpl->get_var('output'));
    }



}

?>