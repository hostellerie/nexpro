// +---------------------------------------------------------------------------+
// | Nexpro                                                                    |
// +---------------------------------------------------------------------------+
// | Commmon javascript functions                                              |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2005,2006 by the following authors:                         |
// |                                                                           |
// | Blaine Lang - blaine.lang@nextide.ca                                      |
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



// Sets a variable for the users current width and height of the main display area
function setMainDisplayHW() {
    obj = document.getElementById('mainDisplayArea');

    // Method1 to get width of the main plugin display area
    mainDisplayHeight = obj.offsetHeight;
    // mainDisplayWidth =  obj.offsetWidth ;

    // Code example to detect horizontal scrollbars are being shown */
    if (document.body.clientWidth < document.body.scrollWidth) {
         //alert("horizontal scrolling - " + obj.clientWidth + ':' + document.body.clientWidth + ':' + document.body.scrollWidth);
    }

    // Method2 to get width of the main plugin display area
    // Using clientWidth appears to be the best
    if (document.body.clientWidth < obj.clientWidth) {
        //alert('1: ' + document.body.clientWidth + ':' + obj.clientWidth + ':' + document.body.scrollWidth);
        mainDisplayWidth = document.body.clientWidth - 150;
    } else {
        //alert('2: ' + document.body.clientWidth + ':' + obj.clientWidth + ':' + document.body.scrollWidth + ':' + obj.offsetWidth );
        /* Subtract the number of pixels that are over flowing */
        mainDisplayWidth = obj.clientWidth - (document.body.scrollWidth - document.body.clientWidth);
    }

    cookievalue = mainDisplayHeight + ',' + mainDisplayWidth;
    setMyCookie('windowhw',cookievalue,'','/');
}


function setMyCookie (name,value,expires,path,theDomain,secure) {
  var theCookie = name + "=" + escape (value) +
  ((expires)    ? "; expires=" + expires.toGMTString() : "") +
  ((path)       ? "; path="    + path   : "") +
  ((theDomain)  ? "; domain="  + theDomain : "") +
  ((secure)     ? "; secure"            : "");
  document.cookie = theCookie;
}


function getCookie (name) {
    var dc = document.cookie;
    var cname = name + "=";
    var clen = dc.length;
    var cbegin = 0;

    while (cbegin < clen) {
        var vbegin = cbegin + cname.length;
        if (dc.substring(cbegin, vbegin) == cname) {
            var vend = dc.indexOf (";", vbegin);
            if (vend == -1) vend = clen;
                return unescape(dc.substring(vbegin, vend));
        }
        cbegin = dc.indexOf(" ", cbegin) + 1;
        if (cbegin== 0) break;
    }
    return null;
}


function activatetab(tabid,starttab,endtab) {

    // Clear the alert or status message in the plugin header
    var obj = document.getElementById('pluginerrmsg');
    if (obj) {
        obj.style.display='none';
    }
    for (var i = starttab; i <= endtab; i++ ) {
        // Clear tabs and the tab panel div
        if (document.getElementById('etab' + i))
            document.getElementById('etab' + i).className = '';
        if (document.getElementById('etab' + i + '_panel'))
            document.getElementById('etab' + i + '_panel').style.display = 'none';
    }
    // Select the desirec active tab and panel
    if (document.getElementById(tabid))
        document.getElementById(tabid).className = 'selected';
    if (document.getElementById(tabid + '_panel'))
        document.getElementById(tabid + '_panel').style.display = '';
}


function hideElement(elm) {

    var obj=document.getElementById(elm);
    if (obj) obj.style.display='none';
}

function showElement(elm) {

    var obj=document.getElementById(elm);
    if (obj) obj.style.display='';
}

function toggleElement(elm) {

    var obj = document.getElementById(elm);
    if(obj) {
        if(obj.style.display == 'none') {
            obj.style.display = '';
        } else {
            obj.style.display = 'none';
        }
    }
}

function toggleElements(elm1,elm2) {
    var obj1 = document.getElementById(elm1);
    var obj2 = document.getElementById(elm2);
    if (obj1 && obj2) {
        if(obj1.style.display == 'none') {
            obj1.style.display = '';
            obj2.style.display = 'none';
        } else {
            obj1.style.display = 'none';
            obj2.style.display = '';
        }
    }
}

// Used with a link to show/hide another element like a div
// Link label will be toggled as well
function linkToggleElement(link,elm,labelshow,labelhide) {
    var obj = document.getElementById(elm);
    if(obj) {
        if(obj.style.display == 'none') {
            obj.style.display = '';
            link.innerHTML = labelhide;
        } else {
            obj.style.display = 'none';
            link.innerHTML = labelshow;
        }
    }
}


function parseURL(url,parm) {
    parm = parm.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+parm+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    if( results == null )
        return "";
    else
        return results[1];
}

/* Object Literal function to calculate execution time
 * Blaine Lang
 * 1: Add this at the start of your JS code to time
 *    >  timeDiff.setStartTime();
 * 2: Call timeDiff.getDiff() to return elaspsed time in ms
*/
var timeDiff  =  {
    setStartTime:function (){
        d = new Date();
        time  = d.getTime();
    },

    getDiff:function (){
        d = new Date();
        return (d.getTime() - time);
    }
}


function parseXML(root,tagname) {

    var pnode = root.getElementsByTagName(tagname)[0];
    retval = '';
    for (var i = 0; i < pnode.childNodes.length; i++) {
        retval += pnode.childNodes[i].nodeValue;
    }
    return retval;
}


/* Pass in source array and item to remove - returns new array */
function arrayRemoveItem(sourceArr, removeItem) {
    var count = 0;
    var newArr = new Array();
    for(i=0; i<sourceArr.length; i++)
       if(sourceArr[i] != removeItem)
          newArr[count++] = sourceArr[i];
    return newArr;
}


function select_innerHTML(obj,innerHTML){
/******
* select_innerHTML - correction for a bug using InnerHTML to replace the Select Options in IE
* Veja o problema em: http://support.microsoft.com/default.aspx?scid=kb;en-us;276228
* Vers�o: 2.1 - 04/09/2007
* Autor: Micox - N�iron Jos� C. Guimar�es - micoxjcg@yahoo.com.br
* @obj(tipo HTMLobject): o select a ser alterado
* @innerHTML(tipo string): o novo valor do innerHTML
*******/
    obj.innerHTML = "";
    var selTemp = document.createElement("micoxselect");
    var opt;
    selTemp.id="micoxselect1";
    document.body.appendChild(selTemp);
    selTemp = document.getElementById("micoxselect1");
    selTemp.style.display="none";
    if(innerHTML.toLowerCase().indexOf("<option")<0){//se n�o � option eu converto
        innerHTML = "<option>" + innerHTML + "</option>";
    }
    innerHTML = innerHTML.toLowerCase().replace(/<option/g,"<span").replace(/<\/option/g,"</span");
    selTemp.innerHTML = innerHTML;

    for(var i=0;i<selTemp.childNodes.length;i++){
        var spantemp = selTemp.childNodes[i];
        if(spantemp.tagName){
            opt = document.createElement("OPTION");

   if(document.all){ //IE
    obj.add(opt);
   } else {
    obj.appendChild(opt);
   }

   //getting attributes
   for(var j=0; j<spantemp.attributes.length ; j++){
    var attrName = spantemp.attributes[j].nodeName;
    var attrVal = spantemp.attributes[j].nodeValue;
    if(attrVal){
     try{
      opt.setAttribute(attrName,attrVal);
      opt.setAttributeNode(spantemp.attributes[j].cloneNode(true));
     }catch(e){}
    }
   }
   //getting styles
   if(spantemp.style){
    for(var y in spantemp.style){
     try{opt.style[y] = spantemp.style[y];}catch(e){}
    }
   }
   //value and text
   opt.value = spantemp.getAttribute("value");
   opt.text = spantemp.innerHTML;
   //IE
   opt.selected = spantemp.getAttribute('selected');
   opt.className = spantemp.className;
  }
 }
 document.body.removeChild(selTemp);
 selTemp = null;
}