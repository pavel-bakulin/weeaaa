		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td>
<script language="JavaScript" type="text/JavaScript" src="scripts/main.js"></script>
<script>
function AddDocument() {
<?php
    //echo 'document.domain="'.$_SERVER['SERVER_NAME'].'";';
		echo 'var URL="http://'.$_SERVER['SERVER_NAME'].'";';
		echo 'var SECTIONID="'.$sid.'";';
?>
	var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes" ;
	sOptions += ",width=550";
	sOptions += ",height=630";
	sOptions += ",left=100";
	sOptions += ",top=100";
	url = URL+"/<?php echo $config->cms; ?>/selectdoc.php?filename=linkinsert&SECTIONID="+ SECTIONID;
  window.open( url, 'BrowseWindow', sOptions ) ; 
}

function adddoc(id, name, doctype) {
  cont =  "<div id='d" + id + "'>";
  cont += "<img src='images/remove.gif' class='removeLinkDoc' onClick='removedoc(" + id + ")'/>";
  cont += "<span>" + id + "</span>";
  cont += "<img src='images/" + doctype + "16.gif' onClick='m_doc_id=" + id + "; m_sec_id=1; m_doctype=\"" + doctype + "\"; editDoc(m_doc_id, m_sec_id, m_doctype);'/>";
  cont += "<font>" + name + "</font>";
  cont += "<input type='hidden' name='linked_id[]' value='" + id + "'/>";
  cont += "<input type='hidden' name='linked_name[]' value='" + name + "'/>";
  cont += "<input type='hidden' name='linked_doctype[]' value='" + doctype + "'/>";
  cont += "</div>";
  $("#linkedDocs").append(cont);
}

function removedoc (id) {
  $("#d"+id).remove();
}
</script>
<div class="linkedstop"><input type="button" id="adddoc" value="Добавить документ" onClick="AddDocument();" class="btn btn-mini btn-info"/>Связанные докумены:</div>
<div id="linkedDocs">
<?php
  $docid = (int) $docid;  
  if ($docid > 0) {
		$sql = "SELECT * FROM linkeds WHERE docid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {
      echo "<div id='d".$myrow->linkedid."'>";
      echo "<img src='images/remove.gif' class='removeLinkDoc' onClick='removedoc(".$myrow->linkedid.")'/>";
      echo "<span>".$myrow->linkedid."</span>";
      echo "<img src='images/".$myrow->doctype."16.gif' onClick='m_doc_id=".$myrow->linkedid."; m_sec_id=1; m_doctype=\"$myrow->doctype\"; editDoc(m_doc_id, m_sec_id, m_doctype);'/>";
      echo "<font>".$myrow->title."</font>";
      echo "<input type='hidden' name='linked_id[]' value='".$myrow->linkedid."'/>";
      echo "<input type='hidden' name='linked_name[]' value='".$myrow->title."'/>";
      echo "<input type='hidden' name='linked_doctype[]' value='".$myrow->doctype."'/>";
      echo "</div>";
		}
	}
?>
<input type="hidden" name="linkeds_sum" id="linkeds_sum"/>
</div>
      </td>
		</tr>