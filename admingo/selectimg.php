<?php
  require_once "config.php";	
	require_once "db_connect.php";
  require_once "login.php"; 
?>
<HTML>
<HEAD>
<TITLE> Выберите картинку </TITLE>      
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
<STYLE TYPE="text/css">
	 BODY   {margin-left:10; font-family:Verdana; font-size:12; background:menu}
	 BUTTON {width:5em}
	 TABLE  {font-family:Arial; font-size:12px}
	 P      {text-align:center}
	 iframe {border: 1px solid;HEIGHT: 500px; WIDTH: 500px;}
</STYLE>

<SCRIPT LANGUAGE=JavaScript>

  var basePath = '<?php echo $config->upfolder; ?>'; 

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
	    case "NumSrc":
	      NumSrc.value = window.dialogArguments["NumSrc"];
	      break;    
	    case "ImgPath":
	      ImgPath.value = window.dialogArguments["ImgPath"];
	      break;    
	    case "ImgPreview":
	      ImgPreview.value = window.dialogArguments["ImgPreview"];
	      break;	      
	    case "PREVIEW":
	      PREVIEW.value = window.dialogArguments["PREVIEW"];
	      break;
	    }
	  }
</SCRIPT>

<SCRIPT LANGUAGE=JavaScript FOR=Ok EVENT=onclick>
    var WorkURL = 'http://'+location.host+'/?';
    var dialog = window.opener.CKEDITOR.dialog.getCurrent(); // определяем текущий диалог  - нашел здесь - http://cksource.com/forums/viewtopic.php?f=11&t=16161&start=0  
    var funcNum = getUrlParam('CKEditorFuncNum'); // получаем параметр из ссылки диалога  
    var imageUrl = WorkURL+"id="+document.getElementById('NumSrc').value+""; // ссылка на картинку
    if (document.getElementById('PREVIEW').options[document.getElementById('PREVIEW').selectedIndex].value=='TRUE'){
      var imageUrl = WorkURL+"id="+document.getElementById('NumSrc').value+"&preview=true"; // вставляем превью  
    }
    
    if (document.getElementById('PREVIEW').options[document.getElementById('PREVIEW').selectedIndex].value=='LINK'){
      var imageUrl = WorkURL+"id="+document.getElementById('NumSrc').value+"&preview=true"; // вставляем превью со ссылкой на большую    
      var imageLnkUrl = WorkURL+"id="+document.getElementById('NumSrc').value+"";             
    } else { 
      var imageLnkUrl = "";
    }           
  
    var imageAlt = document.getElementById('FileName').value; 
    var docIdField = dialog.getContentElement( 'info', 'txtAlt' );
    docIdField.setValue( imageAlt );
    
    var docIdField = dialog.getContentElement( 'Link', 'txtUrl' );
    docIdField.setValue( imageLnkUrl ); 
    
    window.opener.CKEDITOR.tools.callFunction(funcNum, imageUrl);      
    window.close(); // закрываем диалог		  
</SCRIPT>
		
<script language="JavaScript">
		function fu()
		{
			document.all.SectionID.value = window.windoc.i;
		}

		function Create_NEWDOC()
		 {
		 }

		function UpGO()
		 {
			LinkToFolderUp = ParamByName(location.href,'SECTIONID');
			windoc.location.href = 'listdocs.php?SECTIONID=' + windoc.LinkToFolderUp + '&MASK=Image';
		 }
		 
		
		function ParamByName(s,param)
			{	
			s=s+'';
			param=param+'';
		
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
			
      function getSelectedIndexes (oListbox)
      {
        var arrIndexes = new Array;
        for (var i=0; i < oListbox.options.length; i++)
        {
            if (oListbox.options[i].selected) arrIndexes.push(i);
        }
        return arrIndexes;
      };

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
      
  function subm() {
    var WorkURL = 'http://'+location.host+'/?';
    var dialog = window.opener.CKEDITOR.dialog.getCurrent(); // определяем текущий диалог  - нашел здесь - http://cksource.com/forums/viewtopic.php?f=11&t=16161&start=0  
    var funcNum = getUrlParam('CKEditorFuncNum'); // получаем параметр из ссылки диалога  
    //var imageUrl = WorkURL+"id="+document.getElementById('NumSrc').value+""; // ссылка на картинку  
    var imageUrl = basePath+document.getElementById('ImgPath').value; // ссылка на картинку
    if (document.getElementById('PREVIEW').options[document.getElementById('PREVIEW').selectedIndex].value=='TRUE'){
      //var imageUrl = WorkURL+"id="+document.getElementById('NumSrc').value+"&preview=true"; // вставляем превью
      var imageUrl = basePath+document.getElementById('ImgPreview').value; // вставляем превью  
    }
    
    if (document.getElementById('PREVIEW').options[document.getElementById('PREVIEW').selectedIndex].value=='LINK'){
      /*var imageUrl = WorkURL+"id="+document.getElementById('NumSrc').value+"&preview=true"; // вставляем превью со ссылкой на большую    
      var imageLnkUrl = WorkURL+"id="+document.getElementById('NumSrc').value+"";*/             
      var imageUrl = basePath+document.getElementById('ImgPreview').value; // вставляем превью со ссылкой на большую    
      var imageLnkUrl = basePath+document.getElementById('ImgPath').value;      
    } else { 
      var imageLnkUrl = "";
    }           
  
    var imageAlt = document.getElementById('FileName').value; 
    var docIdField = dialog.getContentElement( 'info', 'txtAlt' );
    docIdField.setValue( imageAlt );
    
    var docIdField = dialog.getContentElement( 'Link', 'txtUrl' );
    docIdField.setValue( imageLnkUrl ); 
    
    window.opener.CKEDITOR.tools.callFunction(funcNum, imageUrl);      
    window.close(); // закрываем диалог
  }  
		
</script>

</HEAD>

<BODY bgColor="#FFFFFF">
<TABLE CELLSPACING=1 cellpadding="1" width="100%" border="0">
<tr>
	<td colspan="3"><img src="images/up.gif" border="0" alt="Вверх" onclick="UpGO();" style="cursor:hand;"></td>
</tr>
<tr>
	<td colspan="3">
	<script language="JavaScript">
	current_url=document.location;
	
	nic_sectionid=ParamByName(current_url,'SECTIONID');

	document.write('<iframe name="windoc" frameborder="1" src="listdocs.php?SECTIONID=' + nic_sectionid + '&MASK=Image" marginheight="0" marginwidth="0" width="500" height="500"></iframe>');

	</script>
	</td>
</tr>
<TR>
  <TD>Картинка:</TD>
  <TD><INPUT TYPE=text SIZE=55 NAME=FileName id=FileName></TD>
</TR>
<!--<INPUT TYPE=hidden  name="PREVIEW" id="PREVIEW"/>-->
<TR>
  <TD>Тип вставки:</TD>
  <TD>
  	<select name="PREVIEW" id="PREVIEW">
  		<option value="" selected="true">Сама картинка</option>
  		<option value="TRUE">Уменьшенная копия каринки (preview)</option>
  		<option value="LINK">Уменьшенная копия со ссылкой на саму картинку</option>
	   </select>
  </TD>
</TR>
</TABLE>
<table CELLSPACING=0 cellpadding="0" width="100%" border="0">
<tr>
	<td align="center" valign="baseline" style="padding-top:10px;">
		
    <BUTTON ID=Ok ONCLICK="subm();" style="width:100px;">Готово</BUTTON>
    <BUTTON ONCLICK="window.close();" style="margin-left:15px;margin-right:15px;">Отмена</BUTTON>		
		
		<INPUT TYPE=hidden SIZE=55 NAME=SectionID id=SectionID>
		<INPUT TYPE=hidden SIZE=55 NAME=ParentSectionID id=ParentSectionID>
		<INPUT TYPE=hidden SIZE=55 NAME=NumSrc id=NumSrc>
		<INPUT TYPE=hidden SIZE=55 NAME=ImgPath id=ImgPath>
		<INPUT TYPE=hidden SIZE=55 NAME=ImgPreview id=ImgPreview>
	</td>
</tr>
</table>
</BODY>
</HTML>
