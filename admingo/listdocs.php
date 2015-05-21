<?php
  require_once "config.php";  
	require_once "db_connect.php";
  require_once "login.php";  
?>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style>
.links {font-family:Arial; font-size:16px; line-height:25px; font-weight:normal; color:#1f1f1f; text-decoration: none;}
.links:hover {color:#c83400;}
	</style>
	<script language="JavaScript" type="text/JavaScript">
function byka(sid, iid, fname, imgsrc, ImgPath, doctype, rqpath, ImgPreview)
{
	if (parent.document.getElementById('ParentSectionID')) parent.document.getElementById('ParentSectionID').value = sid;
	if (parent.document.getElementById('SectionID')) parent.document.getElementById('SectionID').value = iid;
	if (parent.document.getElementById('NumSrc')) parent.document.getElementById('NumSrc').value = iid;
	if (parent.document.getElementById('FileName')) parent.document.getElementById('FileName').value = fname;
	if (parent.document.getElementById('doctype')) parent.document.getElementById('doctype').value = doctype;
	if (parent.document.getElementById('rqpath')) parent.document.getElementById('rqpath').value = rqpath;
	if (parent.document.getElementById('ImgPath')) {parent.document.getElementById('ImgPath').value = ImgPath;}
	if (parent.document.getElementById('ImgPreview')) {parent.document.getElementById('ImgPreview').value = ImgPreview;}
	if (parent.document.getElementById('ImgSrc')) {parent.document.getElementById('ImgSrc').value = imgsrc;}
}
	</script>	
</head>
<body style="padding:8px;">
<?php
	$SECTIONID=intval($_REQUEST['SECTIONID']);
	
	/*Это для кнопочки "вверх"*/
	$sql = "SELECT ancestor FROM sections WHERE ancestor in (select ancestor FROM sections WHERE id=$SECTIONID) LIMIT 1";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_row($result))
	{
		echo '<script>LinkToFolderUp='.$myrow[0].'</script>';
	}
			
	$sql = "SELECT id, name FROM sections WHERE ANCESTOR=$SECTIONID ORDER BY position desc";
	$result = $db->execute($sql);	
	while ($myrow = mysql_fetch_row($result))
	{
	  if (isset($_REQUEST['MASK']) && $_REQUEST['MASK']=='Image') $mask = '&MASK=Image'; else $mask = ''; 
		echo '<img src="images/folder16.gif" class="i" align="left" /><a class="links" href="listdocs.php?SECTIONID='.$myrow[0].$mask.'">'.$myrow[1].'</a><br clear="all"/>';
	}
		
	if (isset($_REQUEST['MASK']) && $_REQUEST['MASK']=='Image')
	{
    $sql = "SELECT alldocs.documentid, alldocs.title, doctype, images.image, images.preview FROM alldocs INNER JOIN images ON alldocs.documentid = images.documentid AND sid =$SECTIONID ORDER BY position DESC ";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result)) {
		  $fname = mysql_real_escape_string(str_replace('"', '', $myrow[1]));
			echo '<img src="images/image16.gif" class="i" align="left" /><a class="links123" href="javascript:byka('.$SECTIONID.',\''.$myrow[0].'\',\''.$fname.'\',0,\''.$myrow[3].'\',\'image\',\'\',\''.$myrow[4].'\');">'.$myrow[1].'</a><br clear="all"/>';
		}
	}
	else
	{	
		$sql = "SELECT documentid, title, doctype, rqpath FROM alldocs WHERE sid=$SECTIONID ORDER BY position desc";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result)) {
      $fname = mysql_real_escape_string(str_replace('"', '', $myrow[1]));
			echo '<img src="images/'.$myrow[2].'16.gif" class="i" align="left" /><a class="links" href="javascript:byka('.$SECTIONID.',\''.$myrow[0].'\',\''.$fname.'\',0,0,\''.$myrow[2].'\',\''.$myrow[3].'\');">'.$myrow[1].'</a><br clear="all"/>';
		}
	}
?>
</body>
</html>