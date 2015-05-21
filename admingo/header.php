<html>
<head>
	<title>CMS AdminGo</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap-modal.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="scripts/lib.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="scripts/main.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="scripts/index.js"></script>
</head>
<body class="index">
  <div class="header">
      <div class="r">
        <a href="/admingo/settings.php" class="btn btn-danger"><i class="i-sett"></i>Настройки</a>
        <a href="/admingo/index.php?EXIT=TRUE" class="btn btn-danger"><i class="i-off"></i>Выход</a>
      </div>
      
      <div class="logoWrap"><a class="logo" style="width: 79px;" href="/admingo/"></a> — управление сайтом</div>
  </div>
  
<? if ($sid) { 
	$result = $db->execute("SELECT ancestor FROM sections WHERE id=$sid");
	if ($myrow = mysql_fetch_row($result)) {
		$ancestor = (int)$myrow[0];
	}
?>  
  <div class="content">
    <div class="breadcrumbes">
      <? if ($sid>1) { ?>
      <button class="btn btn-info btn-small" type="button" id="upBtn" href="index.php?sid=<? echo $ancestor; ?>"><i class="icon-arrow-up"></i>Вверх</button>    
      <? } ?>
      <i>Раздел: </i> <a href="index.php"><?php echo $_SERVER['SERVER_NAME']; ?></a> <span>/</span>
      
      <?php	
        
      	$path = array();
      	
      	function pathCreate($current, $end) {
      		global $sid;
      		global $db;
      		global $path;
      		
      		if ($current!=$end && $current!=1) {
      			$result = $db->execute("SELECT `ancestor`, `name` FROM `sections` WHERE `id`=$current");
      			while ($myrow = mysql_fetch_row($result)) {
      				$path[$current] = $myrow[1];
      				pathCreate($myrow[0],$end);
      			}
      		}
      		else {return;}
      	}
      	
      	pathCreate($sid,1);
      	
      	end($path);
      	while (current($path)) {
      		echo '<a href="index.php?sid='.key($path).'">'.$path[key($path)].'</a> <span>/ </span>';
      		prev($path);
      	}
      ?>
    </div>  
  <div class="actions clearfix">     
  <? if ($sid!=140) { ?>
    <button class="btn btn-info" onClick="newSection(<?php echo $sid;?>);"><i class="i-sec"></i>Создать раздел</button>
    <button class="btn btn-info" id="createDoc"><i class="i-doc"></i>Создать документ</button>
    
	  <div class="selectMode">
	    <label style="display:none;"><input type="checkbox" class="ckeckAll"/>Все</label>
      <span class="dottedLink on" id="selectMode">Включить режим мультивыбора</span>
      <span class="withSelects">
        <span>С выбранными:</span>
        <button class="btn btn-info btn-small" id="multycopy"><i class="i2-copy-white"></i>скопировать</button>
        <button class="btn btn-danger btn-small" id="multyremove"><i class="i2-del-white"></i>удалить</button>
      </span>
    </div>

    <div class="modal hide fade" id="doctypes" tabindex="-1" role="dialog">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Выберите тип документа</h3>
      </div>
      <div class="modal-body">   
        <?php
        	foreach ($config->doctypes as $type => $name) {
            echo '<a href="#" class="doc" onClick="javascript:newDoc('.$sid.',\''.$type.'\')"><img src="images/'.$type.'.png" align="left"/>'.$name.'</a>';
          }
        ?>  
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>        
      </div>
    </div>  
    <?php
      if ($config->yml) {
      	echo '<a href="/admingo/yml.php" target="_new" class="btn btn-info"><i class="i-yam"></i>Яндекс Маркет</a>';
      }				
      if (isset($_COOKIE['copydocid']) && (int)$_COOKIE['copydocid']>0) {
        echo '<button class="btn btn-danger" onClick="JavaScript:wopen(\'copydoc.php?copydocid='.$_COOKIE['copydocid'].'&sid='.$_REQUEST['sid'].'\'); return false;"><i class="i-add"></i>Вставить</button>';
      }				
    ?>    
  <? } ?>
  </div>
<? } ?>  
