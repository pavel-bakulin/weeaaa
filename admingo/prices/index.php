<?php
  setlocale(LC_ALL,'ru_RU.UTF-8'); 
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../login.php";  			  	
?>
<html>
<head>
	<title>CMS AdminGo - смена цен</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
	<link rel="stylesheet" type="text/css" href="/admingo/css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="/admingo/scripts/jquery-1.3.2.min.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="/admingo/scripts/main.js"></script>
	<script>
$(document).ready(function () {     
  $('#priceForm').submit(function(event) {
      var params = $(this).serialize();              
      $.ajax({
         type: "POST",
         url: "/admingo/prices/action.php",
         data: params,
         async: false,
         success: function(result){
          $('.result').html(result);
          return false;            
         }
      });
      return false;    
  });  
});	

	</script>
</head>
<body>
<table width="100%" height="100%">
<tr>
	<td><img src="/admingo/images/0.gif" width="10" height="10"/></td>
	<td></td>
	<td width="100%"></td>
	<td></td>
	<td><img src="/admingo/images/0.gif" width="10" height="10"/></td>
</tr>
<tr>
	<td></td>
	<td><img src="/admingo/images/l.gif" width="10" height="50"/></td>
	<td class="top" valign="middle">Admin<span>Go</span> — смена цен</td>
	<td><img src="/admingo/images/r.gif" width="10" height="50"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
	 <p><a href="/admingo/" class="links2">Вернуться</a></p>
<style>
#priceForm label {
  display:inline-block;
  width:250px;
  float:left;
}
#priceForm input, #priceForm select {
  width:200px;
}
.hint {font-style:italic;margin-top:30px;}
.result {margin-bottom:15px;font-weight:bold;}
</style>
<form id="priceForm">
  <div class="result"></div>
  <div>
    <label>Изменение цены в процентах:</label>
    <input type="text" name="value"/>
  </div>
  <div>
    <label>Выберите производителей:</label>
    <select multiple="multiple" size="10" name="manufs[]">
<?php
    		$sql = "SELECT documentid, title FROM alldocs WHERE sid=222 AND doctype='material' ORDER BY position DESC";
    		$result = $db->execute($sql);
    		while ($myrow = mysql_fetch_object($result)) {
    		  echo "<option value='".$myrow->documentid."'>".$myrow->title."</option>";
    		}        
?>    
    </select>
  </div>
  <div>
    <label></label>
    <input type="submit" name="Изменить" class="btn"/>
  </div>    
</form>
<div class="hint">
Подсказки:<br/>
1. Нескольких производителей можно выбрать, используя клваиши Shift, или Ctrl;<br/>
2. Чтобы, например, увеличить стоимость товаров выбранных производителей нада в поле "Изменение цены в процентах" ввести значение "6". Чтобы уменьшитьна 6%: "-6".<br/>  
</div>
	</td>
	<td></td>
	<td></td>
</tr>
</table>
</body>
</html>