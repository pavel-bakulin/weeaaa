<?php
  error_reporting(E_ERROR);
  setlocale(LC_ALL,'ru_RU.UTF-8'); 
  require_once "config.php";
  require_once "db_connect.php";
	require_once "login.php";	

	if (isset($_REQUEST['sid']))  {$sid = (int)$_REQUEST['sid'];}
	else {$sid=1;}		

  include 'header.php';
        
?>  

  <table class="table table-striped table-condensed">  		
<?php
    $totCount = 0;
    $sys = '';
    if (!$config->showSystem) {$sys ='AND id!=2';}
    if ($sid>1 || !strlen($accessSID)) {
		  $sql = "SELECT id, name, date, active FROM sections WHERE ancestor=$sid $sys ORDER BY position DESC";
		} else {
		  $sql = "SELECT id, name, date, active FROM sections WHERE id IN ($accessSID) ORDER BY position DESC";
		}
		$result = $db->execute($sql);
		$totCount += mysql_num_rows($result);
		
		while ($myrow = mysql_fetch_object($result)) {
			echo '
			<tr>
				<td width="55%">
          <a class="title" href="index.php?sid='.$myrow->id.'"><img src="images/folder'.$myrow->active.'.png" class="i" align="left"/>'. stripslashes(htmlspecialchars($myrow->name,ENT_QUOTES)).'</a>        
          <div class="docsecActions">
            <span onClick="editSection('.$myrow->id.')"><i class="i2-prop"></i>Свойства</span>		
            <span onClick="sortSectionDocument('.$myrow->id.',\'section\')"><i class="i2-sort"></i>Сортировать</span>
        		<span onClick="deleteSection('.$myrow->id.', '.$myrow->id.')"><i class="i2-del"></i>Удалить</span>                        
          </div>        
        </td>
				<td width="10%" align="center"><b title="Номер раздела">'.$myrow->id.'</b></td>
				<td width="15%" align="center" class="date">'.substr($myrow->date,0,10).'</td>
			</tr>
			';
		}
    	
    if (!strlen($accessSID)) {
		  $sql = "SELECT * FROM alldocs WHERE sid=$sid ORDER BY position DESC";
		} else {
		  $sida = preg_split('/,/', $accessSID);
		  $where = '(';
		  for ($i=0;$i<count($sida);$i++) {
		    $where .= "path LIKE '%$sida[$i]_%'";
		    if ($i!=count($sida)-1) $where .= ' OR '; 
		  }
		  $where .= ')';
		  $sql = "SELECT * FROM alldocs WHERE sid=$sid 
              AND $where 
              ORDER BY position DESC";
		} 
		$result = $db->execute($sql);
		$totCount += mysql_num_rows($result);
		
		while ($myrow = mysql_fetch_object($result)) {
			echo '
			<tr>
				<td width="55%">
				  <input type="checkbox" name="docid" class="docidCheckbox" value="'.$myrow->documentid.'"/>
          <a class="title" onClick="editDoc('.$myrow->documentid.', '.$sid.', \''.$myrow->doctype.'\');"><img src="images/'.$myrow->doctype.$myrow->disable.'.png" class="i docIcon" align="left" doctype="'.$myrow->doctype.'" sid="'.$sid.'" key="'.$myrow->documentid.'"/>
          '.stripslashes(htmlspecialchars($myrow->title,ENT_QUOTES)).'</a>
          <div class="docsecActions">
            <span onClick="editDoc('.$myrow->documentid.', '.$sid.', \''.$myrow->doctype.'\');"><i class="i2-edit"></i>Редактировать</span>
            <span onClick="propDoc('.$myrow->documentid.', '.$sid.');"><i class="i2-prop"></i>Свойства</span>
            <span onClick="sortSectionDocument('.$myrow->documentid.',\'document\')"><i class="i2-sort"></i>Сортировать</span>
        		<span onClick="copyDoc('.$myrow->documentid.')"><i class="i2-copy"></i>Копировать</span>        		
            <span onClick="deleteDoc('.$myrow->documentid.', '.$myrow->documentid.', \''.$myrow->doctype.'\');"><i class="i2-del"></i>Удалить</span>           
          </div>
        </td>
				<td width="10%" align="center"><b title="Номер документа">'.$myrow->documentid.'</b></td>
				<td width="15%" align="center" class="date">'.substr($myrow->create_date,0,16).'</td>
			</tr>
			';
		}	
		
		if (!$totCount) {
		  echo 'Пустой раздел';
		}
  	
?>
		</table>
</div>

</body>
</html>