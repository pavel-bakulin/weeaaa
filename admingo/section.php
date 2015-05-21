<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	
	$sid = intval($_REQUEST['sid']);
	$ANCESTOR = $sid;
	$action = $_REQUEST['action'];	
	$STID = $_REQUEST['STID'];	
	
	$rqpath_before = '/';
	$rqpath_for_full = '/';
	$sql = "SELECT rqpath FROM sections WHERE id = $sid";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
    $rqpath = $myrow->rqpath;
    if (strlen($rqpath)>0) {
      $rqpath_arr = preg_split('/\//', $rqpath);      
      $rqpath_last = $rqpath_arr[count($rqpath_arr)-2]; //это покажем юзеру в поле Путь при редактировании папки
      $rqpath_for_full = substr($rqpath, 0, strlen($rqpath)-strlen($rqpath_last)-1);
      if ($action=='update') {
        $rqpath_before = substr($rqpath, 0, strlen($rqpath)-strlen($rqpath_last)-1);      
      } else {
        $rqpath_before = $rqpath;
      }
    }
	}
		
	if ($action=='delete') {
		$sql = "SELECT id FROM sections WHERE ancestor = $sid";
		$result = $db->execute($sql);
		if (mysql_num_rows($result)) {
			header("Location: cannotdeletesection.html");
		}
		else {
		  $doctypes = array ("image"=>"images","simple"=>"simple","st"=>"st","material"=>"materials","banner"=>"banners","question"=>"question","goods"=>"goods","orders"=>"orders","siteuser"=>"siteuser","forum"=>"forum");
  		$sql = "SELECT * FROM alldocs WHERE sid = $sid";  		
  		$result = $db->execute($sql);  		
  		while ($myrow = mysql_fetch_object($result)) {
  		  $documentid = $myrow->documentid;
        $doctype = $doctypes[$myrow->doctype];
    		$sql = "DELETE FROM $doctype WHERE documentid = $documentid";
    		$db->execute($sql, false);
    		$sql = "DELETE FROM alldocs WHERE documentid = $documentid";
    		$db->execute($sql, false);
  		}
  		
			$sql = "DELETE FROM sections WHERE id = $sid";
			$db->execute($sql, false);
			header("Location: ok.html");
		}
	}
	else if ($action=='edit') {
		$sql = "SELECT * FROM sections WHERE id = $sid";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result))
		{
			$NAME = htmlspecialchars($myrow->name);
			$RQPATH = $rqpath_last;
			$DESCRIPTION = htmlspecialchars($myrow->description);
			$ACTIVE = $myrow->active;
			$IMAGEID = $myrow->imageid;
			$ROOTDOCID = $myrow->rootdocid;
			$STID = $myrow->stid;
			$ANCESTOR = $myrow->ancestor;
			$PARAM1 = $myrow->param1;
			$PARAM2 = $myrow->param2;
			$PARAM3 = $myrow->param3;
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{
		if (!isset($_REQUEST['ACTIVE'])) {$ACTIVE=0;}
		$NAME=trim(mysql_real_escape_string($_REQUEST['NAME']));
		$RQPATH=trim(mysql_real_escape_string($_REQUEST['RQPATH']));		
		$DESCRIPTION=trim(mysql_real_escape_string($_REQUEST['DESCRIPTION']));
		$ACTIVE=intval($_REQUEST['ACTIVE']);
		$IMAGEID=intval($_REQUEST['IMAGEID']);
		$STID=intval($_REQUEST['STID']);
		$ROOTDOCID=intval($_REQUEST['ROOTDOCID']);
		$ANCESTOR = intval($_REQUEST['ANCESTOR']);
		$PARAM1=trim(mysql_real_escape_string($_REQUEST['PARAM1']));
		$PARAM2=trim(mysql_real_escape_string($_REQUEST['PARAM2']));
		$PARAM3=trim(mysql_real_escape_string($_REQUEST['PARAM3']));
		if (get_magic_quotes_gpc()) { 
			$NAME = stripslashes($NAME);
			$DESCRIPTION = stripslashes($DESCRIPTION);
			$PARAM1 = stripslashes($PARAM1);
			$PARAM2 = stripslashes($PARAM2);
			$PARAM3 = stripslashes($PARAM3);
		}				

		$error="";
		/*проверка на ошибки*/
		$sql = "SELECT id, path FROM sections WHERE id = $ANCESTOR";
		$result = $db->execute($sql);
    if ($myrow = mysql_fetch_object($result)) {
      $isSection = true;
      $PATH = $myrow->path;
    }		
		if (!$isSection) {$error.="Недопустимое значение корневой папки. $isSection";}
		if ($action=='update' && $ANCESTOR == $sid) {$error.="Недопустимое значение корневой папки. $ANCESTOR=$sid";}
		
		if (strlen($NAME)==0 || strlen($NAME)>255) {$error.="Проблемы с названием<br/>";}
		
		if (strlen($RQPATH)>0) {
  		$sql = "SELECT * FROM sections WHERE rqpath = '".$rqpath_before.$RQPATH."/' AND id!=$sid";
  		$result = $db->execute($sql);		
  		$isSection = mysql_num_rows($result);
  		if ($isSection) {$error.="Раздел с таким путем уже существует.";}
  	}
  	
  	if (!strlen($RQPATH)) {
  	 $RQPATH = makeURL($NAME);
  	}
		
		if (!strlen($error)) {
			if ($action=='update') {
        if (strlen($RQPATH)>0) {
          $RQPATH = str_replace ('/', '', $RQPATH);
          $RQPATH = $rqpath_before . $RQPATH . '/';         
        }        					
        //Если меняют название промежуточной папки. Меняем  rqpath всех дочерних элементов.
        $sql = "SELECT rqpath FROM sections WHERE id = $sid";
        $result = $db->execute($sql);
        if ($myrow = mysql_fetch_object($result)) {
          $rqpath_old = $myrow->rqpath;
        }		
        if ($rqpath_old != $RQPATH) { //значит путь отредактировали
        	updateSubSectionRQPath($sid,$rqpath_old,$RQPATH);
        	updateDocumentsRQPath($sid,$rqpath_old,$RQPATH);
        }

				$sql = "UPDATE sections SET name='$NAME', description='$DESCRIPTION', active=$ACTIVE, imageid=$IMAGEID, stid=$STID, rootdocid=$ROOTDOCID, ancestor=$ANCESTOR, rqpath='$RQPATH', param1='$PARAM1', param2='$PARAM2', param3='$PARAM3' WHERE id = $sid";
				$db->execute($sql, false);
				
				header("Location: ok.html");
				//header("Location: $PHP_SELF?sid=$sid&action=edit");
			}
			else {
				getID();
				
        if (strlen($RQPATH)) {
          $RQPATH = str_replace ('/', '', $RQPATH);
          $RQPATH = $rqpath_before . $RQPATH . '/';         
        } else {
          if ((int)$ACTIVE == 1) {$RQPATH = $rqpath_before . $lastid . '/';}        
        }
        $PATH = $PATH .$lastid.'_';
								
				$sql = "INSERT INTO sections (id, position, name, description, active, ancestor, imageid, rootdocid, stid, rqpath, path, param1, param2, param3) VALUES ($lastid, $lastid, '$NAME','$DESCRIPTION',$ACTIVE, $ANCESTOR, $IMAGEID, $ROOTDOCID, $STID, '$RQPATH', '$PATH', '$PARAM1', '$PARAM2', '$PARAM3')";

				$db->execute($sql, false);
				header("Location: ok.html");
			}
		}
}
?>
<html>
<head>
	<title>Создание/редактирование раздела</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="scripts/jquery-1.3.2.min.js"></script>
</head>
<body>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input type='hidden' name='submit' value='submit'/>
<?php		
	echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
	if ($action=='edit') {
		echo "<input type='hidden' name='action' value='update'/>";
	}
	
?>
  <div class="docHeader"><div>
    <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
    <h1>Раздел</h1>        
  </div></div>
  <div class="docBody">
<?php
	if (isset($_REQUEST['update'])) {
    echo '<div class="alert alert-success">Изменения внесены</div>';
  }
	if (strlen($error)) {
		echo '<div class="alert alert-error">'.$error.'</div>';
  }
?>
		<table class="conttbl2" id="paddtbl">
		<tr>
			<td bgColor="#f4f4f4">Заголовок:</td>
			<td></td>
			<td><input type="text" name="NAME" class="inp" value="<?php echo $NAME; ?>"/></td>
		</tr>	
		<tr>
			<td bgColor="#f4f4f4" valign="top">Путь:</td>
			<td></td>
			<td>
        <input type="text" name="RQPATH" id="RQPATH" class="inp" value="<?php echo $RQPATH; ?>"/>
<small>Полный путь раздела:</small>
<div id="fullPath"></div>
<script>
var thispath = $('input#RQPATH').val();
if (thispath.length) thispath = thispath + '/';
<?php
  if ($action == 'edit') {                               
    echo "var fpath = '".'http://'.$_SERVER['HTTP_HOST'].$rqpath_for_full."';";
  } else {
    if (!strlen($rqpath)) $rqpath = '/';
    echo "var fpath = '".'http://'.$_SERVER['HTTP_HOST'].$rqpath."';";
  }
?>
$('#fullPath').html(fpath+thispath);

$('input#RQPATH').bind('keyup', function(){
  var thispath = $(this).val();
  if (thispath.length) thispath = thispath + '/';
  $('#fullPath').html(fpath+thispath);
});
</script>                     
      </td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Активность:</td>
			<td></td>
			<td>
<?php
	if (isset($ACTIVE) && $ACTIVE==0) echo '<input type="checkbox" name="ACTIVE" value="1"/>';
	else echo '<input checked="true" type="checkbox" name="ACTIVE" value="1"/>';
?>
			</td>
		</tr>	
		<tr>
			<td bgColor="#f4f4f4">Корневой документ:</td>
			<td></td>
			<td>
        <input type="text" name="ROOTDOCID" id="ROOTDOCID" class="inp2" style="width:100px;float:right;" value="<?php echo $ROOTDOCID; ?>"/>        
        <select class="inp2" id="rootdocsel" onChange="document.getElementById('ROOTDOCID').value=$('#rootdocsel option:selected').val();" style="width:190px;">
          <option value='0'>---</option>";
<?php
    		$sql = "SELECT documentid, title FROM alldocs WHERE sid=$sid AND doctype='material' ORDER BY position DESC LIMIT 1";
    		$result = $db->execute($sql);
    		while ($myrow = mysql_fetch_object($result)) {
    		  echo "<option value='".$myrow->documentid."'>".$myrow->title."</option>";
    		}
    		echo '<option disabled=true>---</option>';
    		$sql = "SELECT documentid, title FROM alldocs WHERE sid=2 AND doctype='simple' OR doctype='form' ORDER BY position DESC";
    		$result = $db->execute($sql);
    		while ($myrow = mysql_fetch_object($result)) {
    		  echo "<option value='".$myrow->documentid."'>".$myrow->title."</option>";
    		}        
?>
        </select>
<script>
<?php
  if (isset($ROOTDOCID) && $ROOTDOCID>0) {
    echo  "var ROOTDOCID=$ROOTDOCID;"; 
  }
  else {
    echo  "var ROOTDOCID=0;";
  }
?>
  if (ROOTDOCID>0) {
    $("#rootdocsel [value='<?php echo $ROOTDOCID; ?>']").attr("selected", "selected");
  }
</script>        
      </td>
		</tr>			
		<input type="hidden" name="ANCESTOR" class="inp" value="<?php echo $ANCESTOR; ?>"/>
		<tr>
			<td bgColor="#f4f4f4">Шаблон:</td>
			<td></td>
			<td><input type="text" name="STID" class="inp" value="<?php echo $STID; ?>"/></td>
		</tr>		
		</table>
	</div>
</form>
</body>
</html>