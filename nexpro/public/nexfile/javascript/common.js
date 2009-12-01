/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Nexfile 3.0                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// | Blaine Lang - blaine DOT lang AT nextide DOT ca                           |
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


function updateAjaxStatus(message) {
    try {
        Dom.get('nexfile_ajaxStatus').innerHTML = '';
        if(message) {
            if (message == 'activity') {
                ajaxactive = true;
                Dom.setStyle('nexfile_ajaxActivity','visibility','visible');
            } else {
                Dom.get('nexfile_ajaxStatus').innerHTML = message;
                Dom.setStyle('nexfile_ajaxStatus','visibility','visible');
            }
        } else {
            ajaxactive = false;
            Dom.setStyle('nexfile_ajaxActivity','visibility','hidden');
            Dom.setStyle('nexfile_ajaxStatus','visibility','hidden');
        }
    } catch  (e) {}
}

function clearAjaxActivity() {
    clear_ajaxactivity = true;
    YAHOO.log('Clear ' + timerArray.length + ' timers');
    for(var i=0; i< timerArray.length; i++) {
        clearTimeout(timerArray[i]);
    }
    timerArray = new Array;
}


function hideNewFilePanel() {
    uploaderInit();
    YAHOO.container.newfiledialog.hide();
}

function hideFileDetailsPanel() {
    Dom.get('displayfiledetails').innerHTML = '';
    YAHOO.container.filedetails.hide();
    YAHOO.container.menuBar.cfg.setProperty("visible",false);
}

// Used to close the file details dialog once a download is started from clicking on the filename link or menu.
// Found that IE would only handle this if I used a delay.
function hideFileDetailsPanelDelay() {
    timer = setTimeout( function() {
                Dom.get('displayfiledetails').innerHTML = '';
                YAHOO.container.filedetails.hide();
                YAHOO.container.menuBar.cfg.setProperty("visible",false);
            }, 2000);
}

function showFolderMoveActions(e,obj) {
    Event.preventDefault(e);
    moveDivList = Dom.getElementsByClassName('foldermovelinks','',obj);
    if (moveDivList.length == 1) {
        Dom.setStyle(moveDivList[0],'visibility','visible');
    }
}

function hideFolderMoveActions(e,obj) {
    Event.preventDefault(e);
    moveDivList = Dom.getElementsByClassName('foldermovelinks','',obj);
    if (moveDivList.length == 1) {
        Dom.setStyle(moveDivList[0],'visibility','hidden');
    }
}

function setSearchButtonFocus () {
   Dom.get('searchbutton').focus();
}

function edit_activefolder() {
    Dom.setStyle('activefolder', 'display', 'none');
    Dom.setStyle('edit_activefolder', 'display', 'block');
}

function togglefolderoptions() {
    if (Dom.get('folder_options_container').style.display == 'none') {
        Dom.setStyle('folder_options_container','display','');
        Dom.get('folderoptions_link').title = 'Click to close';
    } else {
        Dom.setStyle('folder_options_container','display','none');
        Dom.get('folderoptions_link').title = 'Click to view folder description and notification options';
    }
}


function toggle_filedetails(e) {
    if (Dom.get('editfiledetailslink').innerHTML == 'Display') {
        YAHOO.log('toggle_filedetails - none ');
        Dom.setStyle('displayfiledetails', 'display', '');
        Dom.setStyle('editfiledetails', 'display', 'none');
        Dom.get('editfiledetailslink').innerHTML = 'Edit';
    } else {
        YAHOO.log('toggle_filedetails - block');
        Dom.setStyle('displayfiledetails', 'display', 'none');
        Dom.setStyle('editfiledetails', 'display', '');
        Dom.get('editfiledetailslink').innerHTML = 'Display';
    }
}



function expandfolder(id) {
    var el = 'subfolder_icon' + id ;
    var elc = 'subfolder' + id + '_contents';
    Dom.replaceClass(el, 'icon-folderclosed', 'icon-folderopen');
    Dom.setStyle(elc,'display','');

    // If this folder is closed but empty, and in hide details mode, then show placeholder message
    Dom.setStyle('emptyfolder_' + id ,'display','');
    Dom.setStyle('emptyfolder' + id + '_contents','display','');

    // Check if all records are now open
    recordList = Dom.getElementsByClassName('icon-folderclosed');
    if (recordList.length == 0) {
        Dom.get('expandcollapsefolderslink').href='?op=collapse';
        Dom.get('expandcollapsefolderslink').innerHTML='Collapse Folders';
    }

}

// Recursive function called to collapse subfolders
function collapseSubFolders(pid) {
    expandedfolders = arrayRemoveItem(expandedfolders, pid); // Remove this folder from the array we use to track expanded folders
    subfolderList = Dom.getElementsByClassName('parentfolder' + pid);
    for(var i=0; i< subfolderList.length; i++) {
        var elparts = subfolderList[i].id.split('subfolder');
        var el = 'subfolder_icon' + elparts[1] ;
        var elc = 'subfolder' + elparts[1] + '_contents';
        Dom.replaceClass(el, 'icon-folderopen', 'icon-folderclosed');
        Dom.setStyle(elc,'display','none');
        expandedfolders = arrayRemoveItem(expandedfolders, elparts[1]);
    }
}

function togglefolder(id) {
    var el = 'subfolder_icon' + id ;
    var elc = 'subfolder' + id + '_contents';
    if (Dom.hasClass(el, 'icon-folderclosed')) {
        expandedfolders.push(id);      // Add this expanded folder to the array we use to track expanded folders
        expandfolder(id);

    } else {
        Dom.replaceClass(el, 'icon-folderopen', 'icon-folderclosed');
        Dom.setStyle(elc,'display','none');

        collapseSubFolders(id);

        // Check if all records are now closed
        recordList = Dom.getElementsByClassName('icon-folderopen');
        if (recordList.length == 0) {
            Dom.get('expandcollapsefolderslink').href='?op=expand';
            Dom.get('expandcollapsefolderslink').innerHTML='Expand Folders';
        }
    }

    YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');

}

function expandCollapseFolders(obj,mode) {

    if (!mode || mode == '') {
        var params = obj.getAttribute('href');
        var mode = parseURL(params,'op');
    }

    if (mode == 'expand') {
        YAHOO.util.Cookie.set("nexfilefolders", "expanded");
        nexfilefolders = 'expanded';
        var elements = Dom.getElementsByClassName('icon-folderclosed','span', 'filelisting_container');
        for (var i = 0; i < elements.length; i++) {
            var folder = elements[i].id.split('subfolder_icon');
            var id = folder[1];
            var el = elements[i];
            var elc = 'subfolder' + folder[1] + '_contents';
            Dom.replaceClass(el, 'icon-folderclosed', 'icon-folderopen');
            Dom.setStyle(elc,'display','');

            // If this folder is closed but empty, and in hide details mode, then show placeholder message
            Dom.setStyle('emptyfolder_' + id ,'display','');
            Dom.setStyle('emptyfolder' + id + '_contents','display','');
        }
        obj.href='?op=collapse';
        obj.innerHTML='Collapse Folders';
    } else if (mode == 'collapse') {
        YAHOO.util.Cookie.set("nexfilefolders", "collapsed");
        nexfilefolders = 'collapsed';
        var elements = Dom.getElementsByClassName('icon-folderopen','span', 'filelisting_container');
        for (var i = 0; i < elements.length; i++) {
            var folder = elements[i].id.split('subfolder_icon');
            var id = folder[1];
            var el = elements[i];
            var elc = 'subfolder' + folder[1] + '_contents';
            Dom.replaceClass(el, 'icon-folderopen', 'icon-folderclosed');
            Dom.setStyle(elc,'display','none');
            expandedfolders = arrayRemoveItem(expandedfolders,id); // Remove this folder from the array we use to track expanded folders
        }
        obj.href='?op=expand';
        obj.innerHTML='Expand Folders';
    }
    YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');

}

/* If mode (show/hide) is empty, then function will toggle the filedetails */
function showhideFileDetail(mode) {
    var elements = Dom.getElementsByClassName('filedesc','div', 'filelisting_container');
    if (mode != 'hide' && mode != 'show') {
        if (nexfiledetail == 'collapsed') {
            mode = 'show';
        } else {
            mode = 'hide';
        }
    }
    for (var i = 0; i < elements.length; i++) {
        if (mode == 'show') {
            var listingDescChildren = Dom.getElementsByClassName('filedesc_span','span',elements[i]);
            // Should just return the one span tag and we can test if it contains anything to display
            try {
                if (listingDescChildren[0].innerHTML != '') {
                    Dom.setStyle(elements[i],'display','');
                }
            } catch (e) {}
        } else if (!Dom.hasClass(elements[i].parentNode,'emptyfolder')) {
            // Check if this node is folder that has no items - don't display the filedesc div
            Dom.setStyle(elements[i],'display','none');
        }
    }

    if (elements.length > 0) {
        if (mode == 'show') {
            YAHOO.util.Cookie.set("nexfiledetail", "expanded");
            nexfiledetail = 'expanded';
            var elm = Dom.getFirstChild('showhidedetail');
            elm.innerHTML='Hide File Details';
        } else {
            YAHOO.util.Cookie.set("nexfiledetail", "collapsed");
            nexfiledetail = 'collapsed';
            var elm = Dom.getFirstChild('showhidedetail');
            elm.innerHTML='Show File Details';
        }
    }

    var elements = Dom.getElementsByClassName('emptyfolder','div', 'filelisting_container');
    for (var i = 0; i < elements.length; i++) {
        if (Dom.getStyle(elements[i],'display') == 'none') {
            Dom.setStyle(elements[i],'display','');
        } else {
            // If this folder is closed but empty, and in hide details mode, then show placeholder message
            var x = elements[i].id.split('_');
            if (x[1] > 0) {
                if (Dom.hasClass('subfolder'+x[1],'icon-folderopen')) {
                    Dom.setStyle('emptyfolder' + x[1] + '_contents','display','');
                }
            }
        }
    }

}


function delete_activefolder(frm) {
    if (confirm('Are you sure you want to delete this folder and all it\'s files')) {
        frm.op.value ='deletefolder';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    Dom.get('activefolder_container').innerHTML = oResults.activefolder;
                    renderLeftNavigation(oResults);
                    renderFileListing(oResults);
                } else {
                    alert('Error deleting folder');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Update Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        YAHOO.util.Connect.setForm(frm);
        YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url, callback);

    } else {
        Dom.setStyle('edit_activefolder', 'display', 'none');
        Dom.setStyle('activefolder', 'display', 'block');
        return false;

    }
}

function deletefile() {
    var reportmode = document.frmtoolbar.reportmode.value;
    if (reportmode == 'approvals') {
        if (confirm('Delete this file submission?')) {
            var fid = document.frmFileDetails.id.value;
            makeAJAXDeleteFile(fid);
        } else {
            return false;
        }
    } else {
        if (confirm('Delete this file and associated versions?')) {
            var fid = document.frmFileDetails.id.value;
            makeAJAXDeleteFile(fid);
        } else {
            return false;
        }
    }
}


function broadcastnotification() {
    YAHOO.container.broadcastDialog.cfg.setProperty("visible",true);
}


function checkMultiAction(selectoption) {
    if (selectoption == 'delete') {
        if (confirm("Are you sure you want to delete these selected files?")) {
        var surl = ajax_post_handler_url + '?op=deletecheckedfiles';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    renderFileListing(oResults);
                    Dom.get('headerchkall').checked = false;
                    Dom.get('multiaction').selectedIndex=0;
                    Dom.get('multiaction').disabled = true;
                    try {
                        if (oResults.lastrenderedfiles) {
                            YAHOO.nexfile.getmorefiledata(oResults.lastrenderedfiles);
                        } else {
                            YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');
                        }
                    } catch(e) {YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');}

                } else {
                    alert('Error processing request');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('activity');
        var formObject = document.frmtoolbar;
        YAHOO.util.Connect.setForm(formObject);
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

        } else {
            // Resetting the select element so user can easily re-select same option twice else onChange will not fire
            timer = setTimeout("Dom.get('multiaction').selectedIndex=0", 3000);
            return false;
        }

    } else if (selectoption == 'move') {
        var surl = ajax_post_handler_url + '?op=rendermoveform';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                Dom.get('movebatchfilesdialog_form').innerHTML = oResults.displayhtml;
                YAHOO.container.batchfilemovedialog.cfg.setProperty("visible",true);
                if (!Event.getListeners('btnMoveFolderSubmit')) {   // Check first to see if listener already active
                    Event.addListener("btnMoveFolderSubmit", "click", moveSelectedFiles);
                }
                if (!Event.getListeners('btnMoveFolderCancel')) {   // Check first to see if listener already active
                    Event.addListener("btnMoveFolderCancel", "click",YAHOO.container.batchfilemovedialog.hide, YAHOO.container.batchfilemovedialog, true);
                }
                // Resetting the select element so user can easily re-select same option twice else onChange will not fire
                timer = setTimeout("Dom.get('multiaction').selectedIndex=0", 3000);
            },
            failure: function(o) {
                YAHOO.log('AJAX error loading move folder options : ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

    } else if (selectoption == 'markfavorite') {
        var surl = ajax_post_handler_url + '?op=markfavorite';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    renderFileListing(oResults);
                    Dom.get('headerchkall').checked = false;
                    Dom.get('multiaction').selectedIndex=0;
                    Dom.get('multiaction').disabled = true;
                } else {
                    alert('Error processing request');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('activity');
        var formObject = document.frmtoolbar;
        YAHOO.util.Connect.setForm(formObject);
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

    } else if (selectoption == 'clearfavorite') {
        var surl = ajax_post_handler_url + '?op=clearfavorite';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    renderFileListing(oResults);
                    Dom.get('headerchkall').checked = false;
                    Dom.get('multiaction').selectedIndex=0;
                    Dom.get('multiaction').disabled = true;
                } else {
                    alert('Error processing request');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('activity');
        var formObject = document.frmtoolbar;
        YAHOO.util.Connect.setForm(formObject);
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

    } else if (selectoption == 'approvesubmissions') {
        var surl = ajax_post_handler_url + '?op=approvesubmissions';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    renderFileListing(oResults);
                    renderLeftNavigation(oResults);
                    Dom.get('headerchkall').checked = false;
                    Dom.get('multiaction').selectedIndex=0;
                    Dom.get('multiaction').disabled = true;
                } else {
                    alert('Error processing request');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('activity');
        var formObject = document.frmtoolbar;
        YAHOO.util.Connect.setForm(formObject);
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

    } else if (selectoption == 'deletesubmissions') {
        var surl = ajax_post_handler_url + '?op=deletesubmissions';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    renderFileListing(oResults);
                    renderLeftNavigation(oResults);
                    Dom.get('headerchkall').checked = false;
                    Dom.get('multiaction').selectedIndex=0;
                    Dom.get('multiaction').disabled = true;
                } else {
                    alert('Error processing request');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('activity');
        var formObject = document.frmtoolbar;
        YAHOO.util.Connect.setForm(formObject);
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

    } else if (selectoption == 'subscribe') {
        var surl = ajax_post_handler_url + '?op=multisubscribe';
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.retcode == 200) {
                    renderFileListing(oResults);
                    renderLeftNavigation(oResults);
                    Dom.get('headerchkall').checked = false;
                    document.frmtoolbar.multiaction.selectedIndex=0;
                    document.frmtoolbar.multiaction.disabled = true;
                } else {
                    alert('Error procssing request');
                }
                updateAjaxStatus();
            },
            failure: function(o) {
                YAHOO.log('AJAX Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('activity');
        var formObject = document.frmtoolbar;
        YAHOO.util.Connect.setForm(formObject);
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        return false;

    } else {
        return true;
    }
}

function postSubmitMultiactionResetIfNeed(selectoption) {
    if (selectoption == 'archive')
        timer = setTimeout('document.frmtoolbar.multiaction.selectedIndex=0', 3000);
}

function moveSelectedFiles() {
    var newcid = document.frmBatchMove.movebatchfiles.value;
    document.frmtoolbar.newcid.value = newcid;
    // Since I am resetting the selectbox in checkMultiAction(), I need to now set it to the 'move' option
    Dom.get('multiaction').selectedIndex=2;
    YAHOO.container.batchfilemovedialog.hide();
    var surl = ajax_post_handler_url + '?op=movecheckedfiles';
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                if (oResults.cid > 0) {
                    makeAJAXGetFolderListing(oResults.cid);
                } else {
                    renderFileListing(oResults);
                }
                if (oResults.message != '') {
                    Dom.get('nexfile_alert_content').innerHTML = oResults.message;
                    Dom.setStyle('nexfile_alert','display','');
                }
                Dom.get('headerchkall').checked = false;
                Dom.get('multiaction').selectedIndex=0;
                Dom.get('multiaction').disabled = true;
            } else {
                alert('Error processing request');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('activity');
    var formObject = document.frmtoolbar;
    YAHOO.util.Connect.setForm(formObject);
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
}

function makeAJAXCreateFolder() {
    var surl = ajax_post_handler_url + '?op=createfolder';
    var formObject = document.getElementById('frmNewFolder');

    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                makeAJAXGetFolderListing(oResults.displaycid);
            } else {
                alert(oResults.errmsg);
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }

    YAHOO.util.Connect.setForm(formObject, false);
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
};




var makeAJAXLoadFileDetails = function(id) {
    var reportmode = document.frmtoolbar.reportmode.value;
    YAHOO.container.filedetails.focusFirst();
    Dom.get('displayfiledetails').innerHTML = '';
    YAHOO.container.menuBar.cfg.setProperty("visible",true);
    YAHOO.container.filedetails.cfg.setProperty("visible",true);
    YAHOO.container.filedetails.cfg.setProperty("fixedcenter",false);
    document.frmFileDetails.description.value = '';
    document.frmFileDetails.version_note.value = '';
    document.frmFileDetails.editfile_tags.value = '';
    Dom.setStyle('displayfiledetails', 'display', 'block');
    Dom.setStyle('editfiledetails', 'display', 'none');
    try {
        Dom.get('editfiledetailslink').innerHTML = 'Edit';
    } catch (e) {}

    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.error.length == 0) {
                Dom.get('displayfiledetails').innerHTML = oResults.displayhtml;
                try {
                    Dom.get('menubar_downloadlink').href = actionurl_dir + '/download.php?op=download&fid=' + oResults.fid;
                    Event.addListener("menubar_downloadlink", "click", hideFileDetailsPanelDelay);

                } catch (e) {}
                if (!oResults.editperm) {
                    YAHOO.container.menuBar.getItem(1).cfg.setProperty("disabled", true);
                    YAHOO.util.Event.removeListener("editfiledetailslink", "click");
                } else {
                    YAHOO.container.menuBar.getItem(1).cfg.setProperty("disabled", false);
                    if (!Event.getListeners('editfiledetailslink')) {   // Check first to see if listener already active
                        Event.addListener("editfiledetailslink", "click", toggle_filedetails);
                    }
                }
                if (!oResults.addperm) {
                    YAHOO.container.menuBar.getItem(2).cfg.setProperty("disabled", true);
                    YAHOO.util.Event.removeListener("newversionlink", "click");
                } else {
                    YAHOO.container.menuBar.getItem(2).cfg.setProperty("disabled", false);
                    if (!Event.getListeners('newversionlink')) {   // Check first to see if listener already active
                        Event.addListener("newversionlink", "click", showAddNewVersion, YAHOO.container.newfiledialog, true);
                    }

                }
                if (!oResults.deleteperm) {
                    YAHOO.container.menuBar.getItem(4).cfg.setProperty("disabled", true);
                    YAHOO.util.Event.removeListener("deletefiledetailslink", "click");
                } else {
                    YAHOO.container.menuBar.getItem(4).cfg.setProperty("disabled", false);
                    if (!Event.getListeners('deletefiledetailslink')) {   // Check first to see if listener already active
                        Event.addListener("deletefiledetailslink", "click", deletefile);
                    }
                }
                if (!oResults.lockperm) {
                    if (oResults.locked) {
                        YAHOO.container.menuBar.getItem(0).cfg.setProperty("disabled", true);
                    } else {
                        YAHOO.container.menuBar.getItem(0).cfg.setProperty("disabled", false);
                    }
                    YAHOO.container.menuBar.getItem(5).cfg.setProperty("disabled", true);
                    YAHOO.util.Event.removeListener("lockfiledetailslink", "click");
                } else {
                    YAHOO.container.menuBar.getItem(0).cfg.setProperty("disabled", false);
                    YAHOO.container.menuBar.getItem(5).cfg.setProperty("disabled", false);
                    if (!Event.getListeners('lockfiledetailslink')) {   // Check first to see if listener already active
                        Event.addListener("lockfiledetailslink", "click", adminToggleFilelock);
                    }
                }
                if (!oResults.notifyperm) {
                    YAHOO.container.menuBar.getItem(6).cfg.setProperty("disabled", true);
                    Event.removeListener("notifyfiledetailslink", "click");
                } else {
                    YAHOO.container.menuBar.getItem(6).cfg.setProperty("disabled", false);
                    if (!Event.getListeners('notifyfiledetailslink')) {   // Check first to see if listener already active
                        Event.addListener("notifyfiledetailslink", "click", adminToggleNotification);
                    }
                }
                if (!oResults.broadcastperm) {
                    YAHOO.container.menuBar.getItem(7).cfg.setProperty("disabled", true);
                    Event.removeListener("broadcastnotificationlink", "click");
                } else {
                    YAHOO.container.menuBar.getItem(7).cfg.setProperty("disabled", false);
                    if (!Event.getListeners('broadcastnotificationlink')) {   // Check first to see if listener already active
                        Event.addListener("broadcastnotificationlink", "click", broadcastnotification);
                    }
                }

                if (oResults.status == 0) {       // Un-Approved File
                    Dom.setStyle('newversionlink', 'display', 'none');
                    Dom.setStyle('lockmenubaritem', 'display', 'none');
                    Dom.setStyle('notifymenubaritem', 'display', 'none');
                    Dom.setStyle('approvefiledetailslink', 'display', '');
                    document.frmFileDetails.approved.value = false;
                    Event.addListener("approvefiledetailslink", "click", adminApproveSubmission);
                } else {
                    Dom.setStyle('newversionlink', 'display', '');
                    Dom.setStyle('lockmenubaritem', 'display', '');
                    Dom.setStyle('notifymenubaritem', 'display', '');
                    Dom.setStyle('approvefiledetailslink', 'display', 'none');
                    document.frmFileDetails.approved.value = true;
                }
                document.frmBroadcast.fid.value = oResults.fid;
                document.frmBroadcast.message.value = '';
                document.frmFileDetails.id.value = oResults.fid;
                document.frmFileDetails.version.value = oResults.version;
                document.frmFileDetails.filetitle.value = oResults.title;
                Dom.get('filedetails_titlebar').innerHTML = oResults.title + '&nbsp;-&nbsp;Details';
                Dom.get('disp_owner').innerHTML = oResults.username;
                Dom.get('disp_date').innerHTML = oResults.date;
                Dom.get('disp_size').innerHTML = oResults.size;
                document.frmFileDetails.description.value = oResults.description.replace('<br />','','g');
                document.frmFileDetails.version_note.value = oResults.version_note.replace('<br />','','g');
                document.frmFileDetails.editfile_tags.value = oResults.tags.replace('<br />','','g');
                Dom.get('folderoptions').innerHTML = oResults.folderoptions;
                if (oResults.tagperms) {
                    Dom.setStyle('tagsfield','display','block');
                    Dom.setStyle('tagswarning','display','none');
                } else {
                    Dom.setStyle('tagsfield','display','none');
                    Dom.setStyle('tagswarning','display','block');
                }

                if (oResults.locked) {
                    try {
                        Dom.get('lockfiledetailslink').innerHTML = 'UnLock';
                    } catch(e) {}
                } else {
                    try {
                        Dom.get('lockfiledetailslink').innerHTML = 'Lock';
                    } catch (e) {}
                }
                if (oResults.subscribed) {
                    try {
                        Dom.get('notifyfiledetailslink').innerHTML = 'UnSubscribe';
                    } catch (e) {}
                } else {
                    try {
                        Dom.get('notifyfiledetailslink').innerHTML = 'Subscribe';
                    } catch (e) {}
                }

            } else {
                alert(oResults.error);
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000

    }
    updateAjaxStatus('Loading Data ...');
    var qs = 'op=loadfiledetails&id=' + id + '&reportmode=' + reportmode;
    YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url, callback, qs);
}


function makeAJAXUpdateFileDetails(formObject,fid) {
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.fid > 0) {
                Dom.get('listingDescriptionRec' + oResults.fid ).innerHTML = oResults.description;
                if (oResults.description != '') {
                    Dom.setStyle('filedesc_container_' + oResults.fid ,'display','block');
                } else {
                    Dom.setStyle('filedesc_container_' + oResults.fid ,'display','none');
                }
                if (document.frmFileDetails.approved.value == true) {
                    Dom.get('listingTagsRec' + oResults.fid ).innerHTML = oResults.tags;
                    jQuery('.listing_searchtag').corner("8px");
                }
                Dom.get('listingFilenameRec' + oResults.fid ).innerHTML = oResults.filename;
                if (oResults.filemoved && oResults.cid > 0) {
                    document.location = actionurl_dir + '/index.php?cid=' + oResults.cid;
                } else {
                    Dom.get('tagcloud_words').innerHTML = oResults.tagcloud;
                }
                if (oResults.tagerror != '') {
                    Dom.get('filedetails_statusmsg').innerHTML = oResults.tagerror;
                    Dom.setStyle('filedetails_statusmsg', 'display', 'block');
                    timer = setTimeout('Dom.setStyle("filedetails_statusmsg", "display", "none")', 3000);
                } else {
                    Dom.get('listingTagsRec'+oResults.fid).innerHTML = oResults.tags;
                    YAHOO.container.menuBar.cfg.setProperty("visible",false)
                    hideFileDetailsPanel();
                }
            } else {
                YAHOO.container.menuBar.cfg.setProperty("visible",false)
                hideFileDetailsPanel();
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating File ...');
    YAHOO.util.Connect.setForm(formObject, false);
    YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url, callback);
};


// Show Folder Perms - Called to retrieve and show the folder permissions panel for the selected folder
function makeAJAXShowFolderPerms(formObject) {
    var cid=formObject.cid.value;
    var surl = ajax_post_handler_url + '?op=getfolderperms&cid=' + cid;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                Dom.get('folderperms_content').innerHTML = oResults.html;
                YAHOO.container.folderperms.cfg.setProperty("visible",true);
                Event.addListener("filedetails_cancel", "click", hideFileDetailsPanel);
                Event.addListener("folderperms_cancel", "click", YAHOO.container.folderperms.hide, YAHOO.container.folderperms, true);
            } else {
                alert('Error retrieving folder permissions');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Loading Data ...');
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
};


function makeAJAXUpdateFolderPerms(formObject) {
    YAHOO.util.Connect.setForm(formObject, false);
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                Dom.get('folderperms_content').innerHTML = oResults.html;
                YAHOO.container.folderperms.cfg.setProperty("visible",true);
                Event.addListener("filedetails_cancel", "click", hideFileDetailsPanel);
                Event.addListener("folderperms_cancel", "click", YAHOO.container.folderperms.hide, YAHOO.container.folderperms, true);
            } else {
                alert('Error retrieving folder permissions');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating Permissions ...');
    YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url, callback);
};


function makeAJAXUpdateFolderDetails(formObject) {
    if (!blockui)  {
        blockui=true;
        $.blockUI();
    }
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                makeAJAXGetFolderListing(oResults.cid);
            } else {
                YAHOO.container.menuBar.cfg.setProperty("visible",false)
                hideFileDetailsPanel();
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating Folder ...');
    YAHOO.util.Connect.setForm(formObject, false);
    YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url, callback);
};


// Called from the confirm message displayed by deleteFileHandleSuccess() when user clicks on link in message
function deleteFileSuccessConfirmAction() {
    var cid = document.frmFileDetails.cid.value;
    hideFileDetailsPanel();
}


function makeAJAXDeleteFile(fid) {
    var listingcid = document.frmtoolbar.cid.value;
    var reportmode = document.frmtoolbar.reportmode.value;
    var surl = ajax_post_handler_url + '?op=deletefile&fid=' + fid + '&listingcid=' + listingcid + '&reportmode=' + reportmode;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                Dom.get('folder_' + oResults.cid + '_rec_' + oResults.fid).innerHTML = oResults.filemsg;
                YAHOO.container.menuBar.cfg.setProperty("visible",false);
                Dom.get('filedetails_titlebar').innerHTML = oResults.title;
                Dom.get('displayfiledetails').innerHTML = oResults.message;
                timer = setTimeout('deleteFileSuccessConfirmAction()', 3000);
                renderFileListing(oResults);
                try {
                    if (oResults.lastrenderedfiles) {
                        //YAHOO.log('showfiles: initiate getmorefiledata:' + timeDiff.getDiff() + 'ms');
                        YAHOO.nexfile.getmorefiledata(oResults.lastrenderedfiles);
                        //YAHOO.log('showfiles: completed getmorefiledata:' + timeDiff.getDiff() + 'ms');
                    } else {
                        YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');
                    }
                } catch(e) {YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');}
            } else {
                alert('Error deleting file');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000

    }
    updateAjaxStatus('Deleting File ...');
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
};


function adminToggleFilelock() {
    if (!ajaxactive) {
        ajaxactive = true;
        var fid = document.frmFileDetails.id.value;
        var surl = ajax_post_handler_url + '?op=togglelock&fid=' + fid;
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.error == '') {
                    Dom.get('filedetails_statusmsg').innerHTML = oResults.message;
                    Dom.setStyle('filedetails_statusmsg', 'display', 'block');
                    if (oResults.locked) {
                        Dom.get('lockfiledetailslink').innerHTML = 'UnLock';
                        Dom.get('lockedalertmsg').innerHTML = oResults.locked_message;
                        Dom.setStyle("lockedalertmsg", "display", "block");
                        Dom.setStyle('listingLockIconRec' + oResults.fid, "display", '');
                    } else {
                        Dom.get('lockfiledetailslink').innerHTML = 'Lock';
                        Dom.get('lockedalertmsg').innerHTML = '';
                        Dom.setStyle("lockedalertmsg", "display", "none");
                        Dom.setStyle('listingLockIconRec' + oResults.fid, "display", "none");
                    }
                    timer = setTimeout('Dom.setStyle("filedetails_statusmsg", "display", "none")', 3000);
                } else {
                    alert('Error locking file');
                }
                setTimeout('updateAjaxStatus()',500)
            },
            failure: function(o) {
                YAHOO.log('AJAX Update Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('Updating File Lock ...');
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
    }
};

function adminApproveSubmission () {
    var id = document.frmFileDetails.id.value;
    var surl = ajax_post_handler_url + '?op=approvefile&id=' + id;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                renderLeftNavigation(oResults);
                renderFileListing(oResults);
                YAHOO.container.menuBar.cfg.setProperty("visible",false)
                hideFileDetailsPanel();
            } else {
                alert('Error approving file');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000

    }
    updateAjaxStatus('Approving File ...');
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);

}


function adminToggleNotification() {
    if (!ajaxactive) {
        ajaxactive = true;
        var fid = document.frmFileDetails.id.value;
        var surl = ajax_post_handler_url + '?op=togglesubscribe&fid=' + fid;
        var callback = {
            success: function(o) {
                var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                var oResults = eval('(' + json + ')');
                if (oResults.error == '') {
                    if (oResults.message != '') {
                        Dom.get('filedetails_statusmsg').innerHTML = oResults.message;
                        Dom.setStyle('filedetails_statusmsg', 'display', 'block');
                    } else {
                        Dom.get('filedetails_statusmsg').innerHTML = '';
                        Dom.setStyle('filedetails_statusmsg', 'display', 'none');
                    }
                    var obj = Dom.get('listingNotifyIconRec' + oResults.fid );
                    if (obj) {
                        obj.src = oResults.notifyicon;
                        obj.title = oResults.notifymsg;
                    }
                    if (oResults.subscribed) {
                        Dom.get('notifyfiledetailslink').innerHTML = 'UnSubscribe';
                    } else {
                        Dom.get('notifyfiledetailslink').innerHTML = 'Subscribe';
                    }
                    timer = setTimeout('Dom.setStyle("filedetails_statusmsg", "display", "none")', 3000);
                    updateAjaxStatus();
                } else {
                    alert('Error locking file');
                }
            },
            failure: function(o) {
                YAHOO.log('AJAX Update Error: ' + o.status);
            },
            argument: {},
            timeout:55000
        }
        updateAjaxStatus('Updating Notification ...');
        YAHOO.util.Connect.asyncRequest('POST', surl, callback);
    }
};


function makeAJAXToggleFavorite(id) {
    var surl = ajax_post_handler_url + '?op=togglefavorite&id=' + id;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                var obj = Dom.get('favitem' + id);
                if (obj) obj.src = oResults.favimgsrc;
            } else {
                alert('Error setting item favorite status');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating ....');
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
};


function makeAJAXGetFolderListing(cid) {
    if (!blockui)  {
        blockui=true;
        $.blockUI();
    }
    timeDiff.setStartTime(); // Reset the timer
    document.frmtoolbar.newcid.value = cid;
    var surl = ajax_post_handler_url + '?op=getfolderlisting&cid=' + cid;
    var callback = {
        success: function(o) {
            YAHOO.log('getFolderListing Return: ' + timeDiff.getDiff() + 'ms');
            var root = o.responseXML.documentElement;
            var oResults = new Object();
            oResults.retcode = parseXML(root,'retcode');
            if (oResults.retcode == 200) {
                oResults.cid = parseXML(root,'cid');
                oResults.activefolder = parseXML(root,'activefolder');
                oResults.displayhtml = parseXML(root,'displayhtml');
                Dom.get('activefolder_container').innerHTML = oResults.activefolder;
                if (!Event.getListeners('newfilelink') && Dom.get('newfilelink')) {
                    var oLinkNewFileButton = new YAHOO.widget.Button("newfilelink");
                    Event.addListener("newfilelink", "click", showAddFilePanel);
                }
                document.frmtoolbar.cid.value = oResults.cid;
                YAHOO.log('getFolderlisiting: initiate rendering filelisting:' + timeDiff.getDiff() + 'ms');
                renderFileListing(oResults);
                Dom.setStyle('expandcollapsefolders','display','');
                YAHOO.log('getFolderlisiting: initiate rendering leftside navigation:' + timeDiff.getDiff() + 'ms');
                YAHOO.nexfile.showLeftNavigation();
                YAHOO.log('getFolderlisiting Updated page completed in: ' + timeDiff.getDiff() + 'ms');
                updateAjaxStatus('File listing generated in: ' + timeDiff.getDiff() + 'ms');
                timer = setTimeout("Dom.setStyle('nexfile_ajaxStatus','visibility','hidden')", 3000);
                if (blockui)  {
                    setTimeout('$.unblockUI()',200);
                    blockui = false;
                }
                try {
                    var lastRenderedFiles = parseXML(root,'lastrenderedfiles');
                    if (lastRenderedFiles) {
                        YAHOO.log('showfiles: initiate getmorefiledata:' + timeDiff.getDiff() + 'ms');
                        YAHOO.nexfile.getmorefiledata(lastRenderedFiles);
                        YAHOO.log('showfiles: completed getmorefiledata:' + timeDiff.getDiff() + 'ms');
                    }
                } catch(e) {}

            } else {
                alert('Error getting folder listing');
            }

        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('activity');
    YAHOO.util.Connect.asyncRequest('GET', surl, callback);
}


function makeAJAXSetFolderOrder(cid,direction) {
    var listingcid = document.frmtoolbar.cid.value;
    var surl = ajax_post_handler_url + '?op=setfolderorder&direction=' + direction + '&cid=' + cid + '&listingcid=' + listingcid;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                renderFileListing(oResults);
            } else {
                alert('Error setting folder order');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating ....');
    YAHOO.util.Connect.asyncRequest('GET', surl, callback);
}


function makeAJAXSearchTags(searchtags,removetag) {

    timeDiff.setStartTime();    // Reset Timer
    document.fsearch.query.value = '';
    if (searchtags == 'removetag') {
        var surl = ajax_post_handler_url + '?op=searchtags&tags=' + document.fsearch.tags.value + '&removetag=' + removetag;
    } else {
        var surl = ajax_post_handler_url + '?op=searchtags&tags=' + searchtags;
    }
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                Dom.get('activefolder_container').innerHTML = oResults.activefolder;
                Dom.get('filelistingheader').innerHTML = oResults.header;
                renderFileListing(oResults);
                if (oResults.searchtags) {
                    Dom.setStyle('showactivetags','display','block');
                    Dom.get('activesearchtags').innerHTML = oResults.currentsearchtags;
                    Dom.get('tagcloud_words').innerHTML = oResults.tagcloud;
                    document.fsearch.tags.value=oResults.searchtags;
                } else {
                    Dom.setStyle('showactivetags','display','none');
                    document.fsearch.tags.value='';
                    if (oResults.tagcloud) {
                        Dom.get('tagcloud_words').innerHTML = oResults.tagcloud;
                    }
                }
                updateAjaxStatus('File listing generated in: ' + timeDiff.getDiff() + 'ms');

            } else {
                alert('Error processing tag search');
                updateAjaxStatus();
            }
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('activity');
    YAHOO.util.Connect.asyncRequest('GET', surl, callback);
}



function makeAJAXDeleteQueueFile(fid) {
    var surl = ajax_post_handler_url + '?op=deletequeuefile&fid=' + fid;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                renderLeftNavigation(oResults);
                Dom.get('filelisting_container').innerHTML = oResults.displayhtml;
                YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');
                clearCheckedItems();
            } else {
                alert('Error setting item favorite status');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating ....');
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
}

function moveQueueFile() {
    YAHOO.container.moveQueueFileDialog.hide();

    var surl = ajax_post_handler_url + '?op=movequeuefile';
    var formObject = document.getElementById('frmQueueFileMove');

    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                renderLeftNavigation(oResults);
                Dom.get('filelisting_container').innerHTML = oResults.displayhtml;
                YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');
                clearCheckedItems();
            } else {
                alert(oResults.errmsg);
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }

    YAHOO.util.Connect.setForm(formObject, false);
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
}


function makeAJAXSearch(form) {
    clearAjaxActivity();
    if (!blockui)  {
        blockui=true;
        $.blockUI();
    }
    timeDiff.setStartTime();    // Reset Timer
    Dom.setStyle('showactivetags','display','none');
    YAHOO.container.tagspanel.hide();
    var surl = ajax_post_handler_url + '?op=search&query=' + document.fsearch.query.value;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (blockui)  {
                setTimeout('$.unblockUI()',200);
                blockui = false;
            }
            if (oResults.retcode == 200) {
                Dom.get('activefolder_container').innerHTML = oResults.activefolder;
                Dom.get('filelistingheader').innerHTML = oResults.header;
                renderFileListing(oResults);
                updateAjaxStatus('File listing generated in: ' + timeDiff.getDiff() + 'ms');

            } else {
                alert('Error processing tag search');
                updateAjaxStatus();
            }

        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('activity');
    YAHOO.util.Connect.asyncRequest('GET', surl, callback);
}

function makeAJAXBroadcastNotification () {
    timeDiff.setStartTime();    // Reset Timer
    var surl = ajax_post_handler_url + '?op=broadcastalert';
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                updateAjaxStatus();
                alert('Broadcast message sent to ' + oResults.count + ' users');
                updateAjaxStatus('Broadcast Email n compeleted in: ' + timeDiff.getDiff() + 'ms');
                YAHOO.container.broadcastDialog.hide();
                timer = setTimeout("Dom.setStyle('nexfile_ajaxStatus','visibility','hidden')", 3000);

            } else {
                alert('Error processing broadcast');
                updateAjaxStatus();
            }
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('activity');
    var formObject = document.frmBroadcast;
    YAHOO.util.Connect.setForm(formObject);
    YAHOO.util.Connect.asyncRequest('GET', surl, callback);

}


function closeAlert() {
    Dom.setStyle('cancelalert', 'display', 'none'); // Hide the cancel icon
    // Note: Key to this working is having the div's overflow:auto set
    var myAnim = new YAHOO.util.Anim('nexfile_alert', { height: { to: 0 }},1, YAHOO.util.Easing.easeOut);
    myAnim.animate();
    timer = setTimeout("Dom.setStyle('nexfile_alert', 'display', 'none')", 1000);
}


/* START - Functions to handle the New File Upload */
function handleContentReady () {
    // Allows the uploader to send log messages to trace, as well as to YAHOO.log
    uploader.setAllowLogging(true);

    // Restrict selection to a single file (that's what it is by default,
    // just demonstrating how).
    uploader.setAllowMultipleFiles(false);

    // New set of file filters.
    var ff = new Array({description:"All Files", extensions:"*.*"},
                       {description:"Images", extensions:"*.jpg;*.png;*.gif"},
                       {description:"Videos", extensions:"*.avi;*.mov;*.mpg"});

    // Apply new set of file filters to the uploader.
    uploader.setFileFilters(ff);
    document.getElementById("fileName").innerHTML = '';
    uploader.enable();
    Dom.setStyle('btnClearUpload','visibility','hidden');
}


function onFileSelect(event) {
    for (var item in event.fileList) {
        if(YAHOO.lang.hasOwnProperty(event.fileList, item)) {
            fileID = event.fileList[item].id;
        }
    }
    if (fileID != null) {
        document.getElementById('btnNewFileSubmit').disabled=false;
        if (document.frmNewFile.op.value == 'savefile') {
            var elm = document.getElementById('newfile_category');
            if (elm.options[elm.selectedIndex].value == 0)
                document.getElementById('btnNewFileSubmit').disabled=true;
        }
        Dom.setStyle('btnClearUpload','visibility','visible');
        uploader.disable();
        var filename = document.getElementById("fileName");
        filename.innerHTML = event.fileList[fileID].name;
        Dom.get('newfile_displayname').value = filename.innerHTML;
        var progressbar = document.getElementById("progressBar");
        progressbar.innerHTML = "";
        document.getElementById('newfile_displayname').focus();
    }
}

function onCategorySelect(elm) {
    if (document.frmNewFile.op.value == 'savefile') {
        if ( fileID != null && elm.options[elm.selectedIndex].value > 0) {
            document.getElementById('btnNewFileSubmit').disabled=false;
        } else {
            document.getElementById('btnNewFileSubmit').disabled=true;
        }
    }
}



function makeAJAXToggleFileNotification(fid,cid) {
    var surl = ajax_post_handler_url + '?op=togglesubscribe&fid=' + fid + '&cid=' + cid;
    var callback = {
      success:  function(o) {
        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
        var oResults = eval('(' + json + ')');
        if (oResults.retcode == 200) {
            if (oResults.fid && oResults.fid > 0) {
              var obj = Dom.get('listingNotifyIconRec' + oResults.fid );
              obj.src = oResults.notifyicon;
              obj.title = oResults.notifymsg;
            } else if (cid > 0) {
                  var obj = Dom.get('folderNotifyIconRec' + cid);
                  obj.src = oResults.notifyicon;
                  obj.title = oResults.notifymsg;
            }
        } else {
            alert('Unexpected result ' + oResults.retcode);
        }
        updateAjaxStatus();

      },
      failure: function(o) {
        YAHOO.log('AJAX Update Error: ' + o.status);
      },
      argument: {},
      timeout:300000  // 5 min
    };
    updateAjaxStatus('Updating Notification ...');
    YAHOO.util.Connect.asyncRequest('GET', surl , callback);
};

function doAJAXEditVersionNote(fobj) {

    var fid = fobj.fid.value;
    var callback = {
      success:  function(o) {
        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
        var oResults = eval('(' + json + ')');
        if (oResults.retcode == 200) {
            if (oResults.fid > 0) {
              Dom.get('displayfiledetails').innerHTML = oResults.displayhtml;
            }
        } else {
            alert('Unexpected result ' + oResults.retcode);
        }
        updateAjaxStatus();
      },
      failure: function(o) {
        YAHOO.log('AJAX Update Error: ' + o.status);
      },
      argument: {},
      timeout:12000  // 2 min
    };
    updateAjaxStatus('Updating ...');
    YAHOO.util.Connect.setForm(fobj, false);
    YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url , callback);
};

function doAJAXDeleteVersion(fid,version) {

    var surl = ajax_post_handler_url + '?op=deleteversion&fid=' + fid + '&version=' + version;
    var callback = {
      success:  function(o) {
        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
        var oResults = eval('(' + json + ')');
        if (oResults.retcode == 200) {
            if (oResults.fid > 0) {
              Dom.get('displayfiledetails').innerHTML = oResults.displayhtml;
            }
        } else {
            alert('Unexpected result ' + oResults.retcode);
        }
        updateAjaxStatus();
      },
      failure: function(o) {
        YAHOO.log('AJAX Update Error: ' + o.status);
      },
      argument: {},
      timeout:120000  // 2 min
    };
    updateAjaxStatus('Deleting Version ...');
    YAHOO.util.Connect.asyncRequest('POST', surl , callback);
};


function doAJAXDeleteNotification(type,id) {

    var surl = ajax_post_handler_url + '?op=deletenotification&id=' + id + '&type=' + type;
    var callback = {
      success:  function(o) {
        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
        var oResults = eval('(' + json + ')');
        if (oResults.retcode == 200) {
            Dom.get('filelisting_container').innerHTML = oResults.displayhtml;
            var myTabs = new YAHOO.widget.TabView('notification_report');
            if (type == 'file') {
                myTabs.set('activeIndex',0);
            } else {
                myTabs.set('activeIndex',1);
            }
            Dom.setStyle('filelistingheader', 'display', 'none');
            Dom.setStyle('reportlisting_container', 'display', '');
            YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');
            // Setup the Notifications Settings Dialog
            Dom.setStyle('notificationsettingsdialog', 'display', 'block');
        } else {
            alert('Unexpected result ' + oResults.retcode);
        }
        updateAjaxStatus();
      },
      failure: function(o) {
        YAHOO.log('AJAX Update Error: ' + o.status);
      },
      argument: {},
      timeout:120000  // 2 min
    };
    updateAjaxStatus('Deleting Notification ...');
    YAHOO.util.Connect.asyncRequest('POST', surl , callback);
};

function doAJAXUpdateNotificationSettings(formObject) {

    var surl = ajax_post_handler_url + '?op=updatenotificationsettings';
    var callback = {
      success:  function(o) {
        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
        var oResults = eval('(' + json + ')');
        if (oResults.retcode == 200) {
            Dom.get('filelisting_container').innerHTML = oResults.displayhtml;
            var myTabs = new YAHOO.widget.TabView('notification_report');
            myTabs.set('activeIndex',3);
            Dom.setStyle('filelistingheader', 'display', 'none');
            Dom.setStyle('reportlisting_container', 'display', '');
            YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');
            // Setup the Notifications Settings Dialog
            Dom.setStyle('notificationsettingsdialog', 'display', 'block');
        } else {
            alert('Unexpected result ' + oResults.retcode);
        }
        updateAjaxStatus();
      },
      failure: function(o) {
        YAHOO.log('AJAX Update Error: ' + o.status);
      },
      argument: {},
      timeout:120000  // 2 min
    };
    updateAjaxStatus('Updating Notification Settings ...');
    YAHOO.util.Connect.setForm(formObject);
    YAHOO.util.Connect.asyncRequest('POST', surl , callback);
};

function doAJAXUpdateFolderNotificationSettings(formObject) {
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                togglefolderoptions();
            } else {
                alert('Error saving notification options');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Updating Settings ...');
    YAHOO.util.Connect.setForm(formObject, false);
    YAHOO.util.Connect.asyncRequest('POST', ajax_post_handler_url, callback);
};

function doAJAXClearNotificationLog() {
    var surl = ajax_post_handler_url + '?op=clearnotificationlog';
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                var myTabs = new YAHOO.widget.TabView('notification_report');
                myTabs.set('activeIndex',2);
                Dom.setStyle('notificationlog_report','display','none');
                Dom.setStyle('notificationlog_norecords','display','');
            } else {
                alert('Error clearing notification log');
            }
            updateAjaxStatus();
        },
        failure: function(o) {
            YAHOO.log('AJAX Update Error: ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('Clearing Notification Log');
    YAHOO.util.Connect.asyncRequest('GET', surl, callback);
};

function upload() {
    if (fileID != null) {
        timeDiff.setStartTime();
        uploader.upload(fileID, ajax_post_handler_url,
        "POST",{
            op: document.getElementById("newfile_op").value,
            fid: document.getElementById("newfile_fid").value,
            category: document.getElementById("newfile_category").value,
            displayname: document.getElementById("newfile_displayname").value,
            tags: document.getElementById("newfile_tags").value,
            description: document.getElementById("newfile_desc").value,
            versionnote: document.getElementById("newfile_notes").value,
            notify: document.getElementById("updatenotify").checked,
            cookie_session: document.getElementById("cookie_session").value
        });
        fileID = null;
    }
}

function onUploadProgress(event) {
    prog = Math.round(220*(event["bytesLoaded"]/event["bytesTotal"]));
    progbar = '<div class="uploaderprogress" style="background-color:#f00; width: ' + prog + 'px" ></div>';
    var progressbar = document.getElementById("progressBar");
    progressbar.innerHTML = progbar;
}


function onUploadComplete(event) {
    uploaderInit();
    YAHOO.container.newfiledialog.hide();
}

function onUploadResponse(o) {
    var category = document.getElementById("newfile_category").value;
    var json = o.data.substring(o.data.indexOf('{'), o.data.lastIndexOf('}') + 1);
    var oResults = eval('(' + json + ')');
    if (oResults.retcode == 200) {
        YAHOO.log('upload Response: ' + oResults.retcode);
        if (oResults.op == 'savefile') {
            if (oResults.message != '') {
                Dom.get('nexfile_alert_content').innerHTML = oResults.message;
                Dom.setStyle('nexfile_alert','display','');
            }
            if (initialop == 'newprojectfile') {
                document.location = siteurl + '/nexproject/viewproject.php?pid=' + initialparm;
            } else {
                document.frmtoolbar.cid.value = oResults.cid;
                YAHOO.nexfile.showfiles();
            }

        } else if (oResults.op == 'saveversion' && oResults.fid > 0) {
            YAHOO.nexfile.refreshFileDetails(oResults.fid);
        }
    } else {
        alert(oResults.error);
    }
}

function uploaderInit() {
    uploader.clearFileList();
    Dom.setStyle('btnClearUpload','visibility','hidden');
    uploader.enable();
    document.getElementById("fileName").innerHTML = '';
    progbar = '<div class="uploaderprogress"></div>';
    document.getElementById("progressBar").innerHTML = progbar;
    document.getElementById('btnNewFileSubmit').disabled=true;
    fileID = null;
}

// Tests if any active files have been selected - if not disable the "More Actions' select element
function enable_multiaction(selected_files,selected_folders) {
    if (selected_files == '' && selected_folders == '') {
        clearCheckedItems();
    } else {
        Dom.get('multiaction').disabled = false;
        Dom.replaceClass('multiaction','disabled_element','enabled_element');
    }
}

function clearCheckedItems() {

    // There will be no chkfile element if there are no listing results
    try {
        if (document.frmfilelisting.chkfile) {
            if (document.frmfilelisting.chkfile.length) {
                for (i=0; i<document.frmfilelisting.chkfile.length; i++) {
                    document.frmfilelisting.chkfile[i].checked = false;
                }

                // Check or un-check the folder checkboxes
                if (document.frmfilelisting.chkfolder) {
                    for (i=0; i<document.frmfilelisting.chkfolder.length; i++) {
                        document.frmfilelisting.chkfolder[i].checked = false;
                    }
                }

            } else if (document.frmfilelisting.chkfile.value > 0) {
                try {
                    document.frmfilelisting.chkfolder.checked = false;
                } catch (e) {}
                document.frmfilelisting.chkfile.checked = false;
            }
        }
    } catch (e) {}
    Dom.get('multiaction').disabled = true;
    Dom.replaceClass('multiaction','enabled_element','disabled_element');
    document.frmtoolbar.checkeditems.value='';
    Dom.get('headerchkall').checked = false;

}


function updateCheckedItems(obj,type) {
    if (type == 'folder') {
        var field = document.frmtoolbar.checkedfolders;
    } else {
        var field = document.frmtoolbar.checkeditems;
    }
    if (obj.checked) {
        field.value += ',' + obj.value;
    } else {
        if (field.value == field.value.replace(obj.value + ',', '')) {
            field.value = field.value.replace(obj.value, '');
        } else {
            field.value = field.value.replace(obj.value + ',', '');
        }
    }
    // Remove any leading comma
    field.value = field.value.replace(/^,*/g, '');
    enable_multiaction(field.value);
}

function toggleCheckedItems(obj,files) {

    var selectedFilesField = document.frmtoolbar.checkeditems;
    var selectedFoldersField = document.frmtoolbar.checkedfolders;
    if (obj.value == 'all') {
        // Need to update the hidden fields in the header toolbar form - the 'More Actions' dropdown form
        selectedFilesField.value = '';
        selectedFoldersField.value = '';
        // Need to test if only 1 checkbox exists on page or multiple

        try {
            if (document.frmfilelisting.chkfile.length) {
                for (i=0; i<document.frmfilelisting.chkfile.length; i++) {
                    document.frmfilelisting.chkfile[i].checked = obj.checked ? true : false;
                    if (obj.checked) {
                        var itemvalue = document.frmfilelisting.chkfile[i].value;
                        selectedFilesField.value = selectedFilesField.value + itemvalue + ',';
                    }
                }

                // Check or un-check the folder checkboxes
                if (document.frmfilelisting.chkfolder) {
                    for (i=0; i<document.frmfilelisting.chkfolder.length; i++) {
                        document.frmfilelisting.chkfolder[i].checked = obj.checked ? true : false;
                        if (obj.checked) {
                            var itemvalue = document.frmfilelisting.chkfolder[i].value;
                            selectedFoldersField.value = selectedFoldersField.value + itemvalue + ',';
                        }
                    }
                }

            } else if (document.frmfilelisting.chkfile.value > 0) {
                if (obj.checked)
                    field.value = document.frmfilelisting.chkfile.value;
                    try {
                        document.frmfilelisting.chkfolder.checked = obj.checked ? true : false;
                    } catch (e) {}
                document.frmfilelisting.chkfile.checked = obj.checked ? true : false;
            }
        } catch(e) {
            // Check or un-check the folder checkboxes
            if (document.frmfilelisting.chkfolder) {
                for (i=0; i<document.frmfilelisting.chkfolder.length; i++) {
                    document.frmfilelisting.chkfolder[i].checked = obj.checked ? true : false;
                }
            }
            // Check or un-check the folder checkboxes
            if (document.frmfilelisting.chkfolder) {
                for (i=0; i<document.frmfilelisting.chkfolder.length; i++) {
                    document.frmfilelisting.chkfolder[i].checked = obj.checked ? true : false;
                    if (obj.checked) {
                        var itemvalue = document.frmfilelisting.chkfolder[i].value;
                        selectedFoldersField.value = selectedFoldersField.value + itemvalue + ',';
                    }
                }
            }

        }

        // Remove the trailing comma
        selectedFilesField.value = selectedFilesField.value.replace(/,$/g, '');
        selectedFoldersField.value = selectedFoldersField.value.replace(/,$/g, '');

    } else if (obj.value > 0) {
        // convert to an array
        files = files.split(',');
        for (i=0; i<files.length; i++) {
            if (obj.checked) {
                try {
                    Dom.get('chkfile' + files[i]).checked=true;
                } catch (e) {}

            } else {
                try {
                    Dom.get('chkfile' + files[i]).checked=false;
                } catch (e) {}
            }
        }
        // Need to update the hidden field in the header form - the 'More Actions' dropdown form
        selectedFilesField.value = '';
        selectedFoldersField.value = '';
        // Need to test if only 1 checkbox exists on page or multiple
        try {
            if (document.frmfilelisting.chkfile.length) {
                for (i=0; i<document.frmfilelisting.chkfile.length; i++) {
                    if (document.frmfilelisting.chkfile[i].checked) {
                        var itemvalue = document.frmfilelisting.chkfile[i].value;
                        selectedFilesField.value = selectedFilesField.value + itemvalue + ',';
                    }
                }
            } else if (document.frmfilelisting.chkfile.value > 0) {
                if (document.frmfilelisting.chkfile.checked)
                    selectedFilesField.value = document.frmfilelisting.chkfile.value;
            }
        } catch (e) {}

        // Check or un-check the folder checkboxes
        if (document.frmfilelisting.chkfolder) {
            var chkobj;
            for (i=0; i<document.frmfilelisting.chkfolder.length; i++) {
                chkobj = document.frmfilelisting.chkfolder[i];
                if (chkobj.checked) {
                    var itemvalue = chkobj.value;
                    selectedFoldersField.value = selectedFoldersField.value + itemvalue + ',';
                }
            }
        }

        // Remove the trailing comma and leading comma
        selectedFilesField.value = selectedFilesField.value.replace(/,$/g, '');
        selectedFilesField.value = selectedFilesField.value.replace(/^,*/g, '');

        selectedFoldersField.value = selectedFoldersField.value.replace(/,$/g, '');
        selectedFoldersField.value = selectedFoldersField.value.replace(/^,*/g, '');

    }
    enable_multiaction(selectedFilesField.value,selectedFoldersField.value);

}


function toggleCheckedNotificationItems(obj) {

    var selectedFilesField = document.frmtoolbar.checkeditems;
    var selectedFoldersField = document.frmtoolbar.checkedfolders;
    // Need to update the hidden fields in the header toolbar form - the 'More Actions' dropdown form

    if (obj.id == 'chkallfiles') {
        // Need to test if only 1 checkbox exists on page or multiple
        selectedFilesField.value = '';
        try {
            if (document.frmfilelisting.chkfile.length) {
                for (i=0; i<document.frmfilelisting.chkfile.length; i++) {
                    document.frmfilelisting.chkfile[i].checked = obj.checked ? true : false;
                    if (obj.checked) {
                        var itemvalue = document.frmfilelisting.chkfile[i].value;
                        selectedFilesField.value = selectedFilesField.value + itemvalue + ',';
                    }
                }

            } else if (document.frmfilelisting.chkfile.value > 0) {
                if (obj.checked)
                    field.value = document.frmfilelisting.chkfile.value;
                    try {
                        document.frmfilelisting.chkfile.checked = obj.checked ? true : false;
                    } catch (e) {}
                document.frmfilelisting.chkfile.checked = obj.checked ? true : false;
            }
        } catch(e) { }

        // Remove the trailing comma
        selectedFilesField.value = selectedFilesField.value.replace(/,$/g, '');

    } else if (obj.id == 'chkallfolders') {
        // Need to test if only 1 checkbox exists on page or multiple
        selectedFoldersField.value = '';
        try {
            if (document.frmfilelisting.chkfolder.length) {
                for (i=0; i<document.frmfilelisting.chkfolder.length; i++) {
                    document.frmfilelisting.chkfolder[i].checked = obj.checked ? true : false;
                    if (obj.checked) {
                        var itemvalue = document.frmfilelisting.chkfolder[i].value;
                        selectedFoldersField.value = selectedFoldersField.value + itemvalue + ',';
                    }
                }

            } else if (document.frmfilelisting.chkfolder.value > 0) {
                if (obj.checked)
                    field.value = document.frmfilelisting.chkfolder.value;
                    try {
                        document.frmfilelisting.chkfolder.checked = obj.checked ? true : false;
                    } catch (e) {}
                document.frmfilelisting.chkfolder.checked = obj.checked ? true : false;
            }
        } catch(e) { }

        // Remove the trailing comma
        selectedFoldersField.value = selectedFoldersField.value.replace(/,$/g, '');

    }

    enable_multiaction(selectedFilesField.value,selectedFoldersField.value);

}



// Two Functions used to initialize panels and forms for Add New File 'upload' and Add new Version 'edit'
function showAddFilePanel() {
    var activefolder = document.frmtoolbar.cid.value;
    Dom.setStyle('newfiledialog_folderrow', 'display', '');
    Dom.setStyle('newfiledialog_filedesc', 'display', '');
    Dom.setStyle('newfiledialog_filename', 'display', '');
    Dom.get('newfile_displayname').value='';
    Dom.get('newfile_tags').value='';
    Dom.get('newfile_desc').value='';
    Dom.get('newfile_notes').value='';
    Dom.get('updatenotify').checked='';

    // Use this ajax request to get the latest folder options
    var surl = ajax_post_handler_url + '?op=rendernewfilefolderptions' + '&cid=' + activefolder;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            Dom.get('newfile_selcategory').innerHTML = oResults.displayhtml;
            Dom.get('newfiledialog_heading').innerHTML='Add a new file';
            YAHOO.container.newfiledialog.cfg.setProperty("visible",true);
            if (!Event.getListeners('btnNewFileCancel')) {   // Check first to see if listener already active
                Event.addListener("btnNewFileCancel", "click",hideNewFilePanel, YAHOO.container.newfiledialog, true);
            }
        },
        failure: function(o) {
            YAHOO.log('AJAX error loading add file form : ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);

}

function showAddNewVersion() {
    /* Clear form and then show it */
    //Dom.get('newfile_category').options[0].selected=true;
    Dom.get('newfiledialog_heading').innerHTML='Add new version';
    document.frmNewFile.op.value = 'saveversion';
    document.frmNewFile.fid.value = document.frmFileDetails.id.value;
    Dom.setStyle('newfiledialog_folderrow', 'display', 'none');
    Dom.setStyle('newfiledialog_filedesc', 'display', 'none');
    Dom.setStyle('newfiledialog_filename', 'display', 'none');
    Dom.get('newfile_op').value='saveversion';
    Dom.get('newfile_tags').value='';
    Dom.get('newfile_notes').value='';
    Dom.get('updatenotify').checked='';
    YAHOO.container.newfiledialog.cfg.setProperty("visible",true);
}

function showAddCategoryPanel() {
    var activefolder = document.frmtoolbar.cid.value;
    var surl = ajax_post_handler_url + '?op=rendernewfolderform' + '&cid=' + activefolder;
    var callback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            Dom.get('newfolderdialog_form').innerHTML = oResults.displayhtml;
            YAHOO.container.newfolderdialog.cfg.setProperty("visible",true);
            if (!Event.getListeners('btnNewFolderSubmit')) {   // Check first to see if listener already active
                Event.addListener("btnNewFolderSubmit", "click", makeAJAXCreateFolder, YAHOO.container.newfolderdialog, true);
            }
            if (!Event.getListeners('btnNewFolderCancel')) {   // Check first to see if listener already active
                Event.addListener("btnNewFolderCancel", "click",YAHOO.container.newfolderdialog.hide, YAHOO.container.newfolderdialog, true);
            }
        },
        failure: function(o) {
            YAHOO.log('AJAX error loading new folder form : ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    YAHOO.util.Connect.asyncRequest('POST', surl, callback);
};



function renderLeftNavigation(oResults) {

    try {
        if (!Event.getListeners('folderoptions_link')) {   // Check first to see if listener already active
            Event.addListener("folderoptions_link","click",togglefolderoptions);
        }
    } catch (e) {}

    try {
        YAHOO.container.newfolderdialog.hide();
    } catch (e) {}

    var tree;
    tree = new YAHOO.widget.TreeView("nexfileNavTreeDiv");
    var root = tree.getRoot();
    if((oResults.reports) && (oResults.reports.length)) {
        //Result is an array if more than one result, string otherwise
        if(YAHOO.lang.isArray(oResults.reports)) {
            var reportlinks = new YAHOO.widget.TextNode("Reports", root, true);
            reportlinks.labelStyle = "icon-files";
            for (var i=0, j=oResults.reports.length; i<j; i++) {
                eval('var menuobj = { label: "' + oResults.reports[i]['name'] + '", href:"' + oResults.reports[i]['link'] + '" }');
                var tempNode = new YAHOO.widget.TextNode(menuobj, reportlinks, false);
                tempNode.labelStyle = oResults.reports[i]['icon'];
            }
        }
    }

    if((oResults.recentfolders) && (oResults.recentfolders.length)) {
        //Result is an array if more than one result, string otherwise
        if(YAHOO.lang.isArray(oResults.recentfolders)) {
            var recentfolders = new YAHOO.widget.TextNode("Recent&nbsp;Folders", root, true);
            recentfolders.labelStyle = "icon-allfolders";
            for (var i=0, j=oResults.recentfolders.length; i<j; i++) {
                eval('var menuobj = { label: "' + oResults.recentfolders[i]['name'] + '", href:"' + oResults.recentfolders[i]['link'] + '" }');
                var tempNode = new YAHOO.widget.TextNode(menuobj, recentfolders, false);
                tempNode.labelStyle = oResults.recentfolders[i]['icon'];
            }
        }
    }


    if((oResults.topfolders) && (oResults.topfolders.length)) {
        //Result is an array if more than one result, string otherwise
        if(YAHOO.lang.isArray(oResults.topfolders)) {
            var topfolders = new YAHOO.widget.TextNode("Top&nbsp;Level&nbsp;Folders", root, true);
            topfolders.labelStyle = "icon-allfolders";
            for (var i=0, j=oResults.topfolders.length; i<j; i++) {
                eval('var menuobj = { label: "' + oResults.topfolders[i]['name'] + '", href:"' + oResults.topfolders[i]['link'] + '" }');
                var tempNode = new YAHOO.widget.TextNode(menuobj, topfolders, false);
                tempNode.labelStyle = oResults.topfolders[i]['icon'];
            }
        }
    }

    tree.subscribe('clickEvent', function(oArgs) {
            if (Dom.get('nexfile_alert').style.display != 'none')
                closeAlert();
            var hrefparts = oArgs.node.href.split('=');
            if (hrefparts[0] == 'reportmode') {
                document.frmtoolbar.reportmode.value=hrefparts[1];
                document.frmtoolbar.cid.value = 0;
                YAHOO.nexfile.showfiles();
            } else if (hrefparts[0] == 'cid') {
                document.frmtoolbar.cid.value=hrefparts[1];
                document.frmtoolbar.reportmode.value='';
                YAHOO.nexfile.showfiles();
            }
            return true;
         });
    tree.render();

}

// Common function called by AJAX Return functions that need to update the filelisting container
function renderFileListing(oResults) {
    Dom.get('filelisting_container').innerHTML = oResults.displayhtml;
    if (nexfilefolders == "expanded") {
        var linkobj = document.getElementById('expandcollapsefolderslink');
        if (linkobj) expandCollapseFolders(linkobj,'expand')
    }
    if (nexfiledetail == 'expanded')  showhideFileDetail('show');
    // Disabled after finding a bug in IE if content with corners is below browser viewport and content has to be scrolled to view
    // Content was wrapping and appearing in wrong position
    //jQuery('.listing_searchtag').corner("8px");
    //jQuery('.listing_activetag').corner("8px");

    folderList = Dom.getElementsByClassName('folder_withhover');
    for(var i=0; i< folderList.length; i++) {
        Event.addListener(folderList[i], 'mouseover', showFolderMoveActions, folderList[i]);
        Event.addListener(folderList[i], 'mouseout', hideFolderMoveActions, folderList[i]);
    }
    clearCheckedItems();
}

YAHOO.nexfile.showLeftNavigation = function() {
    /* Generate Left Side Folder Navigation */
    var surl = ajax_post_handler_url + '?op=getleftnavigation';
    var navcallback = {
        success: function(o) {
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            renderLeftNavigation(oResults);
        },
        failure: function(o) {
            YAHOO.log('AJAX error loading leftside navigation : ' + o.status);
        },
        argument: {},
        timeout:55000
    }

    YAHOO.util.Connect.asyncRequest('POST', surl, navcallback);
};

YAHOO.nexfile.getmorefiledata = function(data) {
    clear_ajaxactivity = false;
    folderstack = [];
    //debugger;
    var php = new PHP_Serializer();
    lastfiledata = php.unserialize(data);
    YAHOO.nexfile.threadedRequestManager(0);
};

YAHOO.nexfile.threadedRequestManager = function(i) {
    YAHOO.log('YAHOO.nexfile.threadedRequestManager - i: ' + i);
    if (folderstack.length > numGetFileThreads) {
        timerArray[i] = setTimeout('YAHOO.nexfile.threadedRequestManager(' + i + ')',500);
    } else if (i == 0 || lastfiledata.length > 0) {
        requestdata = lastfiledata.shift();
        YAHOO.nexfile.getmorefiledataRequest(requestdata[0],requestdata[1],requestdata[2],requestdata[3]);
        folderstack.push(requestdata[0]);   // used to track the folders being prcessed.
        i++;
        YAHOO.nexfile.threadedRequestManager(i);
    }
}


YAHOO.nexfile.generateThreadedFileRequest = function(requestdata) {
        folderstack.push(requestdata[0]);   // used to track the folders being prcessed.
        timerArray[i] = setTimeout('YAHOO.nexfile.getmorefiledataRequest('
            + requestdata[0] + ','      //  Folder id   (cid)
            + requestdata[1] + ',"'     //  File id     (fid)
            + requestdata[2] + '",'     //  foldernunber
            + requestdata[3] + ')'      //  level - folder depth
            ,interval);
        YAHOO.log('Schedule getmoredateRequest for cid: ' + requestdata[0] + ' in ' + interval + ' ms');
}

YAHOO.nexfile.getmorefiledataRequest = function(cid,fid,foldernumber,level) {
    if (!clear_ajaxactivity) {
        var surl = ajax_post_handler_url + '?op=getmorefiledata' + '&pending=' + folderstack.length;
        postdata = 'cid='+ cid;
        postdata += '&foldernumber=' + foldernumber;
        postdata += '&level=' + level;
        var callback = {
            success: function(o) {
                //YAHOO.log('getmorefiledata(' + cid + '): return from AJAX call:' + timeDiff.getDiff() + 'ms');
                var root = o.responseXML.documentElement;
                var oResults = new Object();
                oResults.retcode = parseXML(root,'retcode');
                /* Clear the message area for this folder*/
                try {
                    //YAHOO.log("cid:" + cid + " fid:" + fid);
                    Dom.get('listingrec' + fid + '_bottom').innerHTML = '';
                } catch(e) {}

                if (oResults.retcode == 200) {
                    oResults.displayhtml = parseXML(root,'displayhtml');
                    try {
                        Dom.get('subfolder' + cid + '_rec' + fid + '_bottom').innerHTML = oResults.displayhtml;
                    } catch(e) {
                        try {
                            Dom.get('subfolderlisting' + cid + '_bottom').innerHTML = oResults.displayhtml;
                        } catch(e) {}
                    }
                    folderstack = arrayRemoveItem(folderstack,cid); // Remove this folder as it's now been processed
                    //YAHOO.log('folderstack length: ' + folderstack.length);

                }
                if (lastfiledata.length == 0 && folderstack.length == 0) {
                    clearCheckedItems();
                    YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');
                    //alert('Completed loading Data in: ' + timeDiff.getDiff() + 'ms');
                }

            },
            failure: function(o) {
                YAHOO.log('AJAX error loading moredata : ' + o.status);
            },
            argument: {},
            timeout:55000
        }

        YAHOO.util.Connect.asyncRequest('POST', surl, callback,postdata);

    }
}

YAHOO.nexfile.getmorefolderdataRequest = function(cid,fid,foldernumber,level,pass2) {
    if (!blockui)  {
        blockui=true;
        $.blockUI();
    }
    timeDiff.setStartTime(); // Reset the timer
    YAHOO.log('getmorefolderdata: start AJAX call:' + timeDiff.getDiff() + 'ms');
    var surl = ajax_post_handler_url + '?op=getmorefolderdata' + '&pending=' + folderstack.length;
    postdata = 'cid='+ cid;
    postdata += '&foldernumber=' + foldernumber;
    postdata += '&level=' + level;
    if (pass2 == 1) {
        postdata += '&pass2=1'
    }
    var callback = {
        success: function(o) {
            YAHOO.log('getmorefiledata(' + cid + '): return from AJAX call:' + timeDiff.getDiff() + 'ms');
            var root = o.responseXML.documentElement;
            var oResults = new Object();
            oResults.retcode = parseXML(root,'retcode');
            if (oResults.retcode == 200) {
                oResults.displayhtml = parseXML(root,'displayhtml');
                try {
                    Dom.get('subfolder' + cid + '_rec' + fid + '_bottom').innerHTML = oResults.displayhtml;
                } catch(e) {}
            }
            Dom.setStyle('nexfile_ajaxActivity','visibility','hidden');
            timer = setTimeout("Dom.setStyle('nexfile_ajaxStatus','visibility','hidden')", 3000);
            if (blockui)  {
                setTimeout('$.unblockUI()',200);
                blockui = false;
            }
            YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');
        },
        failure: function(o) {
            YAHOO.log('AJAX error loading more folder(' + cid + ') data : ' + o.status);
        },
        argument: {},
        timeout:55000
    }
    updateAjaxStatus('activity');
    YAHOO.util.Connect.asyncRequest('POST', surl, callback,postdata);

}


/* Make the AJAX call to generate an updated file listing and leftside navigation */
YAHOO.nexfile.showfiles = function() {
    YAHOO.log('showfiles: start AJAX call:' + timeDiff.getDiff() + 'ms');
    clearAjaxActivity();
    if (!blockui)  {
        blockui=true;
        $.blockUI();
    }
    timeDiff.setStartTime(); // Reset the timer
    if (cid == undefined && document.frmtoolbar.cid.value > 0) {
        var cid = document.frmtoolbar.cid.value;
        reportmode = '';
    } else {
        var reportmode = document.frmtoolbar.reportmode.value;
    }
    var reportmode = document.frmtoolbar.reportmode.value;
    var surl = ajax_post_handler_url + '?op=getfilelisting&cid='+cid + '&reportmode=' + reportmode;
    document.fsearch.query.value = '';
    YAHOO.container.tagspanel.hide();
    Dom.setStyle('showactivetags','display','none');
    var listingcallback = {
        success: function(o) {
            //YAHOO.log('showfiles: return from AJAX call:' + timeDiff.getDiff() + 'ms');
            var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
            var oResults = eval('(' + json + ')');
            if (oResults.retcode == 200) {
                Dom.setStyle('activefolder_container','display','');
                Dom.setStyle('filelistingheader','display','');
                document.frmtoolbar.cid.value = oResults.cid;

                document.frmBroadcast.cid.value = oResults.cid;
                document.frmBroadcast.message.value = '';
                Dom.get('filelistingheader').innerHTML = oResults.header;

                YAHOO.log('showfiles: start rendering filelisting:' + timeDiff.getDiff() + 'ms');

                if (reportmode == 'notifications') {
                    Dom.get('filelisting_container').innerHTML = oResults.displayhtml;
                    var myTabs = new YAHOO.widget.TabView('notification_report');
                    Dom.setStyle('filelistingheader', 'display', 'none');
                    Dom.setStyle('reportlisting_container', 'display', '');
                    YAHOO.nexfile.alternateRows.init('listing_record', '#FFF', '#EBEBEB');
                    // Setup the Notifications Settings Dialog
                    Dom.setStyle('notificationsettingsdialog', 'display', 'block');
                    if (!Event.getListeners('clearnotificationhistory')) {   // Check first to see if listener already active
                        Event.on('clearnotificationhistory', 'click', doAJAXClearNotificationLog);
                    }
                } else {
                    renderFileListing(oResults);
                }
                YAHOO.log('showfiles: completed rendering filelisting:' + timeDiff.getDiff() + 'ms');

                Dom.get('activesearchtags').innerHTML = '';     // clear any active search tags
                document.fsearch.tags.value='';
                Dom.setStyle('showactivetags','display','none');
                Dom.get('headerchkall').checked = false;

                if (oResults.activefolder) {
                    Dom.get('activefolder_container').innerHTML = oResults.activefolder;
                    if (!Event.getListeners('newfilelink') && Dom.get('newfilelink')) {
                        var oLinkNewFileButton = new YAHOO.widget.Button("newfilelink");
                        Event.addListener("newfilelink", "click", showAddFilePanel);
                    }
                } else {
                    Dom.get('activefolder_container').innerHTML = '';
                }
                if (oResults.moreactions) {
                    var objSelect = Dom.get('multiaction');
                    select_innerHTML(objSelect,oResults.moreactions);
                }

                //YAHOO.log('showfiles: initiate rendering leftside navigation:' + timeDiff.getDiff() + 'ms');
                updateAjaxStatus('File listing generated in: ' + timeDiff.getDiff() + 'ms');
                YAHOO.nexfile.showLeftNavigation();
                //YAHOO.log('showfiles: completed function:' + timeDiff.getDiff() + 'ms');

            } else if (oResults.retcode == 401) {   // No permissions to view this category
                Dom.get('nexfile_alert_content').innerHTML = oResults.error;
                Dom.setStyle('nexfile_alert','display','');
                YAHOO.nexfile.showLeftNavigation();
            } else {
                alert('Unexpected result ' + oResults.retcode);
            }

            Dom.setStyle('nexfile_ajaxActivity','visibility','hidden');
            timer = setTimeout("Dom.setStyle('nexfile_ajaxStatus','visibility','hidden')", 5000);
            if (blockui)  {
                $.unblockUI();
                blockui = false;
                if (initialfid > 0) {
                    setTimeout('makeAJAXLoadFileDetails(' + initialfid + ')',500);
                    initialfid = 0;
                } else if (initialop == 'newprojectfile' && initialparm > 0) {
                    setTimeout('showAddFilePanel()',500);
                }

            }

            // Expand any individual folders user had opened
            for(var i=0; i< expandedfolders.length; i++) {
                expandfolder(expandedfolders[i]);
            }

            try {
                if (oResults['lastrenderedfiles']) {
                    //YAHOO.log('showfiles: initiate getmorefiledata:' + timeDiff.getDiff() + 'ms');
                    YAHOO.nexfile.getmorefiledata(oResults['lastrenderedfiles']);
                    //YAHOO.log('showfiles: completed getmorefiledata:' + timeDiff.getDiff() + 'ms');
                } else {
                    YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');
                }
            } catch(e) {YAHOO.nexfile.alternateRows.init('listing_record','#FFF','#EBEBEB');}

        },
        failure: function(o) {
            YAHOO.log('AJAX error loading main file listing : ' + o.status);
        },
        argument: {},
        timeout:120000 // 2 min
    }
    updateAjaxStatus('activity');
    YAHOO.util.Connect.asyncRequest('POST', surl, listingcallback);
};


YAHOO.nexfile.refreshFileDetails = function(fid) {
    var surl = ajax_post_handler_url + '?op=refreshfiledetails&fid='+fid;
    var callback = {
      success:  function(o) {
        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
        var oResults = eval('(' + json + ')');
        if (oResults.retcode == 200) {
            if (oResults.fid > 0) {
              Dom.get('displayfiledetails').innerHTML = oResults.displayhtml;
            }
        }
        updateAjaxStatus();
      },
      failure: function(o) {
        YAHOO.log('AJAX Update Error: ' + o.status);
      },
      argument: {},
      timeout:120000  // 2 min
    };
    updateAjaxStatus('Updating ...');
    YAHOO.util.Connect.asyncRequest('GET', surl , callback);
};


YAHOO.nexfile.alternateRows = {
    /**
    *    Our init function
    *    @params className String, the table class name
    *    @params firstColor String hex value for first color
    *    @params secondColor String hex value for second color
    */
    init: function(className, firstColor, secondColor){
        // get all the tables with that particular class name
        recordList = Dom.getElementsByClassName(className);
        var closedfolders = new Array();
        YAHOO.log("Set alternating row colors");

        // loop through them
        var color = firstColor;
        var setColor;
        var rows = 0;
        for(var i=0; i< recordList.length; i++) {
            setColor = true;
            if (Dom.hasClass(recordList[i],'subfolder')) {   // This record is a folder so we need to see if it's open or closed
                var elparts = recordList[i].id.split('subfolder');  // Extract the folder id from the record id assigned
                var elc = 'subfolder' + elparts[1] + '_contents';
                // Check if this folder is closed and if so - then skip any records under this folder
                if (Dom.getStyle(elc, 'display') == 'none') {
                    closedfolders.push(elparts[1]);
                    for(var j=0; j < closedfolders.length; j++) {
                        if (Dom.hasClass(recordList[i], 'parentfolder' + closedfolders[j]) ) {
                            setColor = false;
                            break;
                        }
                    }
                }

            } else if (closedfolders.length > 0) {
                // split out the folder id for this listing record
                var elcparts = recordList[i].id.split('folder_');
                var elparts = elcparts[1].split('_rec');
                for(var j=0; j < closedfolders.length; j++) {
                    if (elparts[0] == closedfolders[j]) {
                        setColor = false;
                        break;
                    }
                }
            }

            if (setColor) {
                currentcolor = Dom.getStyle(recordList[i],'backgroundColor');
                rows++;
                Dom.setStyle(recordList[i], 'backgroundColor', color);
                if (color == firstColor) {
                    color = secondColor;
                } else {
                    color = firstColor;
                }
            }

        }

    }
};


