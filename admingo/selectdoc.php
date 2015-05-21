<HTML>
<HEAD>
<TITLE>Выберите документ </TITLE>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<STYLE TYPE="text/css">
 BODY   {margin-left:10; font-family:Verdana; font-size:12; background:menu}
 BUTTON {width:5em}
 TABLE  {font-family:Arial; font-size:12px}
 P      {text-align:center}
 iframe {border: 1px solid;HEIGHT: 500px; WIDTH: 500px;}
</STYLE>
<SCRIPT LANGUAGE=JavaScript>
function IsDigit()
{
  return ((event.keyCode >= 48) && (event.keyCode <= 57))
}
</SCRIPT>

<SCRIPT LANGUAGE=JavaScript FOR=window EVENT=onload>
  for ( elem in window.dialogArguments )
  {
    switch( elem )
    {
    case "NumAlign":
      NumAlign.value = window.dialogArguments["NumAlign"];
      break;
    case "NumVSpace":
      NumVSpace.value = window.dialogArguments["NumVSpace"];
      break;
    case "NumSrc":
      NumSrc.value = window.dialogArguments["NumSrc"];
      break;
    case "FileName":
      NumAlt.value = window.dialogArguments["FileName"];
      break;	  
    }
  }
</SCRIPT>

<SCRIPT LANGUAGE=JavaScript FOR=Ok EVENT=onclick>
alert('вставка в FCKeditor');
  var arr = new Array();
  arr["NumSrc"] = NumSrc.value;
  arr["FileName"] = FileName.value;
  arr["ParentSectionID"] = ParentSectionID.value;
  arr["rqpath"] = rqpath.value;
  window.returnValue = arr;

  //вставка в FCKeditor  
  if(window.opener){  
    if(window.opener.document.all['cke_74_textInput']) {
        if (rqpath.value.length > 0)        
          window.opener.document.all['cke_74_textInput'].value=rqpath.value;
        else
          window.opener.document.all['cke_74_textInput'].value='/?id='+NumSrc.value+'&sid='+ParentSectionID.value;    
    }
    else if (top.window.opener.document.all['adddoc']) {
          top.window.opener.adddoc(document.all['NumSrc'].value, document.all['FileName'].value, document.all['doctype'].value);
    }    
    /*if(window.opener.document.all['txtUrl'])
    {      
      if(window.opener.document.all['txtLnkUrl']) {
        if (rqpath.value.length > 0)        
          window.opener.document.all['txtLnkUrl'].value=location.host+rqpath.value;
        else
          window.opener.document.all['txtLnkUrl'].value=location.host+'/?id='+NumSrc.value+'&sid='+ParentSectionID.value;
      }
      else {
        if (rqpath.value.length > 0)        
          window.opener.document.all['txtUrl'].value=location.host+rqpath.value;
        else       
          window.opener.document.all['txtUrl'].value=location.host+'/?id='+NumSrc.value+'&sid='+ParentSectionID.value;
      }       
    }
    else if (top.window.opener.document.all['adddoc']) {
          top.window.opener.adddoc(document.all['NumSrc'].value, document.all['FileName'].value, document.all['doctype'].value);
    }*/
  }

  window.close();
</SCRIPT>

<script language="JavaScript">
function fu()
{
	document.all.SectionID.value = window.windoc.i;
}

function UpGO()
{
	LinkToFolderUp = ParamByName(location.href,'SECTIONID');
	windoc.location.href = 'listdocs.php?SECTIONID=' + windoc.LinkToFolderUp;
}

function ParamByName(s,param)
	{	
		if ( (s.length>0)&&(param.length>0) )
		{

			var su = s.toUpperCase() + '&';
		var sd = '&' + param.toUpperCase() + '=';
		var sd2 = '?' + param.toUpperCase() + '=';
		var p = -1;
		var p2 = -1;
		var result = '';
		
		 
     		
   			p = su.indexOf(sd,0);
   			if (p == -1)
			{
				p = su.indexOf(sd2,0);
			}

			if (p != -1)
			{
				p2 = su.indexOf('&',p+2);
				result = s.substring(p + sd.length, p2);
			}
		}
		else
		{
			alert('Пустые значения параметров в функции javascript:ParamByName()');
		}
	
		//alert('ParamByName ' + param + '=' + result);
		return result;
	
	}
	
  function getUrlParam(name){       
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var tmpURL = window.location.href;
    var results = regex.exec( tmpURL );
    if( results == null )
      return "";
    else     
      return results[1];     
  } 	
	
	function subm(){	 	
	  if (navigator.appName=='Netscape' || navigator.appName=='Opera') {            	      
	    if (top.window.opener) {
	      var dialog = window.opener.CKEDITOR.dialog.getCurrent();
	      var funcNum = getUrlParam('CKEditorFuncNum'); 
	      if (funcNum.length) {                                          
          var value = '/?id='+document.all['NumSrc'].value+'&sid='+document.all['ParentSectionID'].value;
          window.opener.CKEDITOR.tools.callFunction(funcNum, value);                     
          window.close();
        }
        else if (top.window.opener.document.all['adddoc']) {
          top.window.opener.adddoc(document.all['NumSrc'].value, document.all['FileName'].value, document.all['doctype'].value);
          window.close();
        }
      } else {          
        window.submit();          
     }
    }
  }
</script>


</HEAD>

<BODY bgColor="#FFFFFF">

<TABLE cellpadding="1" width="100%" border="0">
<tr>
	<td colspan="2">
	<img src="images/up.gif" border="0" alt="Вверх" onclick="UpGO();" style="cursor:hand;">
	</td>
</tr>
<tr>
	<td colspan="2">
	<script>
	current_url=document.location.href;	

	var gp_sectionid = "-1";	
	gp_sectionid=ParamByName(current_url,'SECTIONID');	
	
	document.write('<iframe name="windoc" frameborder="1" src="listdocs.php?SECTIONID=' + gp_sectionid + '" marginheight="0" marginwidth="0" width="500" height="500"></iframe>');

	</script>
	</td>
</tr>
<TR>
  <TD>Документ:</TD>
  <TD><INPUT TYPE=text SIZE=55 NAME=FileName id=FileName><BUTTON ID=Ok ONCLICK="subm();" style="margin-left:10px;">OK</BUTTON></TD>
</TR>
</TABLE>
<table CELLSPACING=0 cellpadding="0" width="100%" border="0">
<tr>
	<td>
	</td>
	<td align="center" valign="baseline" style="padding-top:10px;">	
		<BUTTON ONCLICK="window.close();" style="margin-left:15px;">Отмена</BUTTON>
		<INPUT TYPE=hidden SIZE=55 NAME=SectionID id=SectionID>
		<INPUT TYPE=hidden SIZE=55 NAME=ParentSectionID id=ParentSectionID>
		<INPUT TYPE=hidden SIZE=55 NAME=NumSrc id=NumSrc>
		<INPUT TYPE=hidden SIZE=55 NAME=doctype id=doctype>
		<INPUT TYPE=hidden SIZE=55 NAME=rqpath id=rqpath>
	</td>
</tr>
</table>

</BODY>
</HTML>

