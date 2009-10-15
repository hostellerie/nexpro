/*
nexTime plugin Javascript library

*/


var roundval=0.25;
var lasttshtml='';
var approvaltimer=0;
var savetimer;
var v1, v4, v5, v6, v7, ve3,ve4,ve6,ve7 ;
function init_index_page(){
	v1=new nexYUICal;
    v1.init('cal1','start_date');
    v1.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    if(!enableautoenddate){
        v2=new nexYUICal;
        v2.init('cal2','end_date');
        v2.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    }

    v4=new nexYUICal;
    v4.init('startdate2','start_date2');
    v5=new nexYUICal;
    v5.init('enddate2','end_date2');

    v4.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    v5.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd

    if(enableautoenddate){
        v1.setAfterFunction('fetchSundayToSunday("start_date","span_dummy1","span_dummy1","enter_my_timesheet","start_date","end_date")');
    }else{
        v2.setAfterFunction('turnOnButton("enter_my_timesheet")');
    }
    
    YAHOO.util.Event.onDOMReady(reportbyemppanel);
    YAHOO.util.Event.onDOMReady(reportbytaskpanel);
    YAHOO.util.Event.onDOMReady(reportbyprojectpanel);
    YAHOO.util.Event.onDOMReady(reportbyfreeformpanel);

    

    v3=new nexYUICal;
    v3.init('cal3','byEmpsdate');
    v3.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve3=new nexYUICal;
    ve3.init('cale3','byEmpedate');
    ve3.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve3.setAfterFunction('turnOnButton("byEmpGoButton")');
    
    v4=new nexYUICal;
    v4.init('cal4','byTasksdate');
    v4.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    
    ve4=new nexYUICal;
    ve4.init('cale4','byTaskedate');
    ve4.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve4.setAfterFunction('turnOnButton("byTaskGoButton")');
    
    v6=new nexYUICal;
    v6.init('cal5','byProjectsdate');
    v6.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve6=new nexYUICal;
    ve6.init('cale5','byProjectedate');
    ve6.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve6.setAfterFunction('turnOnButton("byProjectGoButton")');
    
    
    v7=new nexYUICal;
    v7.init('cal6','byFreeFormsdate');
    v7.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve7=new nexYUICal;
    ve7.init('cale6','byFreeFormedate');
    ve7.setFormat (3,2,1,'/');  //day, month, year... this will create a dateoutput that is YYYY/mm/dd
    ve7.setAfterFunction('turnOnButton("byFreeFormGoButton")');
	
}

function init_approval_page(){
	YAHOO.util.Event.onDOMReady(delmsgpanel);
	YAHOO.util.Event.onDOMReady(rejectpanel);
	YAHOO.util.Event.onDOMReady(cmtpanel);
	YAHOO.util.Event.onDOMReady(errmsgpanel);
	YAHOO.util.Event.onDOMReady(savepanel);
}

function init_entry_page(){    
	YAHOO.util.Event.onDOMReady(delmsgpanel);
	YAHOO.util.Event.onDOMReady(errmsgpanel);
	YAHOO.util.Event.onDOMReady(cmtpanel);
	YAHOO.util.Event.onDOMReady(rejectionreasonpanel);
	YAHOO.util.Event.onDOMReady(savepanel);
}

function reportbyemppanel(){
    YAHOO.namespace("nextide.container");
    YAHOO.nextide.container.panel20 = new YAHOO.widget.Panel("reportbyemployeepanel", { width:"600px", visible:false, constraintoviewport:true, x:300, y:600, modal:true } );
    YAHOO.nextide.container.panel20.render();
    YAHOO.nextide.container.panel20.center();
    try{
        document.getElementById('reportByEmployeeButton').style.display='';
    }catch(e){}
}

function reportbytaskpanel(){
    YAHOO.namespace("nextide.container");
    YAHOO.nextide.container.panel30 = new YAHOO.widget.Panel("reportbytaskpanel", { width:"600px", visible:false, constraintoviewport:true, x:300, y:600, modal:true } );
    YAHOO.nextide.container.panel30.render();
    YAHOO.nextide.container.panel30.center();
    try{
        document.getElementById('reportByTaskButton').style.display='';
    }catch(e){}
}

function reportbyprojectpanel(){
    YAHOO.namespace("nextide.container");
    YAHOO.nextide.container.panel50 = new YAHOO.widget.Panel("reportbyprojectpanel", { width:"600px", visible:false, constraintoviewport:true, x:300, y:600, modal:true } );
    YAHOO.nextide.container.panel50.render();
    YAHOO.nextide.container.panel50.center();
    try{
        document.getElementById('reportByProjectButton').style.display='';
    }catch(e){}
}

function reportbyfreeformpanel(){
    YAHOO.namespace("nextide.container");
    YAHOO.nextide.container.panel60 = new YAHOO.widget.Panel("reportbyfreeformpanel", { width:"600px", visible:false, constraintoviewport:true, x:300, y:600, modal:true } );
    YAHOO.nextide.container.panel60.render();
    YAHOO.nextide.container.panel60.center();
    try{
        document.getElementById('reportByFreeFormButton').style.display='';
        document.getElementById('fetching_reports_message').style.display='none';
    }catch(e){}
}


function turnOnButton(btn){
document.getElementById(btn).style.display='';
}
