<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	header('Content-type: text/html; charset=UTF-8');
	
	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
	
	if ($action=='delete')  {
		$sql = "DELETE FROM orders WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') {
		$sql = "SELECT * FROM orders WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {
			$TITLE = $myrow->title;
			$DATE = $myrow->date;
			$CONTENT = $myrow->content;
			$PAID = $myrow->paid;
			$PAYTYPE = $myrow->paytype;
			$DELIVERY = $myrow->delivery;
			$USERID = $myrow->userid;
			$USERNAME = $myrow->username;
			$STATUS = $myrow->status;
			$USERID = $myrow->userid;
			$BALLSCALC = (int)$myrow->ballscalc;
		}
		$xml = new DOMDocument('1.0');  		
  	$xml->loadXML($CONTENT);
		$xsl = new DOMDocument;
	    $xsl->substituteEntities = true;
	    $xsl->resolveExternals = true;	    
	  	$xsl->load("xsl/order.xsl");			
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xsl);			
	    $CONTENT = $xslt->transformToXML($xml);   	    
	    $CONTENT = html_entity_decode($CONTENT);
	}
	else if ($_REQUEST['submit']) {
    $PAID = (int)$_REQUEST['paid'];
    $STATUS = $_REQUEST['status'];	  
		$sql = "UPDATE orders SET paid=$PAID, status='$STATUS' WHERE documentid = $docid";		
		$db->execute($sql, false);
		
		header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
	}
?>
<html>
<head>
	<title>Заказ</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="scripts/jquery-1.3.2.min.js"></script>
	<script language="JavaScript" type="text/JavaScript">
$(function() {
  $('#ballsCalc').bind('click', function(event) {
    var params = 'orderid='+$(this).attr('orderid');
    $(this).replaceWith('<p><i>Баллы пересчитаны</i></p>');
    $.ajax({
       type: "POST",
       url: "/admingo/balls_calc.php",
       data: params,
       async: false,
       success: function(result){
           alert(result);
       }
    });       
  });  
});	
	</script>
</head>
<body>
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
	if ($action=='edit') 
	{
		echo "<input type='hidden' name='action' value='update'/>";
		echo "<input type='hidden' name='docid' value='$docid'/>";
	}
?>
<table width="100%" height="100%">
<tr>
	<td><img src="images/0.gif" width="6" height="6"/></td>
	<td></td>
	<td width="100%"></td>
	<td></td>
	<td><img src="images/0.gif" width="6" height="6"/></td>
</tr>
<tr>
	<td></td>
	<td><img src="images/l.gif" width="6" height="25"/></td>
	<td class="top2" valign="middle"><b>З</b>аказ</td>
	<td><img src="images/r.gif" width="6" height="25"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
		<table class="conttbl2" id="paddtbl">
		<tr>
			<td bgColor="#f4f4f4"><img src="images/0.gif" width="120" height="6"/></td>
			<td><img src="images/0.gif" width="15" height="6"/></td>
			<td width="100%">
<?php
	if (isset($_REQUEST['update'])) {
    echo "<span class='allright'>Изменения внесены</span></br>";
  }
?>
      </td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Заголовок:</td>
			<td></td>
			<td><?php echo $TITLE; ?></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Оплата:</td>
			<td></td>
			<td><?php echo $PAYTYPE; ?></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Доставка:</td>
			<td></td>
			<td><?php echo $DELIVERY; ?></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Дата:</td>
			<td></td>
			<td><?php echo $DATE; ?></td>
		</tr>        		
		<tr>
			<td bgColor="#f4f4f4">Покупатель:</td>
			<td></td>
			<td><a href="/admingo/user.php?docid=<?php echo $USERID; ?>&amp;sid=120&amp;action=edit"><?php echo $USERNAME; ?></a></td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4">Оплата:</td>
			<td></td>
			<td>
        <select name="paid" class="inp2">
          <option value="0"<?php if ($PAID==0) echo ' selected';?>>Не оплачен</option>
          <option value="1"<?php if ($PAID==1) echo ' selected';?>>Оплачен</option>
        </select>
      </td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4">Статус:</td>
			<td></td>
			<td>
        <select name="status" class="inp2">
          <option value="В работе"<?php if ($STATUS=='В работе') echo ' selected';?>>В работе</option>
          <option value="Выполнен"<?php if ($STATUS=='Выполнен') echo ' selected';?>>Выполнен</option>
          <option value="Отменен"<?php if ($STATUS=='Отменен') echo ' selected';?>>Отменен</option>
        </select>
      </td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td valign="top"><input type="submit" name="submit" class="btn" value="Изменить"/></td>
		</tr>		
		<tr height="100%">
			<td bgColor="#f4f4f4" valign="top">Детали заказа:</td>
			<td></td>
			<td valign="top">
        <?php echo $CONTENT; ?>
        
<? if ($USERID) { ?>
  <? if ($BALLSCALC) { ?>
    <p><i>Баллы пересчитаны</i></p>
  <? } else { ?>
    <p></p>
    <input type="button" id="ballsCalc" orderid="<? echo $docid; ?>" class="btn" value="Пересчитать баллы пользователя" style="width:auto;"/>      
  <? } ?>
<? } ?>
      </td>
		</tr>
		</table>
	</td>
	<td></td>
	<td></td>
</tr>
</table>
</form>
</body>
</html>