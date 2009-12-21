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

var init_nexfile = function() {

    YAHOO.widget.Logger.enableBrowserConsole()
    // Since we are using AJAX to update display, the cookie is not updated as the page is not refreshing.
    // We can use this variable anyways for the session since it will not be reset until a page refresh
    window.nexfiledetail = YAHOO.util.Cookie.get("nexfiledetail");
    window.nexfilefolders = YAHOO.util.Cookie.get("nexfilefolders");

    var el = document.getElementById('nexfile');
    if (pagewidth == 0) {
        layoutPageWidth = Dom.get('nexfile').offsetWidth - 10;   //Width of the outer element
    } else {
        layoutPageWidth = pagewidth;
    }
    YAHOO.log('width: ' + layoutPageWidth);
    var layout = new YAHOO.widget.Layout(el,{
        height: Dom.getClientHeight(), //Height of the viewport
        width: layoutPageWidth, //Width of the outer element
        minHeight: 150, //So it doesn't get too small
        units: [
            { position: 'left',  width: leftcolwidth, resize: true, body: 'nexfile_sidecol', scroll: true, animate: true },
            { position: 'center', body: 'nexfile_centercol', gutter: '0px 4px 12px 0px', scroll: true }
        ]
    });

    if (pagewidth == 0)
        layout.on('beforeResize', function() {
            Dom.setStyle('nexfile', 'height', Dom.getClientHeight() + 'px');
            Dom.setStyle('nexfile', 'width', Dom.getClientWidth()-25 + 'px');
        });
    layout.render();
    updateAjaxStatus('Loading Workspace ...')

    if (pagewidth == 0)
        //Handle the resizing of the window
        Event.on(window, 'resize', layout.resize, layout, true);

    // Need to add this class to the body tag for the YUI skin
    var objects = document.getElementsByTagName("body");
    var bodytag = objects[0];
    bodytag.className = bodytag.className + ' yui-skin-sam';

    try {
        Dom.setStyle('header-welcomedate', 'display', 'none');
    } catch(e) {}

    var oSearchButton = new YAHOO.widget.Button("searchbutton");

    try {
        Dom.setStyle('newfolderlink','display','');
        var oLinkButton1 = new YAHOO.widget.Button("newfolderlink");
        Event.addListener("newfolderlink", "click", showAddCategoryPanel, YAHOO.container.newfolderdialog, true);
    } catch(e) {}

    // User may not have upload rights
    try {
        YAHOO.widget.Uploader.SWFURL = siteurl + "/javascript/yui/uploader/assets/uploader.swf";
        uploader = new YAHOO.widget.Uploader( "uploaderUI", imgset + '/XPButtonUploadText_61x22.png' );
        uploader.addListener('contentReady', handleContentReady);
        uploader.addListener('fileSelect',onFileSelect)
        uploader.addListener('uploadProgress',onUploadProgress);
        uploader.addListener('uploadComplete',onUploadComplete);
        uploader.addListener('uploadCompleteData',onUploadResponse);
        document.getElementById('btnNewFileSubmit').disabled=true;
        Dom.setStyle('newfilelink','display','');
       } catch(e) { alert('failed to load uploader') }


    <!-- File Tags Panel -->
    YAHOO.container.tagspanel = new YAHOO.widget.Panel("tagspanel",
                                            { width:"320px",
                                              visible:false,
                                              zindex:2000,
                                              constraintoviewport:true
                                            });
    YAHOO.container.tagspanel.render();

    <!-- File Details Dialogs -->
    YAHOO.container.filedetails = new YAHOO.widget.Panel("filedetails",
                                            { width : "670px",
                                              fixedcenter : true,
                                              visible : false,
                                              zindex: 2000,
                                              modal: true,
                                              effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration: .5},
                                              constraintoviewport : true
                                             } );
    YAHOO.container.filedetails.render();

    // Add and event on the close icon to hide the menu
    var closeEl = Dom.getElementsByClassName("container-close", null, 'filedetails')[0];
    Event.on(closeEl, 'click', function(e){ YAHOO.container.menuBar.cfg.setProperty("visible",false); });

    <!-- Folder Perms Panel -->
    YAHOO.container.folderperms = new YAHOO.widget.Panel("folderperms",
                                            { width : "750px",
                                              fixedcenter : true,
                                              visible : false,
                                              zindex: 2000,
                                              modal: true,
                                              constraintoviewport : true
                                             } );
    YAHOO.container.folderperms.render();


    // Setup the New Folder Dialog
    Dom.setStyle('newfolderdialog', 'display', 'block');
    YAHOO.container.newfolderdialog = new YAHOO.widget.Panel("newfolderdialog",
                                            { width : "450px",
                                              visible : false,
                                              fixedcenter : true,
                                              modal: true,
                                              constraintoviewport : true
                                             } );

    YAHOO.container.newfolderdialog.render();

    // Setup the New File Dialog
    Dom.setStyle('newfiledialog', 'display', 'block');
    YAHOO.container.newfiledialog = new YAHOO.widget.Panel("newfiledialog",
                                            { width : "520px",
                                              visible : false,
                                              fixedcenter : true,
                                              modal: true,
                                              constraintoviewport : true
                                             } );

    YAHOO.container.newfiledialog.render();

    // Setup the Batch File Move Dialog
    Dom.setStyle('movebatchfilesdialog', 'display', 'block');
    YAHOO.container.batchfilemovedialog = new YAHOO.widget.Panel("movebatchfilesdialog",
                                            { width : "450px",
                                              visible : false,
                                              fixedcenter : true,
                                              modal: true,
                                              constraintoviewport : true
                                             } );

    YAHOO.container.batchfilemovedialog.render();

    // Setup the Incoming Queue File Move Dialog
    Dom.setStyle('moveQueueFileDialog', 'display', 'block');
    YAHOO.container.moveQueueFileDialog = new YAHOO.widget.Panel("moveQueueFileDialog",
                                            { width : "450px",
                                              visible : false,
                                              fixedcenter : true,
                                              modal: true,
                                              constraintoviewport : true
                                             } );

    YAHOO.container.moveQueueFileDialog.render();

    // Setup the Broadcast Notification Dialog
    Dom.setStyle('broadcastDialog', 'display', 'block');
    YAHOO.container.broadcastDialog = new YAHOO.widget.Panel("broadcastDialog",
                                            { width : "450px",
                                              visible : false,
                                              fixedcenter : true,
                                              modal: true,
                                              constraintoviewport : true
                                             } );

    YAHOO.container.broadcastDialog.render();

    try {
        if (nexfiledetail == "expanded") {
            var elements = Dom.getElementsByClassName('filedesc','div', 'filelisting_container');
            for (var i = 0; i < elements.length; i++) {
                Dom.setStyle(elements[i],'display','block');
                var elm = Dom.getFirstChild('showhidedetail');
                elm.innerHTML='Hide File Details';
            }
            var elements = Dom.getElementsByClassName('emptyfolder','div', 'filelisting_container');
            for (var i = 0; i < elements.length; i++) {
                Dom.setStyle(elements[i],'display','none');
            }
        }
    } catch(e) {}

    try {
        Dom.setStyle('newfolderlink','display','');
        var oLinkButton1 = new YAHOO.widget.Button("newfolderlink");
        Event.addListener("newfolderlink", "click", showAddCategoryPanel, YAHOO.container.newfolderdialog, true);
    } catch(e) {}

    try {
        Dom.setStyle('contactslink','display','');
        var oLinkButton2 = new YAHOO.widget.Button("contactslink");
        //oLinkButton2.set('disabled',false);
        Event.addListener("contactslink", "click", function(e) { YAHOO.nexfile.showReport('contacts');} );
    } catch(e) {}

    try {
        Dom.setStyle('folderadmin','display','');
        var oLinkButton3 = new YAHOO.widget.Button("folderadmin");
        Event.addListener("folderadmin", "click", function(e) { document.location = actionurl_dir + '/admin.php?op=folderadmin&wkspace=' + workspace } );
        Event.addListener("btnNewFolderCancel", "click", YAHOO.container.newfolderdialog.hide, YAHOO.container.newfolderdialog, true);
    } catch(e) {}

    Event.addListener("filedetails_cancel", "click", hideFileDetailsPanel);
    Event.addListener("showsearchtags", "click", YAHOO.container.tagspanel.show, YAHOO.container.tagspanel, true);
    Event.addListener("searchbutton", "click", makeAJAXSearch);
    Event.addListener("cancelalert", "click", closeAlert);
    Event.addListener("btnBroadcastSubmit", "click", makeAJAXBroadcastNotification);
    Event.addListener("btnBroadcastCancel", "click", YAHOO.container.broadcastDialog.hide, YAHOO.container.broadcastDialog, true);

    // Setup event handlers for the flag favorite feature - hooked to the Star Image
    Event.addListener('filelisting_container', 'click', function(e){
        var target = Event.getTarget(e);
        if(target.tagName.toUpperCase() === 'IMG' && Dom.hasClass(target,'togglefavorite')) {
            Event.preventDefault(e);
            var link = target.parentNode;
            var params = link.getAttribute('href');
            var id = parseURL(params,'id');
            makeAJAXToggleFavorite(id);
        }
    });

    // Setup event handler on each delete permission link in the folder perms panel
    Event.on('folderperms', 'click', function(e){
        var target = Event.getTarget(e);
        var tagname = target.tagName.toUpperCase();
        if(tagname === 'A' && Dom.hasClass(target,'deleteperm')) {
            Event.preventDefault(e);
            var params = target.getAttribute('href');
            var id = parseURL(params,'accid');
            var surl = ajax_post_handler_url + '?op=delfolderperms&id=' + id;
            var callback = {
                success: function(o) {
                    var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                    var oResults = eval('(' + json + ')');
                    if (oResults.retcode == 200) {
                        Dom.get('folderperms_content').innerHTML = oResults.html;
                        YAHOO.container.folderperms.cfg.setProperty("visible",true);
                        Event.addListener("folderperms_cancel", "click", YAHOO.container.folderperms.hide, YAHOO.container.folderperms, true);
                    } else {
                        alert('Error retrieving folder permissions');
                    }
                    updateAjaxStatus();
                },
                failure: function(o) {
                    alert('AJAX Update Error: ' + o.status);
                },
                argument: {},
                timeout:55000
            }
            updateAjaxStatus('Updating Permissions ...');
            YAHOO.util.Connect.asyncRequest('POST', surl, callback);
        }
    });

    // Setup to work with the file details link
    Event.on('filelisting_container', 'click', function(e){
        var target = Event.getTarget(e);
        var tagname = target.tagName.toUpperCase();
        if(tagname === 'A') {
            if (Dom.hasClass(target,'filedetailsdialog')) {
                Event.preventDefault(e);
                var params = target.getAttribute('href');
                var id = parseURL(params,'fid');
                makeAJAXLoadFileDetails(id);
            } else if (Dom.hasClass(target,'deleteincoming')) {
                Event.preventDefault(e);
                var params = target.getAttribute('href');
                var id = parseURL(params,'fid');
                makeAJAXDeleteQueueFile(id);
            } else if (Dom.hasClass(target,'moveincoming')) {
                Event.preventDefault(e);
                var params = target.getAttribute('href');
                var id = parseURL(params,'fid');
                showMoveQueueFile(id);
            } else if (Dom.hasClass(target,'morefolderdata')) {
                Event.preventDefault(e);
                var params = target.getAttribute('href');
                var cid   = parseURL(params,'cid');
                var fid   = parseURL(params,'fid');
                var fnum  = parseURL(params,'fnum');
                var level = parseURL(params,'level');
                YAHOO.nexfile.getmorefolderdataRequest(cid,fid,fnum,level);
            }

        } else if (tagname === 'IMG') {
            var link = target.parentNode;
            if (Dom.hasClass(link,'filedetailsdialog')) {
                Event.preventDefault(e);
                var params = link.getAttribute('href');
                var id = parseURL(params,'fid');
                makeAJAXLoadFileDetails(id);
            }
        }
    });

    // Setup event handler on each tag words in the tag cloud
    Event.on('tagspanel', 'click', function(e){
        var target = Event.getTarget(e);
        var tagname = target.tagName.toUpperCase();
        if(tagname === 'A' && Dom.hasClass(target,'tagcloudword')) {
            Event.preventDefault(e);
            var tagword = target.innerHTML;
            if (document.fsearch.tags.value.length > 0)
                tagword = tagword + ',' + document.fsearch.tags.value;
            makeAJAXSearchTags(tagword);
        }
    });

    /*  Instantiate a MenuBar:  */
    YAHOO.container.menuBar = new YAHOO.widget.MenuBar("filedetailsmenubar");
    /*  Call the "render" method with no arguments since the  markup for this MenuBar already exists in the page.   */
    YAHOO.container.menuBar.render();
    YAHOO.container.menuBar.cfg.setProperty("visible",false);

    Dom.setStyle('nexfile', 'visibility', 'visible');
    Dom.setStyle('tagspanel', 'display', 'block');

    YAHOO.nexfile.showfiles();

    // Browser may have cached selected items and checked checkbox'es but verify and reset if need
    enable_multiaction(document.frmtoolbar.checkeditems.value);


    /* Auto-Complete feature for tags field on the New File form */
    YAHOO.nexfile.newFileAutoComplete = function() {
        // Use an XHRDataSource
        var oDS = new YAHOO.util.XHRDataSource(ajax_post_handler_url);
        // Set the responseType
        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;

        oDS.responseSchema = {
            recordDelim: "\n",
            fieldDelim: "\t"
        };

        // Enable caching
        oDS.maxCacheEntries = 5;

        try {
            var tstore = document.frmNewFile.tagstore;
        } catch(e) {}

        // Instantiate the AutoComplete
        var oAC = new YAHOO.widget.AutoComplete("newfile_tags", "newfile_autocomplete", oDS);
        oAC.queryQuestionMark = false;
        oAC.autoHighlight = true;
        oAC.generateRequest = function(sQuery) {
            return "?op=autocompletetag&query=" + sQuery;
        };

        // Following two methods added to handle multiple tags
        // Allowing you to query still on new tags when there are already tags in the input text box
        // Needed to handle allowing you to delete or clearing tags in the text box as well
        oAC.doBeforeExpandContainer = function( elTextbox ) {
            var tags = elTextbox.value.replace(/^\s+|\s+$/g, '');
            if (tags.length > 1) {
                var atags = tags.split(',');
                var stags = '';
                for (var i = 0; i < atags.length-1; i++) {
                    stags = stags + ',' + atags[i];
                }
                // After trimming and re-building tags string in case user has removed or cleared
                // Save the tag(s) to be used after user selectes a new tag
                tstore.value = stags;
            }
            return true;
        };

        // OnSelect of tag, add it to the existing saved tags
        var itemSelectHandler = function(sType, aArgs) {
            tstore.value = tstore.value + ',' +  aArgs[2];
            tstore.value = tstore.value.replace(/^,*/g, '');
            Dom.get('newfile_tags').value = tstore.value;
        };

        //subscribe your handler to the event, assuming
        //you have an AutoComplete instance oAC:
        try {
            oAC.itemSelectEvent.subscribe(itemSelectHandler);
        } catch(e) {}

        return {
            oDS: oDS,
            oAC: oAC
        };
    }();


   /* Auto-Complete feature for tags field on the Edit File form */
    YAHOO.nexfile.editFileAutoComplete = function() {
        // Use an XHRDataSource
        var oDS = new YAHOO.util.XHRDataSource(ajax_post_handler_url);
        // Set the responseType
        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;

        oDS.responseSchema = {
            recordDelim: "\n",
            fieldDelim: "\t"
        };

        // Enable caching
        oDS.maxCacheEntries = 5;

        try {
            var tstore = document.frmFileDetails.tagstore;
        } catch(e) {}

        // Instantiate the AutoComplete
        var oAC = new YAHOO.widget.AutoComplete("editfile_tags", "editfile_autocomplete", oDS);
        oAC.queryQuestionMark = false;
        oAC.autoHighlight = true;
        oAC.generateRequest = function(sQuery) {
            return "?op=autocompletetag&query=" + sQuery;
        };

        // Following two methods added to handle multiple tags
        // Allowing you to query still on new tags when there are already tags in the input text box
        // Needed to handle allowing you to delete or clearing tags in the text box as well
        oAC.doBeforeExpandContainer = function( elTextbox ) {
            var tags = elTextbox.value.replace(/^\s+|\s+$/g, '');
            if (tags.length > 1) {
                var atags = tags.split(',');
                var stags = '';
                for (var i = 0; i < atags.length-1; i++) {
                    stags = stags + ',' + atags[i];
                }
                // After trimming and re-building tags string in case user has removed or cleared
                // Save the tag(s) to be used after user selectes a new tag
                tstore.value = stags;
            }
            return true;
        };

        // OnSelect of tag, add it to the existing saved tags
        var itemSelectHandler = function(sType, aArgs) {
            tstore.value = tstore.value + ',' +  aArgs[2];
            tstore.value = tstore.value.replace(/^,*/g, '');
            Dom.get('editfile_tags').value = tstore.value;
        };

        //subscribe your handler to the event, assuming
        //you have an AutoComplete instance oAC:
        try {
            oAC.itemSelectEvent.subscribe(itemSelectHandler);
        } catch(e) {}

        return {
            oDS: oDS,
            oAC: oAC
        };
    }();



};