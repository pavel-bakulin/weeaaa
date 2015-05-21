<?php
require_once "lib.php";
require_once "handlers/lib_handlers.php";
$user = new UserInfo();

$fixResultNode = "";
$SHOWHIDDENSECTIONS = "";
$SCANSUBSECTIONS = "";
$ADDPARAM = "";
$RANDOM = "";
$FILTER = "";
$WHERESQL = "";
$LINKID = 0;
$STID = 0;
$CLOSEDSID = "";
$SECTIONQUERY = "";
$path = array();
$sid = isset($_REQUEST['sid'])?(int)$_REQUEST['sid']:0;

function SEARCHRESULT($srNode)
{
	global $globals, $resultXML, $sid, $db, $config;
	$ORDERBY = "ORDER BY alldocs.position DESC";
	
    $attributes = $srNode->attributes;
    foreach($attributes as $domAttribute)
    {
    	if ($domAttribute->name == "SECTIONID") {$SECTIONID = $domAttribute->value;}
    	if ($domAttribute->name == "DOCTYPE") {$DOCTYPE = $domAttribute->value;}
    	if ($domAttribute->name == "COUNT") {$COUNT = (int)$domAttribute->value;}
    	if ($domAttribute->name == "SCANSUBSECTIONS") {$SCANSUBSECTIONS = $domAttribute->value;}
    	if ($domAttribute->name == "COUNTONPAGE") {$COUNTONPAGE = (int)$domAttribute->value;}
    	if ($domAttribute->name == "QUERY") {$QUERY = trim(mysql_real_escape_string($domAttribute->value));}    	
    	if ($domAttribute->name == "ADDPARAM") {$ADDPARAM = $domAttribute->value;}
    	if ($domAttribute->name == "WHERE") {$FILTER = quote2code(trim($domAttribute->value));}
    	if ($domAttribute->name == "WHERESQL") {$WHERESQL = $domAttribute->value;}
    	if ($domAttribute->name == "RANDOM") {$RANDOM = 'TRUE';}
    	if ($domAttribute->name == "STID") {$STID = (int)$domAttribute->value;}
    	if ($domAttribute->name == "CLOSEDSID") {$CLOSEDSID = mysql_real_escape_string($domAttribute->value);}
    	if ($domAttribute->name == "LINKID") {$LINKID = $domAttribute->value;}
    	if ($domAttribute->name == "ORDERBY") {
        $val = mysql_real_escape_string($domAttribute->value);
        if (strlen($val)) {
          if ($val == 'price') $ORDERBY = 'ORDER BY m.price1';
          else if ($val == 'price-') $ORDERBY = 'ORDER BY m.price1 DESC';
          else if ($val == 'title') $ORDERBY = 'ORDER BY m.title';
          else if ($val == 'title-') $ORDERBY = 'ORDER BY m.title DESC';
          else $ORDERBY = 'ORDER BY m.'.$val;                     
        } 
      }
    	if ($domAttribute->name == "SHOWCONTENT") {$SHOWCONTENT = $domAttribute->value;}
      if ($domAttribute->name == "SHOWIMAGES") {$SHOWIMAGES = $domAttribute->value;}    	
    }
    if ($SECTIONID=="CURRENT") {$SECTIONID = $sid;}
    else if ($SECTIONID=="PARENT") {
      $sql = "SELECT ancestor FROM sections WHERE id=$sid";  		
  		$result = $db->execute($sql);
  		if (mysql_num_rows($result)>0) {
      	$myrow = mysql_fetch_object($result);
      	$SECTIONID = $myrow->ancestor; 
      }
      else $SECTIONID = 0;
    }
    else if ($SECTIONID=="ALL") {$SECTIONID = "sid";}
    else {$SECTIONID = intval($SECTIONID);}
    if ($SCANSUBSECTIONS=="true" || $SCANSUBSECTIONS=="TRUE" || $SCANSUBSECTIONS=="1") {$SCANSUBSECTIONS = "1";}
    if ($RANDOM=='TRUE') $ORDERBY = "ORDER BY RAND()";
    
    if ($STID) {$STID = ' AND a.stid = '.$STID;}    
    
    if ($LINKID=="CURRENT") {
      $LINKID = $globals['id'];
    }
    
    $COUNT = intval($COUNT);
    if (!($COUNT>0)) {$COUNT=10000;}
    $doctypes = array ("Search"=>"Search","image"=>"images","simple"=>"simple","st"=>"st","material"=>"materials","banner"=>"banners","question"=>"question","goods"=>"goods","orders"=>"orders","user"=>"user","forum"=>"forum","file"=>"file","record"=>"record","feedback"=>"feedback");    
    
    $DOCTYPE = $doctypes[$DOCTYPE];       
    
    /* filters */
    $FILTER_WHERE = $WHERESQL;
    if (strlen($WHERESQL) > 0) {$operator = ' WHERE ';$FILTER_WHERE = $operator.$FILTER_WHERE;}
    else {$operator = ' AND '; }    
    
    if (strlen($FILTER) > 0) {
      $arrF = split(',',$FILTER);
      for ($fi = 0; $fi < count($arrF); $fi++) {
        if (strpos($arrF[$fi],'[')!==false && strpos($arrF[$fi],']')!==false) {
          $arrF[$fi] = substr($arrF[$fi],1,strlen($arrF[$fi])-2);          
          $vals = $_REQUEST[$arrF[$fi]];          
          if (strlen($vals[0]) > 0) {
            $srNode->setAttribute($arrF[$fi].'_0', $vals[0]);
            $FILTER_WHERE .= $operator.$arrF[$fi].">=".$vals[0];
            $operator = ' AND ';
          }
          if (strlen($vals[1]) > 0) {
            $srNode->setAttribute($arrF[$fi].'_1', $vals[1]);
            $FILTER_WHERE .= $operator.$arrF[$fi]."<".$vals[1];
            $operator = ' AND ';
          }
        }        
        else if (strlen($_REQUEST[$arrF[$fi]]) > 0) {
          $val = $_REQUEST[$arrF[$fi]];
          if (!get_magic_quotes_gpc())  {
            $val = addslashes($_REQUEST[$arrF[$fi]]);
          }
          //$srNode->setAttribute($arrF[$fi], $val);
          $srNode->setAttribute($arrF[$fi], iconv("windows-1251","UTF-8",$val));
          if ($arrF[$fi] == 'sid') {$SECTIONID = $val;}
          else {
            if ($arrF[$fi] == 'price2') {
              $FILTER_WHERE .= $operator.$arrF[$fi]."=".$val;
            } else {
              $FILTER_WHERE .= $operator.$arrF[$fi]."='".$val."'";
            }
            $operator = ' AND ';
          }          
        }
        else  if (strpos($arrF[$fi],'=')!==false) {
          $key_value = split('=',$arrF[$fi]);
          $val = $key_value[1];
          $srNode->setAttribute($key_value[0], $val);
          $FILTER_WHERE .= $operator.$key_value[0]."=".$val;
          $operator = ' AND ';
        }        
      }            
    }        
    /* ///filters */
    
    if ($SCANSUBSECTIONS=="1")
    {
    	$SCANSUBSECTIONS=" or sid in (SELECT id FROM sections WHERE ancestor=$SECTIONID)";
    }
    else {$SCANSUBSECTIONS="";}
    
    $GOODS_AVAILABLE = '';
    $GOODS_AVAILABLE_PAGE_COUNT = '';
    if ($DOCTYPE == 'goods') {
      $GOODS_AVAILABLE = ' available=1 ';
      $GOODS_AVAILABLE_PAGE_COUNT = 'WHERE'.$GOODS_AVAILABLE;
    }
      
    if (strlen($CLOSEDSID)>0) {
      $CLOSEDSID = " AND sid NOT IN ($CLOSEDSID)";
    }
      
    /* pages */
    $START = 0;
    if (isset($COUNTONPAGE)){
  		if(isset($_REQUEST['page']))  {$CURRENTPAGE = (int)$_REQUEST['page'];}
  		else {$CURRENTPAGE = 1;}
        
      $sql = "SELECT * FROM $DOCTYPE AS m INNER JOIN alldocs ON alldocs.documentid = m.documentid AND (alldocs.sid=$SECTIONID $SCANSUBSECTIONS)$CLOSEDSID $FILTER_WHERE $ORDERBY";
  		if ($LINKID>0) {
  		  $FILTER_WHERE .= " AND linkeds.docid=".$globals['id'];
  		  $sql = "SELECT $DOCTYPE.*, linkeds.*, alldocs.sid, alldocs.position FROM $DOCTYPE INNER JOIN linkeds ON linkeds.linkedid = $DOCTYPE.documentid $FILTER_WHERE INNER JOIN alldocs ON alldocs.documentid = linkeds.linkedid $GOODS_AVAILABLE_PAGE_COUNT ORDER BY linkeds.linkedid";		  
  		}		
  		
  		$Result = $db->execute($sql);
  		$TotalMsg = mysql_num_rows($Result);
  		unset($Result);
  		
  		if ($TotalMsg <= $COUNTONPAGE) $TotalPages = 1;
  		elseif ($TotalMsg % $COUNTONPAGE == 0) $TotalPages = $TotalMsg / $COUNTONPAGE;
  		else $TotalPages = ceil ($TotalMsg / $COUNTONPAGE);
  		if($TotalMsg == 0) $MsgStart = 0;
  		else $MsgStart = $COUNTONPAGE * $CURRENTPAGE - $COUNTONPAGE + 1;
  		if ($CURRENTPAGE == $TotalPages) $msgEnd = $TotalMsg;
  		else $msgEnd = $COUNTONPAGE * $CURRENTPAGE;
  		$InitialMsg = $COUNTONPAGE * $CURRENTPAGE - $COUNTONPAGE;		
  		$PrevPage = -1;
  		$NextPage = -1;
  		if ($CURRENTPAGE > 1) {$PrevPage=$CURRENTPAGE-1;}
  		if ($CURRENTPAGE < $TotalPages) {$NextPage=$CURRENTPAGE+1;}
  		
  		$COUNT = $COUNTONPAGE;
  		$START = $InitialMsg;
    }
    /* ///pages */
    
    // поиск по связанным товаров. В выгребку попадают только товары-родители, среди детей которых есть подходящие по $FILTER_WHERE 
    if (strlen($FILTER_WHERE)>0) {
      $FILTER_WHERE_GOODS = "
                OR m.documentid IN (
                SELECT docid FROM linkeds l
                INNER JOIN $DOCTYPE g ON g.documentid = l.linkedid AND l.doctype='$DOCTYPE'
                $FILTER_WHERE) ";
      if ($DOCTYPE == 'goods') $GOODS_AVAILABLE = ' AND'.$GOODS_AVAILABLE;
    }
    else {
      $FILTER_WHERE_GOODS = '';
      if (strlen($GOODS_AVAILABLE)>0) $GOODS_AVAILABLE = ' WHERE'.$GOODS_AVAILABLE;
    }
    
    if ($DOCTYPE=='feedback') $feedbackWhere = ' AND m.active=1'; else $feedbackWhere = '';
    
    // The main query
    $sql = "SELECT * FROM $DOCTYPE AS m INNER JOIN alldocs ON alldocs.documentid = m.documentid 
            AND (sid=$SECTIONID $SCANSUBSECTIONS)$CLOSEDSID
            $GOODS_AVAILABLE
            $FILTER_WHERE 
            $FILTER_WHERE_GOODS
            $feedbackWhere            
            $ORDERBY 
            LIMIT $START, $COUNT";                      
            
		if ($LINKID>0) {
		  $FILTER_WHERE .= " AND linkeds.docid=".$globals['id'];
		  $sql = "SELECT $DOCTYPE.*, linkeds.*, alldocs.sid, alldocs.position FROM $DOCTYPE INNER JOIN linkeds ON linkeds.linkedid = $DOCTYPE.documentid $FILTER_WHERE INNER JOIN alldocs ON alldocs.documentid = linkeds.linkedid $GOODS_AVAILABLE ORDER BY linkeds.linkedid LIMIT $START, $COUNT";		  
		}
		//if (strlen($FILTER_WHERE)>0) die($sql);    
    		
  if ($DOCTYPE=="Search") {
    $sql = "SELECT * FROM materials m 
            INNER JOIN alldocs a ON m.documentid = a.documentid
            WHERE (m.content LIKE '%$QUERY%' OR m.title LIKE '%$QUERY%')
            $CLOSEDSID
            $STID            
            LIMIT $START, $COUNT";

    $result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "MATERIAL");

  		if (strlen($myrow->rqpath)) {
  		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		} else {		
			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
      }      		
			$srNodeN -> setAttribute("TITLE", $myrow->title);
			if (strlen($myrow->description)) {$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);}
      if (strlen($myrow->image)) {
        $srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow->image);
        $srNodeN -> setAttribute("IMAGE_MULTY", $config->upfolder.'multy_'.$myrow->image);
        $srNodeN -> setAttribute("IMAGE_BIG", $config->upfolder.'big_'.$myrow->image);
      }			      			
			$srNodeN -> setAttribute("DATE", $myrow->date);
			if ($SHOWCONTENT == 'TRUE') {
					$new_child = $resultXML -> createCDATASection($myrow->content);
		      $srNodeN -> appendChild($new_child);
			}			
		}
	}
	else
    if ($DOCTYPE=="materials") {    
    $previews = array();
    $images = array();
    $img_prw = array();
    $img = array();
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {
		  $ids[$myrow->documentid] = $myrow->imageid;
		}	
		$sql_img = "SELECT documentid, image, preview FROM images";
		$result_img = $db->execute($sql_img);
    while ($myrow_img = mysql_fetch_object($result_img)) {
      $previews[$myrow_img->documentid] = $config->upfolder.$myrow_img->preview;
      $images[$myrow_img->documentid]   = $config->upfolder.$myrow_img->image;
    }    
    if (count($ids)>0) {
      foreach ($ids as $key => $value) {
        $img_prw[$key] = $previews[$value];
        $img[$key] = $images[$value];
      }
    }
            
  	if (mysql_num_rows($result)) { mysql_data_seek($result, 0);}
		while ($myrow = mysql_fetch_object($result)) {
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "MATERIALS");
			
  		if (strlen($myrow->rqpath)) {
  		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		} else {		
			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
      }
			
			$srNodeN -> setAttribute("TITLE", stripslashes($myrow->title));
			if (strlen($myrow->description)) {
				$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);
			}
			if (strlen($myrow->date)) {
				$srNodeN -> setAttribute("DATE", $myrow->date);
			}
			if (strlen($myrow->type)) {
				$srNodeN -> setAttribute("MATTYPE", $myrow->type);
			}
			if (strlen($myrow->info)) {
				$srNodeN -> setAttribute("INFO", $myrow->info);
			}
			if (strlen($myrow->file)) {
				$srNodeN -> setAttribute("FILE", $config->upfolder.$myrow->file);
			}			
			/*if ($myrow->imageid) {
				$srNodeN -> setAttribute("IMAGE", $img[$myrow->documentid]);
				$srNodeN -> setAttribute("PREVIEW", $img_prw[$myrow->documentid]);
				$i_img++;
			}*/
			if (strlen($myrow->image)) { 
			 $srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow->image);
			}
			if (strlen($myrow->keywords)>0) {
				$srNodeN -> setAttribute("KEYWORDS", $myrow->keywords);
			}			
			if (strlen($myrow->metadescription)>0) {
				$srNodeN -> setAttribute("METADESCRIPTION", $myrow->metadescription);
			}
			if (strlen($myrow->pagetitle)>0) {
				$srNodeN -> setAttribute("PAGETITLE", $myrow->pagetitle);
			}
			if ($SHOWCONTENT == 'TRUE')
			{
					$new_child = $resultXML -> createCDATASection($myrow->content);
		      $srNodeN -> appendChild($new_child);
			}			
		}
	}
	else
    if ($DOCTYPE=="sport")
    {	    	            
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)){
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
  		if (strlen($myrow->rqpath)) {
  		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		} else {		
			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
      }			
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "SPORT");						
			$srNodeN -> setAttribute("TITLE", stripslashes($myrow->title));
			if (strlen($myrow->image)) $srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow->image);
			else $srNodeN -> setAttribute("IMAGE", "/images/nosport.jpg");
			$srNodeN -> setAttribute("ICON", $config->upfolder.$myrow->icon);			
		}
	}        
	else
    if ($DOCTYPE=="record")
    {	    	            
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "RECORD");
						
			$srNodeN -> setAttribute("TITLE", stripslashes($myrow->title));
			if (strlen($myrow->content)>0) {
				$srNodeN -> setAttribute("CONTENT", $myrow->content);
			}
			if (strlen($myrow->date)>0) {
				$srNodeN -> setAttribute("DATE", $myrow->date);
			}
			if (strlen($myrow->addparam)>0) {
				$srNodeN -> setAttribute("ADDPARAM", $myrow->addparam);
			}				
		}
	}
	else
    if ($DOCTYPE=="feedback") {
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "FEEDBACK");
						
			$srNodeN -> setAttribute("USERNAME", stripslashes($myrow->username));
			$srNodeN -> setAttribute("USERID", stripslashes($myrow->userid));
			$srNodeN -> setAttribute("CONTENT", $myrow->content);
      $srNodeN -> setAttribute("RATE", $myrow->rate);						
			$srNodeN -> setAttribute("DATE", $myrow->date);							
		}
	}	  	
	else
    if ($DOCTYPE=="mailform")
    {	    	            
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "MAILFORM");
						
			$srNodeN -> setAttribute("TITLE", stripslashes($myrow->title));
			$srNodeN -> setAttribute("EMAIL", stripslashes($myrow->email));
			if (strlen($myrow->description)>0) {
				$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);
			}				
		}
	}		
	else
    if ($DOCTYPE=="orders")
    {
      
      if ($ADDPARAM=='OWNER') {
        if (isset($_COOKIE["stateid"])) $stateid = $_COOKIE["stateid"];
    		$sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
    		$result = $db->execute($sql);
    		if ($myrow = mysql_fetch_object($result)) {
    		  $userid = $myrow->documentid;
    		}
    		if ($userid>0) $ADDPARAM = " AND $DOCTYPE.userid = $userid";
      } else {$ADDPARAM='';}
      
	    $sql = "select * FROM $DOCTYPE INNER JOIN alldocs ON alldocs.documentid = $DOCTYPE.documentid AND (sid=$SECTIONID $SCANSUBSECTIONS)$ADDPARAM $ORDERBY LIMIT $START, $COUNT";
	    
	    //$srNode -> setAttribute("SQL", $sql);
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("TYPE", "ORDER");
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$sql2 = "select sid from alldocs where documentid=$myrow->documentid";
			$result2 = $db->execute($sql2);
			if ($myrow2 = mysql_fetch_row($result2))
			{
				$srNodeN -> setAttribute("URL", "?id=$myrow->documentid&sid=$myrow2[0]");
			}
			$srNodeN -> setAttribute("TITLE", $myrow->title);
			$srNodeN -> setAttribute("DATE", $myrow->date);
			$srNodeN -> setAttribute("STATUS", $myrow->status);
  		$xml = new DOMDocument('1.0');  		
  		$xml->loadXML($myrow->content);
  		$new_child = $resultXML -> importNode($xml->firstChild, true);   
  		$srNodeN -> appendChild($new_child);
		}
	}
	else
    if ($DOCTYPE=="goods") {   
    $result = $db->execute($sql); 
    $srNode -> setAttribute("sql", $sql);
		while ($myrow = mysql_fetch_object($result)) {
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "GOODS");

  		if (strlen($myrow->rqpath)) {
  		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		} else {		
			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
      }      		
			$srNodeN -> setAttribute("TITLE", $myrow->title);
			$srNodeN -> setAttribute("PRICE1", $myrow->price1);
			$srNodeN -> setAttribute("PRICE_FORMAT", number_format($myrow->price1, 0, ',', ' '));
			if ((float)$myrow->price2) $srNodeN -> setAttribute("PRICE2", $myrow->price2);
			if (strlen($myrow->description)) {$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);}
      if (strlen($myrow->image)) {
        $srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow->image);
        $srNodeN -> setAttribute("IMAGE_MULTY", $config->upfolder.'multy_'.$myrow->image);
        $srNodeN -> setAttribute("IMAGE_BIG", $config->upfolder.'big_'.$myrow->image);
      }			      			
			$srNodeN -> setAttribute("DATE", $myrow->date);			
			$srNodeN -> setAttribute("POSITION", $myrow->position);
			if (strlen($myrow->code)) {$srNodeN -> setAttribute("CODE", $myrow->code);}
			if (strlen($myrow->scale)) {$srNodeN -> setAttribute("SCALE", $myrow->scale);}
			if (strlen($myrow->manuf)) {$srNodeN -> setAttribute("MANUF", $myrow->manuf);}
			if (strlen($myrow->mark)) {$srNodeN -> setAttribute("MARK", $myrow->mark);}
			if (strlen($myrow->stock)) {$srNodeN -> setAttribute("STOCK", $myrow->stock);}
			if (strlen($myrow->param1)) {$srNodeN -> setAttribute("PARAM1", $myrow->param1);}
			if (strlen($myrow->param2)) {$srNodeN -> setAttribute("PARAM2", $myrow->param2);}
			if (strlen($myrow->param3)) {$srNodeN -> setAttribute("PARAM3", $myrow->param3);}
			if (strlen($myrow->param4)) {$srNodeN -> setAttribute("PARAM4", $myrow->param4);}
			if (strlen($myrow->param5)) {$srNodeN -> setAttribute("PARAM5", $myrow->param5);}
			if (strlen($myrow->param6)) {$srNodeN -> setAttribute("PARAM6", $myrow->param6);}
			if (strlen($myrow->param7)) {$srNodeN -> setAttribute("PARAM7", $myrow->param7);}
			if (strlen($myrow->param8)) {$srNodeN -> setAttribute("PARAM8", $myrow->param8);}
			if (strlen($myrow->param9)) {$srNodeN -> setAttribute("PARAM9", $myrow->param9);}
			if (strlen($myrow->param10)) {$srNodeN -> setAttribute("PARAM10", $myrow->param10);}			      			
			if (strlen($myrow->param11)) {$srNodeN -> setAttribute("PARAM11", $myrow->param11);}
			if (strlen($myrow->param12)) {$srNodeN -> setAttribute("PARAM12", $myrow->param12);}
			if (strlen($myrow->param13)) {$srNodeN -> setAttribute("PARAM13", $myrow->param13);}   			
			if ($SHOWCONTENT == 'TRUE') {
					$new_child = $resultXML -> createCDATASection($myrow->content);
		      $srNodeN -> appendChild($new_child);
			}			
			if ($SHOWIMAGES == 'TRUE') {
      	$sql2 = "SELECT * FROM image_owner WHERE docid = $myrow->documentid  ORDER BY sort DESC";	
      	$result2 = $db->execute($sql2);  		  
      	while ($myrow2 = mysql_fetch_object($result2)) {      
        	$srNodeI = $srNodeN -> appendChild($resultXML->createElement("PHOTO"));
      		$srNodeI -> setAttribute("IMAGE", $config->upfolder.$myrow2->image);
          $srNodeI -> setAttribute("IMAGE_SMALL", $config->upfolder.'small_'.$myrow2->image);
      		if (strlen($myrow2->title)) {$srNodeI -> setAttribute("TITLE", $myrow2->title);}
      	}
			}			
		}
	}
	else
    if ($DOCTYPE=="user")
    {
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "user");
			
  		if (strlen($myrow->rqpath)>0) {
  		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		} else {		
			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
      }
      
			if (strlen($myrow->title)>0)
			{
				$srNodeN -> setAttribute("EMAIL", $myrow->email);
			}
			if (strlen($myrow->title)>0)
			{
				$srNodeN -> setAttribute("FIO", $myrow->FIO);
			}
			if (strlen($myrow->title)>0)
			{
				$srNodeN -> setAttribute("TITLE", $myrow->title);
			}
			if (strlen($myrow->description)>0)
			{
				$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);
			}
			if ($myrow->info1>0)
			{
				$srNodeN -> setAttribute("INFO1", $myrow->info1);
			}
			if ($myrow->info2>0)
			{
				$srNodeN -> setAttribute("INFO2", $myrow->info2);
			}
			if (strlen($myrow->date)>0)
			{
				$srNodeN -> setAttribute("DATE", $myrow->date);
			}
			if (strlen($myrow->image)>0)
			{
				$srNodeN -> setAttribute("IMAGE", $myrow->image);
			}
		}
	}
	else
    if ($DOCTYPE=="banners")
    {
  		$result = $db->execute($sql);
  		while ($myrow = mysql_fetch_object($result))
  		{
  			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
  			$srNodeN -> setAttribute("IID", $myrow->documentid);
  			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
  			$srNodeN -> setAttribute("TYPE", "BANNER");
  			
    		if (strlen($myrow->rqpath)>0) {
    		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
    		} else {		
  			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
        }
        
  			$srNodeN -> setAttribute("TITLE", $myrow->title);
  			$srNodeN -> setAttribute("BAN_TYPE", $myrow->type);  			
  			if (strlen($myrow->file)>0) {
  				$srNodeN -> setAttribute("FILE", $config->upfolder.$myrow->file);
  			}
  			if (strlen($myrow->link)>0) {
  				$srNodeN -> setAttribute("LINK", $myrow->link);
  			}
  			if (strlen($myrow->param)>0) {
  				$srNodeN -> setAttribute("PARAM", $myrow->param);
  			}  			
  			if ($myrow->width > 0) {
  			 $srNodeN -> setAttribute("WIDTH", $myrow->width);
  			}
  			if ($myrow->height > 0) {
  			 $srNodeN -> setAttribute("HEIGHT", $myrow->height);
  			}
  			if ($myrow->linkdocid>0) {
  				$sql2 = "SELECT sid FROM alldocs WHERE documentid=$myrow->linkdocid";
  				$result2 = $db->execute($sql2);
  				if ($myrow2 = mysql_fetch_row($result2))
  				{
  					$srNodeN -> setAttribute("LINK", "/?id=".$myrow->linkdocid."&sid=".$myrow2[0]);
  				}
  			}
  		}
	}
	else
    if ($DOCTYPE=="file")
    {
  		$result = $db->execute($sql);
  		while ($myrow = mysql_fetch_object($result))
  		{
  			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
  			$srNodeN -> setAttribute("IID", $myrow->documentid);
  			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
  			$srNodeN -> setAttribute("TYPE", "FILE");
  			
    		if (strlen($myrow->rqpath)>0) {
    		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
    		} else {		
  			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
        }        
  			$srNodeN -> setAttribute("TITLE", $myrow->title);
  			$srNodeN -> setAttribute("LINK", $config->upfolder.$myrow->link);
  			if ($myrow->size > 0) {
  			 $srNodeN -> setAttribute("SIZE", $myrow->size);
  			}
  			if (strlen($myrow->type)>0) {
  			 $srNodeN -> setAttribute("TYPE", $myrow->type);  			
  			}
  			$srNodeN -> setAttribute("DATE", $myrow->date);  			  			
  		}
	}
	else
    if ($DOCTYPE=="simple")
    {
  		$result = $db->execute($sql);
  		while ($myrow = mysql_fetch_object($result))
  		{
  			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
  			$srNodeN -> setAttribute("IID", $myrow->documentid);
  			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
  			$srNodeN -> setAttribute("TYPE", "SIMPLE");
  			
    		if (strlen($myrow->rqpath)>0) {
    		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
    		} else {		
  			  $srNodeN -> setAttribute("URL", "/?id=".$myrow->documentid."&sid=$myrow->sid");
        }
        
  			$srNodeN -> setAttribute("TITLE", $myrow->title);
  			                         
      	$xml = new DOMDocument('1.0');
      	$xml->loadXML($myrow->content);
        if ($xml->firstChild){	
      	   $new_child = $resultXML->importNode($xml->firstChild,true);            
           $srNodeN -> appendChild($new_child);
        }
  		}
	}
	else
  if ($DOCTYPE=="images")
  {
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "IMAGE");

  		if (strlen($myrow->rqpath)>0) {
  		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		} else {		
			  $srNodeN -> setAttribute("URL", $config->upfolder.$myrow->image);
      }			
			$srNodeN -> setAttribute("PREVIEW", $config->upfolder.$myrow->preview);
			
			if (strlen($myrow->title)>0)
			{
				$srNodeN -> setAttribute("TITLE", $myrow->title);
			}
		}
	}
	else
  if ($DOCTYPE=="question") {
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {		
			$id = $myrow->documentid;
	    	if (isset($_REQUEST['results']) &&  $_REQUEST['results']=='true') {
	    		$answers = $_REQUEST['ANSWERID'];
	    		foreach ($answers as &$answerid)
	    		{
	    			$sql = "UPDATE answers SET acount = acount+1 WHERE id = $answerid";	    			
	    			$db->execute($sql, false);
	    		}
	    		$sql = "SELECT sum(acount) FROM answers WHERE questid=$id";
	    		$result = $db->execute($sql);
	    		if ($myrow = mysql_fetch_row($result))
	    		{
	    			$sql2 = "UPDATE question SET total = $myrow[0] WHERE documentid = $id";
	    			$db->execute($sql2, false);
	    		}
	    		setcookie("question$id", "true");
	    		header("Location: $_SERVER[HTTP_REFERER]");
	    	} else {
  				if (isset($_COOKIE["question$id"]) && $_COOKIE["question$id"]=="true") {$r="TRUE";}
  					else {$r="FALSE";}
  				$srNodeN = $srNode -> appendChild($resultXML->createElement("VOTE"));
  				if (isset($_REQUEST['graph']) &&  $_REQUEST['graph']=='true') {
  				  $srNodeN -> setAttribute("GRAPH", 'TRUE');
  				}
  				$srNodeN -> setAttribute("RESULTS", $r);
  				$srNodeN -> setAttribute("SHOW", "MULTIPLE");
  				$srNodeN -> setAttribute("TYPE", "QUESTION");
  				$srNodeQ = $srNodeN -> appendChild($resultXML->createElement("QUESTION"));
  				$srNodeQ -> setAttribute("RESULTS", $r);
  				$srNodeQ -> setAttribute("IID", $myrow->documentid);
  				$srNodeQ -> setAttribute("QUESTIONID", $myrow->documentid);
  				$sql2 = "SELECT sid FROM alldocs WHERE documentid=$myrow->documentid";
  				$result2 = $db->execute($sql2);
  				if ($myrow2 = mysql_fetch_row($result2))
  				{
  					$srNodeQ -> setAttribute("URL", "/?id=$myrow->documentid&sid=$myrow2[0]");
  				}
  				if (strlen($myrow->quest)>0)
  				{
  					$srNodeQ -> setAttribute("QUESTION", $myrow->quest);
  				}
  				if ($myrow->multianswer==1) {$srNodeQ -> setAttribute("MULTIPLE", "TRUE");}
  					else {$srNodeQ -> setAttribute("MULTIPLE", "FALSE");}
  				$srNodeQ -> setAttribute("TOTAL", $myrow->total);
  	
  				$sql2 = "SELECT id, answer, acount FROM answers WHERE questid=$myrow->documentid";
  				$result2 = $db->execute($sql2);
  				while ($myrow2 = mysql_fetch_row($result2)) {
  					$srNodeA = $srNodeQ -> appendChild($resultXML->createElement("ANSWER"));
  					$srNodeA -> setAttribute("ANSWERID", $myrow2[0]);	
  					$srNodeA -> setAttribute("CONTENT", $myrow2[1]);	
  					$srNodeA -> setAttribute("TOTAL", $myrow2[2]);
  					if ((int)$myrow->total>0) {$PERCENT = round((int)$myrow2[2]/(int)$myrow->total*100);}
  					else {$PERCENT = 0;}
  					$srNodeA -> setAttribute("PERCENT", $PERCENT);
  					if ((int)$myrow->total>0) {$DOUBLEPERCENT = round((int)$myrow2[2]/(int)$myrow->total*10000)/100;}
  					else {$DOUBLEPERCENT = 0;}
  					$srNodeA -> setAttribute("DOUBLEPERCENT", $DOUBLEPERCENT);
  				}
			 }
		}
	}
	elseif ($DOCTYPE=="forum") {
	  $ids = '';
    $sql = "select * FROM $DOCTYPE INNER JOIN alldocs ON alldocs.documentid = $DOCTYPE.documentid AND $DOCTYPE.parentid=0 AND (alldocs.sid=$SECTIONID $SCANSUBSECTIONS) ORDER BY forum.last_answer_id DESC LIMIT $START, $COUNT";
    
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
		  $documentid = $myrow->documentid;
  	  $title = $myrow->title;
  		$content = $myrow->content;		
  		$userid = $myrow->userid;
  		$username = $myrow->username;
  		$date = $myrow->date;
  		$image = $myrow->image;
  		$image_pre = $myrow->image_pre;
  		$parentid = $myrow->parentid;
  		$sid = $myrow->sid;
  		$ids .= $documentid.',';
  		
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "FORUM");			
			$srNodeN -> setAttribute("URL", "/?id=".$documentid."&sid=$sid");
  		if (strlen($title)>0)
  		{
  			$srNodeN -> setAttribute("TITLE", stripslashes($title));
  		}
  		$srNodeN -> setAttribute("USERID", $userid);
  		if (strlen($username)>0)
  		{
  			$srNodeN -> setAttribute("USERNAME", $username);
  		}
  		if (strlen($image)>0){
  			$srNodeN -> setAttribute("IMAGE", $image);
  			$srNodeN -> setAttribute("IMAGE_PRE", $image_pre);
  		}
  		$srNodeN -> setAttribute("DATE", $date);
      
      if ($myrow->active == 1) {$srNodeN -> setAttribute("ACTIVE", 'TRUE');}
      else {$srNodeN -> setAttribute("ACTIVE", 'FALSE');}
      $srNodeN -> setAttribute("LAST_ANSWER_DATE", $myrow->last_answer_date);		
      $srNodeN -> setAttribute("LAST_ANSWER_ID", $myrow->last_answer_id);
      $srNodeN -> setAttribute("LAST_ANSWER_USERID", $myrow->last_answer_userid);		
      $srNodeN -> setAttribute("LAST_ANSWER_USER_NAME", $myrow->last_answer_user_name);		
      $srNodeN -> setAttribute("ANSWERS_COUNT", $myrow->answers_count);				
		}
		
	  //новые сообщения	  
    forumNews($srNode,$ids);
	}
	else
	{
		$srNode -> setAttribute("ERROR", "Неизвестный тип документа");
	}
	
	if (isset($COUNTONPAGE))
	{
		$argv = $_SERVER['QUERY_STRING'];   				
		
		$argv = RemoveParamParam ("page", $argv);		
		$argv = RemoveParamParam ("rqpath", $argv);		
		$argv = substr($argv,1);		

		$LINK = $_SERVER['PHP_SELF'];
		if (isset($_REQUEST['rqpath'])) {
		  $LINK = 'http://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['rqpath'];
		}
		
		$srNodeN = $srNode -> appendChild($resultXML->createElement("PARTS"));
		$srNodeN -> setAttribute("ACTIVE", $CURRENTPAGE);
		$srNodeN -> setAttribute("LINK", $LINK.$argv);
		$srNodeN -> setAttribute("TOTAL", $TotalPages);
		$srNodeN -> setAttribute("DOCUMENTS", $TotalMsg);
		if ($PrevPage > 0)
		{
			$srNodeN -> setAttribute("PREV", $PrevPage);
		}
		if ($NextPage > 0)
		{
			$srNodeN -> setAttribute("NEXT", $NextPage);
		}
		
		if ($PrevPage > 0)
		{
			$srNodePrev = $srNodeN -> appendChild($resultXML->createElement("PREVPART"));
			$srNodePrev -> setAttribute("CURRENTPAGE", $PrevPage);
		}
		
		for ($p=1; $p<=$TotalPages; $p++) {
  	  if (abs($p-$CURRENTPAGE)<6 || $p==1 || $p==$TotalPages) {
    		$srNodePart = $srNodeN -> appendChild($resultXML->createElement("PART"));
    		$srNodePart -> setAttribute("CURRENTPAGE", $p);
    		if ($p==$CURRENTPAGE) {
    			$srNodePart -> setAttribute("ACTIVE", "TRUE");
    			$ddd = false;
    		}
    	}
    	if (abs($p-$currentpage)>=6 && $currentpage && !$ddd) {
    	  $ddd = true;
    		$srNodePart = $srNodeN -> appendChild($resultXML->createElement("PART"));
    		$srNodePart -> setAttribute("TYPE", 'ddd');  	
    		$srNodePart -> setAttribute("p", $p);
    		$srNodePart -> setAttribute("currentpage", $currentpage);
    	}
		}
		
		if ($NextPage>0) {
			$srNodeNext = $srNodeN -> appendChild($resultXML->createElement("NEXTPART"));
			$srNodeNext -> setAttribute("CURRENTPAGE", $NextPage);
		}
	}
}

function SEARCHSECTIONPATH($srNode)
{
	global $resultXML, $db;
	global $path;
	global $sid;
	
    $attributes = $srNode->attributes;
    foreach($attributes as $domAttribute)
    {
    	if ($domAttribute->name == "SECTIONID") {$SECTIONID = $domAttribute->value;}
    	if ($domAttribute->name == "START") {$START = $domAttribute->value;}
    	if ($domAttribute->name == "SHOWSTART") {$SHOWSTART = $domAttribute->value;}
    }
    if ($SECTIONID=="CURRENT") {$SECTIONID = $sid;}
    else {$SECTIONID = intval($SECTIONID);}
    if ($SECTIONID==0) {$SECTIONID = -1;}
    
    $path = array();
    $path[] = $SECTIONID;
    pathCreate($SECTIONID,$START);
    if ($SHOWSTART=='TRUE') {$end = 1;} else {$end = 2;}

	for ($i=count($path)-$end; $i>=0; $i--)
	{
		$sql = "SELECT id, name, description, RootDocId, date, imageid, rqpath, active FROM sections WHERE id = ".$path[$i]." ORDER BY position DESC";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_row($result))
		{
			$srNodeN = $srNode -> appendChild($resultXML->createElement("SECTION"));
			$srNodeN -> setAttribute("SECTIONID", $myrow[0]);
			if (strlen($myrow[1])>0)
			{
				$srNodeN -> setAttribute("NAME", $myrow[1]);
			}
			if (strlen($myrow[2])>0)
			{
				$srNodeN -> setAttribute("DESCRIPTION", $myrow[2]);
			}
			
  		if (strlen($myrow[6])>0) {
  		  $srNodeN -> setAttribute("URL", $myrow[6]);
    		if ($myrow[3] > 0) {
    			$srNodeN -> setAttribute("URL2", "/?id=".$myrow[3]."&sid=".$myrow[0]);
    		}
  		}
  		else if ($myrow[3] > 0) {
    			$srNodeN -> setAttribute("URL", "/?id=".$myrow[3]."&sid=".$myrow[0]);
    	} 

			if (strlen($myrow[4])>0)
			{
				$srNodeN -> setAttribute("DATE", $myrow[4]);
			}
			if ($myrow[5]>0)
			{
				$srNodeN -> setAttribute("IMAGE", "/?id=".$myrow[5]);
				$srNodeN -> setAttribute("PREVIEW", "/?id=".$myrow[5]."&preview=true");
			}
			$srNodeN -> setAttribute("ACTIVE", $myrow[7]);
			$srNodeN -> setAttribute("LEVEL", $i);
		}
	}
}

function SEARCHSECTIONS($srNode)
{
	global $resultXML, $db;
	global $SHOWHIDDENSECTIONS;
	global $SCANSUBSECTIONS;
  global $LEVELDEPTH, $SECTIONQUERY;
	global $path;
	global $sid;	
	
    $attributes = $srNode->attributes;
    foreach($attributes as $domAttribute)
    {
    	if ($domAttribute->name == "SECTIONID") {$SECTIONID = $domAttribute->value;}
    	if ($domAttribute->name == "SCANSUBSECTIONS") {$SCANSUBSECTIONS = $domAttribute->value;}
    	if ($domAttribute->name == "LEVELDEPTH") {$LEVELDEPTH = (int)$domAttribute->value;}
    	if ($domAttribute->name == "SHOWHIDDENSECTIONS") {$SHOWHIDDENSECTIONS = $domAttribute->value;}
    	if ($domAttribute->name == "SECTIONQUERY") {$SECTIONQUERY = trim(mysql_real_escape_string($domAttribute->value));}
    }
    if ($SECTIONID=="CURRENT") {$SECTIONID = $sid;}
    else {$SECTIONID = intval($SECTIONID);}
    if ($SECTIONID==0) {$SECTIONID = -1;}
    
    if ($SCANSUBSECTIONS=="true" || $SCANSUBSECTIONS=="TRUE" || $SCANSUBSECTIONS=="1") {$SCANSUBSECTIONS = "1";}
    
    if (!($SHOWHIDDENSECTIONS=="true" || $SHOWHIDDENSECTIONS=="TRUE" || $SHOWHIDDENSECTIONS=="1"))
    {
    	$SHOWHIDDENSECTIONS=" and active=1";
    }
    else {$SHOWHIDDENSECTIONS="";}
    
    $path[] = $sid;
    if (isset($sid)) pathCreate($sid,1);
    $level = 1;
    ssection($srNode,$SECTIONID,$level);
}

function pathCreate($current, $end)
{
	global $path, $db;
	
	if ($current!=$end && $current!=1)
	{
		$sql = "SELECT `ancestor` FROM `sections` WHERE `id`=$current";		
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$path[] = $myrow[0];
			pathCreate($myrow[0],$end);
		}
	}
	else {return;}
}

function ssection($srNode,$SECTIONID,$level)
{	
	global $resultXML, $db, $config;
	global $SHOWHIDDENSECTIONS;
	global $SCANSUBSECTIONS;
	global $LEVELDEPTH;
	global $path;
	global $sid;
	global $SECTIONQUERY;
	
	if (strlen($SECTIONQUERY) > 0) {
    $sql = "SELECT * FROM sections WHERE active=1 AND `Name` LIKE '%$SECTIONQUERY%'";
    $SECTIONQUERY = '';      
  }	
	else {
	 $sql = "SELECT * FROM sections WHERE ancestor = ".$SECTIONID.$SHOWHIDDENSECTIONS." ORDER BY position DESC";
	}

	$result = $db->execute($sql);

	while ($myrow = mysql_fetch_object($result))
	{
		$srNodeN = $srNode -> appendChild($resultXML->createElement("SECTION"));
		$srNodeN -> setAttribute("SECTIONID", $myrow->id);
		$srNodeN -> setAttribute("NAME", $myrow->name);
		if (strlen($myrow->description)>0) {
			$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);
		}
		if (strlen($myrow->param1)>0) {
			$srNodeN -> setAttribute("PARAM1", $myrow->param1);
		}
		if (strlen($myrow->param2)>0) {
			$srNodeN -> setAttribute("PARAM2", $myrow->param2);
		}
		if (strlen($myrow->param3)>0) {
			$srNodeN -> setAttribute("PARAM3", $myrow->param3);
		}		
		
		if (strlen($myrow->rqpath)>0) {
		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
  		if ($myrow->rootdocid > 0) {
  			$srNodeN -> setAttribute("URL2", "/?id=".$myrow->rootdocid."&sid=".$myrow->id);
  		}
		}
		else if ($myrow->rootdocid > 0) {
  			$srNodeN -> setAttribute("URL", "/?id=".$myrow->rootdocid."&sid=".$myrow->id);
  	} 
		$srNodeN -> setAttribute("LEVEL", $level);
		$srNodeN -> setAttribute("DATE", $myrow->date);
		if ((int)$myrow->imageid) {
    	$sql2 = "SELECT image, preview FROM images WHERE documentid=$myrow->imageid";
    	$result2 = $db->execute($sql2);
    	if ($myrow2 = mysql_fetch_object($result2)) {
			 $srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow2->image);
			 if (strlen($myrow2->preview)>0) {$srNodeN -> setAttribute("PREVIEW", $config->upfolder.$myrow2->preview);}
			}
		}
		if (in_array($myrow->id,$path)) {
			$srNodeN -> setAttribute("ACTIVE", "TRUE");
		}
		if ($sid == $myrow->id) {
			$srNodeN -> setAttribute("CURRENT", "TRUE");
		}
		
		if ($SCANSUBSECTIONS=="1" && (!$LEVELDEPTH || ($LEVELDEPTH > 0 && $level < $LEVELDEPTH))) {
			$sql2 = "SELECT id FROM sections WHERE ancestor = ".$myrow->id.$SHOWHIDDENSECTIONS;
			$result2 = $db->execute($sql2);
			if (mysql_num_rows($result2)) {
				ssection($srNodeN,$myrow->id,$level+1);
			}
		}
		if (!$myrow->active) {
			$srNodeN -> setAttribute("HIDDEN", "TRUE");
		}
	}
}

function GOODSCOUNT($srNode)
{
  global $resultXML, $db;  
	$sql = "SELECT sid, count(*) as count FROM alldocs WHERE doctype='goods' GROUP BY sid";
	$result = $db->execute($sql);
	while ($myrow = mysql_fetch_object($result))
	{
	  $srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
		$srNodeN -> setAttribute("SECTIONID", $myrow->sid);
		$srNodeN -> setAttribute("COUNT", $myrow->count);
	}
}

function SHOPBASKET($srNode)
{
	global $resultXML,$db;
	if (isset($_COOKIE['GOODS'])) {
	  foreach ($_COOKIE['GOODS'] as $id => $value)  {
			$srNodeN = $srNode -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("TYPE", "GOODS");
			$srNodeN -> setAttribute("QUANTITY", $value);
			$srNodeN -> setAttribute("IID", $id);
			if (isset($_COOKIE['GOODSPARAMS']))
  			foreach ($_COOKIE['GOODSPARAMS'] as $paramid => $paramvalue) {
  	       if ($paramid==$id)
  			   $srNodeN -> setAttribute("PARAM", $paramvalue);
  			}

			$sql = "SELECT title, price1 FROM goods WHERE documentid = $id";
			$result = $db->execute($sql);
			if ($myrow = mysql_fetch_object($result)) {
				$srNodeN -> setAttribute("TITLE", $myrow->title);        
				$srNodeN -> setAttribute("PRICE1", $myrow->price1);				
			}
	  }
	}
}

function BASKETFORM($srNode) {
	global $resultXML,$db, $config;
	global $globals;
		
	$srNode -> setAttribute("min_order", $config->min_order);
  $srNode -> setAttribute("pochta_min", $config->pochta_min);
  $srNode -> setAttribute("pochta_over_10", $config->pochta_over_10);
  $srNode -> setAttribute("courier_min", $config->courier_min);
  $srNode -> setAttribute("courier_over_10", $config->courier_over_10);
  $srNode -> setAttribute("balls_percent", $config->balls_percent);  

	if (isset($_REQUEST['action']) && $_REQUEST['action']='set') {
    $nextid = (int)$_REQUEST['nextid'];      
    header("Location: /?id=$nextid");
	}	

	$srNode -> setAttribute("IID", $globals['id']);	
	$sum = 0;
	if (isset($_COOKIE['GOODS'])) {
	    foreach ($_COOKIE['GOODS'] as $id => $value)  {
  			$srNodeN = $srNode -> appendChild($resultXML->createElement("FIELD"));
  			$srNodeN -> setAttribute("TYPE", "GOODS");
  			$srNodeN -> setAttribute("QUANTITY", $value);
  			$srNodeN -> setAttribute("IID", $id);
  			if (isset($_COOKIE['GOODSPARAMS']))
    			foreach ($_COOKIE['GOODSPARAMS'] as $paramid => $paramvalue) {
    	       if ($paramid==$id)
    			   $srNodeN -> setAttribute("PARAM", $paramvalue);
    			}
  
        $sql = "SELECT * FROM goods INNER JOIN alldocs ON goods.documentid = alldocs.documentid  
                WHERE goods.documentid = $id";
  			$result = $db->execute($sql);
  			if ($myrow = mysql_fetch_object($result)) {
      		if (strlen($myrow->rqpath)) {
      		  $srNodeN -> setAttribute("URL", $myrow->rqpath);
      		} else {		
    			  $srNodeN -> setAttribute("URL", "/?id=$id&sid=$myrow->sid");
          }			
  			
  				$srNodeN -> setAttribute("TITLE", $myrow->title);
  				$srNodeN -> setAttribute("IMAGE", $config->upfolder.'multy_'.$myrow->image);        				
          
  				$srNodeN -> setAttribute("PRICE1", $myrow->price1);
  				$srNodeN -> setAttribute("PRICE_FORMAT", number_format($myrow->price1, 0, ',', ' '));
  				
  				$srNodeN -> setAttribute("CODE", $myrow->code);
  				$sum += $myrow->price1*$value;
  				
  				if (strlen($myrow->scale)) {$srNodeN -> setAttribute("SCALE", $myrow->scale);}
  				if (strlen($myrow->mark)) {$srNodeN -> setAttribute("MARK", $myrow->mark);}
  				if (strlen($myrow->manuf)) {$srNodeN -> setAttribute("MANUF", $myrow->manuf);}
      		if (strlen($myrow->param1)) {$srNodeN -> setAttribute("PARAM1", $myrow->param1);}
      		if (strlen($myrow->param2)) {$srNodeN -> setAttribute("PARAM2", $myrow->param2);}
      		if (strlen($myrow->param3)) {$srNodeN -> setAttribute("PARAM3", $myrow->param3);}
      		if (strlen($myrow->param4)) {$srNodeN -> setAttribute("PARAM4", $myrow->param4);}
      		if (strlen($myrow->param5)) {$srNodeN -> setAttribute("PARAM5", $myrow->param5);}
      		if (strlen($myrow->param6)) {$srNodeN -> setAttribute("PARAM6", $myrow->param6);}
      		if (strlen($myrow->param7)) {$srNodeN -> setAttribute("PARAM7", $myrow->param7);}
      		if (strlen($myrow->param8)) {$srNodeN -> setAttribute("PARAM8", $myrow->param8);}
      		if (strlen($myrow->param9)) {$srNodeN -> setAttribute("PARAM9", $myrow->param9);}
      		if (strlen($myrow->param10)) {$srNodeN -> setAttribute("PARAM10", $myrow->param10);}				
  			}
	    }
	}
	$srNode -> setAttribute("SUM", $sum);
}

function FORM($srNode,$srNodeFix,$attr)
{
	global $resultXML,$db;
  global $globals;
  	
	$documentid = $globals['id'];
	$sql = "SELECT handler FROM `form` WHERE documentid=$documentid LIMIT 1";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_row($result))
	{
		$handler = trim($myrow[0]);
	}	
	
	$handler = "handlers/".$handler;
	
	include($handler);
}

function PHP($srNode,$srNodeFix,$attr)
{
	global $resultXML,$db,$user,$sid;
	
	$file = getAttribute("FILE", $attr);
	
	$php_param = '';
	$params = getAttribute("PARAMS", $attr);	
	if (strlen($params)>0) {
    $params = split('=',$params);
  }
  if (is_array($params)) {
    $php_param = $params[1];
  }

	$handler = "handlers/".$file;
	include $handler;
}

function FORUM_SECTION_LIST($srNode)
{
  global $resultXML,$db;
	global $sid;
	$FORUMSCOUNT = 5; //выгребка форумов на каждый раздел. значение по-умолчанию
	$ids = '';
	
  $attributes = $srNode->attributes;
  foreach($attributes as $domAttribute) {
  	if ($domAttribute->name == "SECTIONID") {$SECTIONID = (int)$domAttribute->value;}
  	if ($domAttribute->name == "FORUMSCOUNT") {$FORUMSCOUNT = (int)$domAttribute->value;}
  }
  if ($SECTIONID=="CURRENT") {$SECTIONID = $sid;}
  else {$SECTIONID = intval($SECTIONID);}
  if ($SECTIONID==0) {$SECTIONID = -1;}
    
 	$sql = "SELECT * FROM sections WHERE ancestor = $SECTIONID ORDER BY position DESC";
	$result = $db->execute($sql);
	while ($myrow = mysql_fetch_object($result))
	{
		$srNodeN = $srNode -> appendChild($resultXML->createElement("FORUMSECTION"));
		$srNodeN -> setAttribute("SECTIONID", $myrow->id);
		if (strlen($myrow->rqpath)>0) {
		  $srNodeN -> setAttribute("URL", "/".$myrow->rqpath);
  		if ($myrow->RootDocId > 0) {
  			$srNodeN -> setAttribute("URL2", "/?id=".$myrow->RootDocId."&sid=".$myrow->id);
  		}
		}
		else if ($myrow->RootDocId > 0) {
  			$srNodeN -> setAttribute("URL", "/?id=".$myrow->RootDocId."&sid=".$myrow->id);
  	}		
		$srNodeN -> setAttribute("NAME", $myrow->Name);
		$srNodeN -> setAttribute("DESCRIPTION", $myrow->description);
		$srNodeN -> setAttribute("IMAGEID", $myrow->imageid);
	  
    $sql2 = "SELECT count(*) as count FROM alldocs WHERE doctype = 'forum' AND sid=$myrow->id";
	  $result2 = $db->execute($sql2);
	  if ($myrow2 = mysql_fetch_object($result2)) {
	     $COUNT = $myrow2->count;
	  }
	  $srNodeN -> setAttribute("COUNT", $COUNT);
	  
	  $sql2 = "SELECT * FROM forum INNER JOIN alldocs ON alldocs.documentid = forum.documentid AND alldocs.sid=$myrow->id ORDER BY forum.last_answer_id DESC LIMIT $FORUMSCOUNT";
	  $result2 = $db->execute($sql2);
	  while ($myrow2 = mysql_fetch_object($result2)) {
      $srNodeForum = $srNodeN -> appendChild($resultXML->createElement("DOCUMENT"));
      $srNodeForum -> setAttribute("IID", $myrow2->documentid);			
      $srNodeForum -> setAttribute("URL", "/?id=".$myrow2->documentid."&sid=$myrow2->sid");
      $srNodeForum -> setAttribute("TITLE", stripslashes($myrow2->title));
      $srNodeForum -> setAttribute("USERID", $myrow2->userid);
      $srNodeForum -> setAttribute("USERNAME", $myrow2->username);      
      $srNodeForum -> setAttribute("LAST_ANSWER_DATE", $myrow2->last_answer_date);		
      $srNodeForum -> setAttribute("LAST_ANSWER_ID", $myrow2->last_answer_id);
      $srNodeForum -> setAttribute("LAST_ANSWER_USERID", $myrow2->last_answer_userid);		
      $srNodeForum -> setAttribute("LAST_ANSWER_USER_NAME", $myrow2->last_answer_user_name);		
      $srNodeForum -> setAttribute("ANSWERS_COUNT", $myrow2->answers_count);
      $ids .= $myrow2->documentid.',';				
	  }	  
	}
  //новые сообщения	  
  forumNews($srNode,$ids);
}

function forumNews($srNode,$ids)
{
  global $resultXML,$db;
  
	$stateid = "";
	$user_id = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = $_COOKIE["stateid"];
	}
	if (strlen($stateid)>0) {
    $sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {
		  $user_id = $myrow->documentid;
		}
		if ($user_id>0) {      
		  if (strlen($ids)==0) $ids = '1';
		  else $ids = substr($ids,0,-1); //убрали запятую в конце лишнюю
  	  $srNodeNew = $srNode -> appendChild($resultXML->createElement("NEWS"));
  	  $sql = "SELECT forum.ownerid as tema_id, count(*) as count FROM forum
              INNER JOIN forum_reading ON forum.documentid = forum_reading.tema_id
              OR (forum.ownerid = forum_reading.tema_id AND forum.ownerid IN ($ids))
              WHERE forum.last_answer_id > forum_reading.last_read_id AND forum_reading.user_id=$user_id
              AND forum.ownerid !=0 
              GROUP BY forum.ownerid
              UNION ALL
              SELECT documentid as tema_id, answers_count as count FROM forum
              WHERE documentid IN ($ids)
              AND documentid NOT IN (SELECT tema_id FROM forum_reading WHERE user_id=$user_id);";        
      $result = $db->execute($sql);
      while ($myrow = mysql_fetch_object($result)) {
        $srNodeNewCount = $srNodeNew -> appendChild($resultXML->createElement("TEMA"));
        $srNodeNewCount -> setAttribute("ID", $myrow->tema_id);
        $srNodeNewCount -> setAttribute("COUNT", $myrow->count);
      }
		}
	}   
}

function fullDocument($doctype,$id)
{
	$result = new DOMDocument('1.0');

	if ($doctype=='material')
	{
		$result = fullMaterial($id);
	}
	elseif ($doctype=='multydoc')
	{
		$result = fullMultydoc($id);
	}	
	elseif ($doctype=='sport')
	{
		$result = fullSport($id);
	}	
	elseif ($doctype=='banner')
	{
		$result = fullBanner($id);
	}
	elseif ($doctype=='goods')
	{
		$result = fullGoods($id);
	}
	elseif ($doctype=='orders')
	{
		$result = fullOrders($id);
	}
	elseif ($doctype=='user')
	{
		$result = fulluser($id);
	}
	elseif ($doctype=='forum')
	{
		$result = fullForum($id);
	}
	elseif ($doctype=='simple')
	{
		$result = fullSimple($id);
	}
	elseif ($doctype=='question')
	{
		$result = fullQuestion($id);
	}
	elseif ($doctype=='logo')
	{
		$result = fullLogo($id);
	}	
	elseif ($doctype=='mailform')
	{
		$result = fullMailForm($id);
	}	
	else
	{
		return false;
	}
	
	return $result;
}

function INSERTDOCUMENT($srNode,$id)
{
	global $resultXML;
	$doctype=getDocType($id);	
	$insertXML = new DOMDocument('1.0');
	$insertXML = fullDocument($doctype,$id);
  if ($insertXML->firstChild){	
	 $new_child = $resultXML->importNode($insertXML->firstChild,true);            
  	$srNode -> appendChild($new_child);
  }
}

function CBR($srNode,$currency)
{
	global $resultXML;
	
  $course = new Valute();
  //$valute = $course->getCourse($currency);
  switch ($currency) {
      case 'dollar':
          $valute = $course->dollar;
          break;
      case 'euro':
          $valute = $course->euro;
          break;
      case 'rub':
          $valute = $course->rub;
          break;
      default:
          $valute = $course->rub;
          break;
  } 
	
  $date = date("d.m.Y");
  
  $curr = 'rub';
  if (isset($_COOKIE['currency'])) {
    $curr = $_COOKIE['currency'];
  }  
  if ($curr == $currency) $srNode -> setAttribute("ACTIVE", 'TRUE');
   
  $srNode -> setAttribute("VALUE", $valute);	
  $srNode -> setAttribute("DATE", $date);  
}

function REDIRECTDOWN()
{
	global $sid,$db;
	
	$sql = "SELECT id, rootdocid, rqpath FROM `sections` WHERE ancestor=$sid AND active=1 ORDER BY position DESC";
	//die($sql);
	$result = $db->execute($sql);
	while ($myrow = mysql_fetch_object($result))
	{		
	  if (strlen($myrow->rqpath)==0) {
  		$rid = $myrow->RootDocId;
  		if ($rid>0) {
        $rsid = $myrow->id;
        break;		
  		}
  	}
  	else {$rqpath = $myrow->rqpath; break;}
	}	
		
	if (strlen($rqpath)>0)
	 $rpath = $rqpath;
  else 
	 $rpath = "$_SERVER[PHP_SELF]?id=$rid&sid=$rsid";
	 
	header("Location: $rpath");
}

function processXML($domNode, $resultNode)
{
	global $resultXML, $fixResultNode, $id;
	
	if($domNode)
	{   
		if($domNode->nodeType == 1)
		{
		  {
		  	  $fixResultNode = $resultNode -> appendChild($resultXML->createElement($domNode->nodeName));
		      if($domNode->attributes)
		      {
		        $attributes = $domNode->attributes;
		        foreach($attributes as $domAttribute)
		        {
		        	$avalue = $domAttribute->value;
		        	if (strpos($avalue,"##")!==false) {
		        	  if ($avalue == '##id##') {
                  $avalue = $id;
                } else { 
		        		  $avalue = getParamFromUrl($avalue);
		        		}
		        	}
					    $fixResultNode -> setAttribute($domAttribute->name, urldecode(urlencode($avalue)));
		        }
		      }
			  if ($domNode->nodeName == "SEARCHRESULT") 
			  {
			  	SEARCHRESULT($fixResultNode);			  	
			  }
			  else
			  if ($domNode->nodeName == "SEARCHSECTIONS") 
			  {
			  	SEARCHSECTIONS($fixResultNode);			  	
			  }
			  else
			  if ($domNode->nodeName == "SEARCHSECTIONPATH") 
			  {
			  	SEARCHSECTIONPATH($fixResultNode);			  	
			  }
			  else
			  if ($domNode->nodeName == "INSERTDOCUMENT") 
			  {
			  	INSERTDOCUMENT($fixResultNode,getAttribute("IID", $domNode->attributes));
			  }
			  else
			  if ($domNode->nodeName == "CBR") 
			  {
			  	CBR($fixResultNode,getAttribute("CURRENCY", $domNode->attributes));
			  }
			  else			  
			  if ($domNode->nodeName == "REDIRECTDOWN") 
			  {
					REDIRECTDOWN();
			  }			  
			  else
			  if ($domNode->nodeName == "SHOPBASKET") 
			  {
			  	SHOPBASKET($fixResultNode);
			  }
			  else
			  if ($domNode->nodeName == "BASKETFORM") 
			  {
			  	BASKETFORM($fixResultNode);
			  }
			  else
			  if ($domNode->nodeName == "FORM") 
			  {			  
			  	FORM($domNode,$fixResultNode,$domNode->attributes);
			  }
			  else
			  if ($domNode->nodeName == "PHP") 
			  {
			  	PHP($domNode,$fixResultNode,$domNode->attributes);
			  }
			  else
			  if ($domNode->nodeName == "GOODSCOUNT") 
			  {			  
			  	GOODSCOUNT($fixResultNode);
			  }
			  else if ($domNode->nodeName == "FORUM_SECTION_LIST") 
			  {
			  	FORUM_SECTION_LIST($fixResultNode);			  	
			  }
		      if($domNode->childNodes)
		      {
		        $nextNode = $domNode->firstChild;
		        processXML($nextNode,$fixResultNode);
		      }
		   }                 
		}   		
		else
		{
			$resultNode -> appendChild($resultXML->createTextNode($domNode->nodeValue));
		}
		
		$nextNode = $domNode->nextSibling;
		processXML($nextNode,$resultNode);
	}
}

function getDocType($id)
{
  global $db;
	$sql = "SELECT doctype FROM alldocs WHERE documentid=$id";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_row($result))
	{
		return $myrow[0];
	}
}

function fullMultydoc($id) {
  global $db, $config, $currentRegion;
  $sql = "SELECT * FROM mmaterial WHERE mrid=$id AND regionid=$currentRegion";
	$result = $db->execute($sql);
	$resultXML = new DOMDocument('1.0');	
	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
	if ($myrow = mysql_fetch_object($result)) {
		$resultRoot -> setAttribute("TYPE", 'MATERIAL');			
		$resultRoot -> setAttribute("TITLE", $myrow->title);		
		if (strlen($myrow->keywords)){
			$resultRoot -> setAttribute("KEYWORDS", $myrow->keywords);
		}
		if (strlen($myrow->metadescription)){
			$resultRoot -> setAttribute("METADESCRIPTION", $myrow->metadescription);
		}
		if (strlen($myrow->pagetitle)){
			$resultRoot -> setAttribute("PAGETITLE", $myrow->pagetitle);
		}
		if (strlen($date)){
			$resultRoot -> setAttribute("DATE", $myrow->date);
		}
		$new_child = $resultXML -> createCDATASection($myrow->content);
		$resultRoot -> appendChild($new_child);		              
  } else {
    $sql = "SELECT * FROM multydoc WHERE documentid=$id";
  	$result = $db->execute($sql);
    if ($myrow = mysql_fetch_object($result)) {
      $resultRoot -> setAttribute("TITLE", $myrow->title);			  
  		$new_child = $resultXML -> createCDATASection("Информации по данному региону не найдено.");
  		$resultRoot -> appendChild($new_child);		      
    }    
  }
  $sql = "SELECT * FROM multydoc WHERE documentid=$id";
	$result = $db->execute($sql);  
	if ($myrow = mysql_fetch_object($result)) {
	 $resultRoot -> setAttribute("BOTTOM", $myrow->content);
	}
	
  return $resultXML;
}

function fullMaterial($id)
{
  global $db, $config;
	$sql = "SELECT * FROM materials WHERE documentid=$id";	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result))
	{
		$content = $myrow->content;
		$title = stripslashes($myrow->title);
		$description = $myrow->description;
		$keywords = $myrow->keywords;
		$metadescription = $myrow->metadescription;
		$PageTitle = $myrow->pagetitle;
		$Date = $myrow->Date;
		$ImageId = $myrow->imageid;
		$date = $myrow->date;
		$info = $myrow->info;
		$file = $myrow->file;
	}

	$resultXML = new DOMDocument('1.0');	
	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
		$resultRoot -> setAttribute("IID", $id);
		$resultRoot -> setAttribute("TYPE", 'MATERIAL');			
		$resultRoot -> setAttribute("TITLE", $title);		
		if (strlen($description)){
			$resultRoot -> setAttribute("DESCRIPTION", $description);
		}
		if (strlen($keywords)){
			$resultRoot -> setAttribute("KEYWORDS", $keywords);
		}
		if (strlen($metadescription)){
			$resultRoot -> setAttribute("METADESCRIPTION", $metadescription);
		}
		if (strlen($PageTitle)){
			$resultRoot -> setAttribute("PAGETITLE", $PageTitle);
		}
		if (strlen($date)){
			$resultRoot -> setAttribute("DATE", $date);
		}
		if (strlen($info)) {
			$resultRoot -> setAttribute("INFO", $info);
		}
		if (strlen($file)) {
			$resultRoot -> setAttribute("FILE", $config->upfolder.$file);
		}		
		if ($ImageId>0) {
    	$sql2 = "SELECT preview, image, bigimage FROM images WHERE documentid = $ImageId";
    	$result2 = $db->execute($sql2);
    	if ($myrow2 = mysql_fetch_object($result2)) {
    	   $resultRoot -> setAttribute("IMAGE", "/admingo/".$myrow2->image);
    	   if (strlen($myrow2->preview)>0) $resultRoot -> setAttribute("PREVIEW", "/admingo/".$myrow2->preview);
    	   if (strlen($myrow2->bigimage)>0) $resultRoot -> setAttribute("BIGIMAGE", "/admingo/".$myrow2->bigimage);    		
    	}			
		}
		if (strlen($userdate)>0)
		{
			$resultRoot -> setAttribute("USERDATE", $userdate);
		}

		$new_child = $resultXML -> createCDATASection($content);
		$resultRoot -> appendChild($new_child);		  

		return $resultXML;
}

function fullSport($id) {
  global $db, $config;
	$sql = "SELECT * FROM sport WHERE documentid=$id";	
	$result = $db->execute($sql);	

  if ($myrow = mysql_fetch_object($result)) {
  	$resultXML = new DOMDocument('1.0');	
  	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
  		$resultRoot -> setAttribute("IID", $id);
  		$resultRoot -> setAttribute("TYPE", 'SPORT');			
  		if (strlen($myrow->title)) {
  			$resultRoot -> setAttribute("TITLE", $myrow->title);
  		}
  		if (strlen($myrow->image)){
  			$resultRoot -> setAttribute("IMAGE", $config->upfolder.$myrow->image);
  		}  		
  		if (strlen($myrow->icon)){
  			$resultRoot -> setAttribute("ICON", $config->upfolder.$myrow->icon);
  		}        		
  		if (strlen($myrow->keywords)){
  			$resultRoot -> setAttribute("KEYWORDS", $myrow->keywords);
  		}
  		if (strlen($myrow->metadescription)){
  			$resultRoot -> setAttribute("METADESCRIPTION", $myrow->metadescription);
  		}
  		if (strlen($myrow->pagetitle)){
  			$resultRoot -> setAttribute("PAGETITLE", $myrow->pagetitle);
  		}  		
  		$resultRoot -> setAttribute("PAGETYPE", $_REQUEST['type']);
  		if (isset($_REQUEST['type']) && $_REQUEST['type']=='gallery') {  		    		
      	$sql2 = "SELECT * FROM image_owner WHERE docid=$id";	
      	$result2 = $db->execute($sql2);  		  
      	while ($myrow2 = mysql_fetch_object($result2)) {      
        	$srNodeN = $resultRoot -> appendChild($resultXML->createElement("PHOTO"));
      		$srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow2->image);
          $srNodeN -> setAttribute("IMAGE_SMALL", $config->upfolder.'small_'.$myrow2->image);
      		if (strlen($myrow2->title)) {
      			$srNodeN -> setAttribute("TITLE", $myrow2->title);
      		}                		      	
      	}
  		} else {  
    		$new_child = $resultXML -> createCDATASection($myrow->content);
    		$resultRoot -> appendChild($new_child);		  
    	}
  }
	return $resultXML;
}

function fullOrders($id)
{
  global $db;
	$sql = "SELECT title, content, date FROM orders WHERE documentid=$id";	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result))
	{
		$resultXML = new DOMDocument('1.0');	
		$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
		$resultRoot -> setAttribute("IID", $id);
		$resultRoot -> setAttribute("TYPE", 'ORDERS');			
		if (strlen($myrow->title)>0)
		{
			$resultRoot -> setAttribute("TITLE", $myrow->title);
		}
		if (strlen($myrow->date)>0)
		{
			$resultRoot -> setAttribute("DESCRIPTION", $myrow->date);
		}

		$xml = new DOMDocument('1.0');
		$xml->loadXML($myrow->content);
		$new_child = $resultXML -> importNode($xml->firstChild, true);   
		$resultRoot -> appendChild($new_child);  
		
		return $resultXML;
	}
}

function fullBanner($id) {
  global $db, $config;
	$sql = "select * FROM banners WHERE documentid=$id";	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
		$title = $myrow->title;
		$type = $myrow->type;
		$file = $myrow->file;
		$width = $myrow->width;
		$height = $myrow->height;
		$link = $myrow->link;
		$linkdocid = $myrow->linkdocid;
	}

	$resultXML = new DOMDocument('1.0');	
	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
		$resultRoot -> setAttribute("IID", $id);
		$resultRoot -> setAttribute("TYPE", 'BANNER');
    
		$resultRoot -> setAttribute("TITLE", $myrow->title);
		$resultRoot -> setAttribute("BAN_TYPE", $type);  			
		if (strlen($myrow->file)) {
			$resultRoot -> setAttribute("FILE", $config->upfolder.$file);
		}
		if (strlen($link)>0) {
			$resultRoot -> setAttribute("LINK", $link);
		}
		if ($width > 0) {
		 $resultRoot -> setAttribute("WIDTH", $width);
		}
		if ($height > 0) {
		 $resultRoot -> setAttribute("HEIGHT", $height);
		}
		return $resultXML;
}

function fullLogo($id)
{
  global $db;
	$sql = "select * FROM logo WHERE documentid=$id";	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result))
	{
		$title = $myrow->title;
		$image = $myrow->image;
		$description = $myrow->description;
		$email = $myrow->email;
		$phone = $myrow->phone;
		$icq = $myrow->icq;
		$skype = $myrow->skype;
		$counter = $myrow->counter;
	}

	$resultXML = new DOMDocument('1.0');	
	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
		$resultRoot -> setAttribute("IID", $id);
		$resultRoot -> setAttribute("TYPE", 'LOGO');
    
		$resultRoot -> setAttribute("TITLE", $myrow->title);
		if (strlen($image)) {
		 $resultRoot -> setAttribute("IMAGE", "/admingo/".$image);
		}
		if (strlen($description)) {$resultRoot -> setAttribute("DESCRIPTION", $description);}        		
		if (strlen($email)) {$resultRoot -> setAttribute("EMAIL", $email);}
		if (strlen($phone)) {$resultRoot -> setAttribute("PHONE", $phone);}
		if (strlen($icq)) {$resultRoot -> setAttribute("ICQ", $icq);}
		if (strlen($skype)) {$resultRoot -> setAttribute("SKYPE", $skype);}
		if (strlen($counter)) {$resultRoot -> setAttribute("COUNTER", $counter);}
		
		return $resultXML;
}

function fullMailForm($id)
{
  global $db;
	$sql = "select * FROM mailform WHERE documentid=$id";	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result))
	{
		$title = $myrow->title;
		$email = $myrow->email;
		$description = $myrow->description;
	}

	$resultXML = new DOMDocument('1.0');	
	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
		$resultRoot -> setAttribute("IID", $id);
		$resultRoot -> setAttribute("TYPE", 'MAILFORM');
    
		$resultRoot -> setAttribute("TITLE", $title);
		$resultRoot -> setAttribute("EMAIL", $email);
		if (strlen($description)) {
		 $resultRoot -> setAttribute("DESCRIPTION", $description);
		}       	
		
		return $resultXML;
}

function fulluser($id)
{
  global $db, $config, $srNodeList, $resultXML, $user;
	$sql = "SELECT *, (accesstime + INTERVAL 10 MINUTE > now()) AS online FROM user WHERE documentid=$id";	
	$result = $db->execute($sql);

	$resultXML = new DOMDocument('1.0');	
	$srNodeN = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));
  if (isset($_REQUEST['type'])) $srNodeN -> setAttribute("PAGETYPE", $_REQUEST['type']);	
	if ($myrow = mysql_fetch_object($result)) {
		$srNodeN -> setAttribute("IID", $myrow->documentid);
		$srNodeN -> setAttribute("TYPE", "user");
		$srNodeN -> setAttribute("STATUS", $myrow->status);
		$srNodeN -> setAttribute("EMAIL", $myrow->email);
		$srNodeN -> setAttribute("PHONE", $myrow->phone);		
    if (strlen($myrow->purpose)) {$srNodeN -> setAttribute("PURPOSE", $myrow->purpose);}    	
    if (strlen($myrow->prepare)) {$srNodeN -> setAttribute("PREPARE", $myrow->prepare);}
    if (strlen($myrow->idol)) {$srNodeN -> setAttribute("IDOL", $myrow->idol);}
		$srNodeN -> setAttribute("TITLE", $myrow->title);
		if (strlen($myrow->name)) {
		  $srNodeN -> setAttribute("NAME", $myrow->name);
    }				
    if (strlen($myrow->surname)) {
		  $srNodeN -> setAttribute("SURNAME", $myrow->surname);
		}
		if (strlen($myrow->dream)) {
			$srNodeN -> setAttribute("DREAM", $myrow->dream);
			$srNodeN -> setAttribute("DREAM_EDIT", br2rn($myrow->dream,ENT_QUOTES));
		}
		if (strlen($myrow->site)) {
      if (strpos($myrow->site,'http://') === false) $srNodeN -> setAttribute("SITE_LINK", 'http://'.$myrow->site);
          else $srNodeN -> setAttribute("SITE_LINK", $myrow->site);		
			$srNodeN -> setAttribute("SITE", $myrow->site);
		}
		if (strlen($myrow->phone)) {
			$srNodeN -> setAttribute("PHONE", $myrow->phone);
		}				
		$srNodeN -> setAttribute("CITY", $myrow->city);
		$srNodeN -> setAttribute("HEIGHT", (int)$myrow->height);
		$srNodeN -> setAttribute("WEIGHT", (int)$myrow->weight);
		if (strlen($myrow->sport)) {
		  $sports = array();
		  $sports = preg_split('/,/',$myrow->sport);
		  foreach ($sports AS $sport) {
		    $srNodsS = $srNodeN -> appendChild($resultXML->CreateElement("SPORT"));
		    $srNodsS -> setAttribute("IID", (int)$sport);
		  }
		}
		if ((int)$myrow->notice_iam) $srNodeN -> setAttribute("NOTICE_IAM", "TRUE");
		if ((int)$myrow->notice_team) $srNodeN -> setAttribute("NOTICE_TEAM", "TRUE");
		if ((int)$myrow->notice_russia) $srNodeN -> setAttribute("NOTICE_RUSSIA", "TRUE");
    				
		if (strlen($myrow->birthday) && $myrow->birthday!='0000-00-00 00:00:00') {
			$srNodeN -> setAttribute("BIRTHDAY", $myrow->birthday);
      $day = substr($myrow->birthday,8,2);
      $month = substr($myrow->birthday,5,2);
      $year = substr($myrow->birthday,0,4);
      $bdayunix = mktime (0, 0, 0, $month, $day, $year);
      $nowunix = time(); 
      $ageunix = $nowunix - $bdayunix; 
      $age = floor($ageunix/(365.25*24*60*60)); 
      $srNodeN -> setAttribute("AGE", $age);
		}
		if (strlen($myrow->image)) {
		  $srNodeN -> setAttribute("IMAGE_EDIT", $myrow->image);
		  $srNodeN -> setAttribute("IMAGE", $config->upfolder.$id.'/'.$myrow->image);
			$srNodeN -> setAttribute("BIG_IMAGE", $config->upfolder.$id.'/big_'.$myrow->image);
		} else {
		  $srNodeN -> setAttribute("IMAGE", '/images/no-ava.png');
			$srNodeN -> setAttribute("BIG_IMAGE", '/images/no-ava-856.png');		
		}
		$srNodeN -> setAttribute("SEX", $myrow->sex);		
		if ((int)$myrow->online) $srNodeN -> setAttribute("ONLINE", 'TRUE');
		$srNodeN -> setAttribute("ACCESSTIME", $myrow->accesstime);

		$srNodeN -> setAttribute("PTYPE", $_REQUEST['type']);
		
    $sql2 = "SELECT teamid, team.title, team.image, team.userid FROM team_user
            INNER JOIN team ON  team_user.teamid = team.id
            WHERE team_user.userid=$id ORDER BY role LIMIT 1;";
  	$result2 = $db->execute($sql2);        	
  	if ($myrow2 = mysql_fetch_object($result2)) {
  	  $teamid =  $myrow2->teamid;
      $srNodeT = $srNodeN -> appendChild($resultXML->CreateElement('TEAM'));  			      
      $srNodeT -> setAttribute("IID", $teamid);
      $srNodeT -> setAttribute("TITLE", $myrow2->title);
      $srNodeT -> setAttribute("URL", '/team/'.$teamid.'/');
			if (strlen($myrow2->image)) {
			 $srNodeT -> setAttribute("MULTY_IMAGE", $config->upfolder.$myrow2->userid.'/multy_'.$myrow2->image);  			 
			} else {
       $srNodeT -> setAttribute("MULTY_IMAGE", '/images/noava-team-multy.jpg');			 
			}
      $sql3 = "SELECT *, user.image FROM team_user
               INNER JOIN user ON user.documentid = team_user.userid 
               WHERE teamid=$teamid AND (role='Главный тренер' OR role='Капитан');";   		
    	$result3 = $db->execute($sql3);        	
    	while ($myrow3 = mysql_fetch_object($result3)) {
    	  $srNodeU = $srNodeT -> appendChild($resultXML->CreateElement('USER'));
        $srNodeU -> setAttribute("USERID", $myrow3->userid);
        $srNodeU -> setAttribute("USERNAME", $myrow3->username);
        $srNodeU -> setAttribute("ROLE", $myrow3->role);
  			if (strlen($myrow3->image)) {
  			 $srNodeU -> setAttribute("IMAGE", $config->upfolder.$myrow3->userid.'/multy_'.$myrow3->image);
  			} else {
  			 $srNodeU -> setAttribute("IMAGE", '/images/noava-multy.jpg');
  			}    	 
    	}
    }      		
		
		if ($_REQUEST['type']=='training') {
      if (strlen($myrow->training)) {		  
		    $srNodeN -> setAttribute("TRAINING", $myrow->training);
		  }
		}	else if ($_REQUEST['type']=='blog') {
		  if (isset($_REQUEST['recordid']) && (int)$_REQUEST['recordid']) {
		    $recordid = (int)$_REQUEST['recordid'];
      	$srNodeBR = $srNodeN -> appendChild($resultXML->CreateElement('BLOG_RECORD'));
      	$srNodeBR -> setAttribute("LISTURL", '/?id='.$id.'&type=blog');
      	$prev = 0; 
      	$next = 0; 
      	$found = false;
        $sql = "SELECT * FROM `blog` WHERE ownerid=$id AND ownertype='user' ORDER BY id DESC";   		
      	$result = $db->execute($sql);      
      	while ($myrow = mysql_fetch_object($result)) {
          if ($recordid == $myrow->id) {
            $srNodeBR -> setAttribute("IID", $myrow->id);            
            $srNodeBR -> setAttribute("USERID", $myrow->userid);
            $srNodeBR -> setAttribute("USERNAME", $myrow->username);
            $srNodeBR -> setAttribute("TITLE", $myrow->title);
            $srNodeBR -> setAttribute("CONTENT", $myrow->content);
            $srNodeBR -> setAttribute("DATE", $myrow->date);          	  
            $srNodeBR -> setAttribute("VIEWS", $myrow->views);
            $found = true;
          } else {
            if (!$found) $prev = $myrow->id;
            else {$next = $myrow->id; break;}
          }
        }
        if ((int)$prev) $srNodeBR -> setAttribute("PREVURL", '/?id='.$id.'&type=blog&recordid='.$prev);              
        if ((int)$next) $srNodeBR -> setAttribute("NEXTURL", '/?id='.$id.'&type=blog&recordid='.$next);
        
        $currentUserId = $user->id;
        if ($currentUserId) {
          $sql = "SELECT * FROM `likes` 
                  WHERE ownerid = $recordid AND userid = $currentUserId AND `ownertype`='user'";  		
        	$result = $db->execute($sql);      
        	if (mysql_num_rows($result)) {
        	 $srNodeBR -> setAttribute("LIKE", "TRUE");
        	}        	
        }
        $sql = "SELECT count(1) AS `count` FROM `likes` WHERE ownerid = $recordid AND `ownertype`='user'";  		
      	$result = $db->execute($sql);      
      	if ($myrow = mysql_fetch_object($result)) {
      	 $srNodeBR -> setAttribute("LIKECOUN", $myrow->count);
      	}                
        
        $sql = "UPDATE blog SET views = views+1 WHERE id=$recordid";
        $db->execute($sql, false);		    
		  } else {
      	$srNodeList = $srNodeN -> appendChild($resultXML->CreateElement('BLOG'));
        $sql = "SELECT count(1) as `count` FROM `blog` WHERE ownerid=$id AND ownertype='user' ORDER BY id DESC";      		
    	  $pages = new Paging();
    	  $limits = $pages->makePages($config->countonpage, $sql);
    	  $limit = "LIMIT $limits[start], $limits[count]";    	
        $sql = "SELECT * FROM `blog` WHERE ownerid=$id AND ownertype='user' ORDER BY id DESC $limit";   		
      	$result = $db->execute($sql);      
      	while ($myrow = mysql_fetch_object($result)) {
          $srNodeB = $srNodeList -> appendChild($resultXML->CreateElement('RECORD'));
          $srNodeB -> setAttribute("IID", $myrow->id);
          $srNodeB -> setAttribute("URL", '?id='.$id.'&type=blog&recordid='.$myrow->id);
          $srNodeB -> setAttribute("USERID", $myrow->userid);
          $srNodeB -> setAttribute("USERNAME", $myrow->username);
          $srNodeB -> setAttribute("TITLE", $myrow->title);
          $srNodeB -> setAttribute("CONTENT", $myrow->content);
          $srNodeB -> setAttribute("DATE", $myrow->date);          	  
          $srNodeB -> setAttribute("VIEWS", $myrow->views);
          if (strpos($myrow->content,'<hr />')!== false) {
            $description = substr($myrow->content,0,strpos($myrow->content,'<hr />'));
            $description = iconv("UTF-8", "UTF-8", $description).'...';
            $srNodeB -> setAttribute("SHORT", "TRUE");
          } else if (strlen($myrow->content)<1000) {
            $description = $myrow->content;
          } else {
            $description = substr($myrow->content,0,1000);
            $description = substr($description, 0, strrpos($description,' '));
            $description = iconv("UTF-8", "UTF-8", $description).'...';
            $srNodeB -> setAttribute("SHORT", "TRUE");
          }
          $srNodeB -> setAttribute("DESCRIPTION", $description);
        }
      }
    }	else if ($_REQUEST['type']=='events') {
      $sql = "SELECT * FROM `event`
              INNER JOIN event_user ON event.id = event_user.eventid   
              WHERE event_user.userid=$id 
              ORDER BY id DESC";   		
    	$result = $db->execute($sql);      
    	$srNodeList = $srNodeN -> appendChild($resultXML->CreateElement('EVENTS'));
    	while ($myrow = mysql_fetch_object($result)) {
        $srNodeN = $srNodeList -> appendChild($resultXML->CreateElement('EVENT'));
        $srNodeN -> setAttribute("IID", $myrow->id);
        $srNodeN -> setAttribute("TITLE", $myrow->title);
        $srNodeN -> setAttribute("URL", '/event/'.$myrow->id.'/');
        $srNodeN -> setAttribute("DATE", $myrow->date);
        $srNodeN -> setAttribute("ADDRESS", $myrow->address);
    		if (strlen($myrow->sport)) {
    		  $sports = array();
    		  $sports = preg_split('/,/',$myrow->sport);
    		  foreach ($sports AS $sport) {
    		    if ((int)$sport) {
    		      $srNodsS = $srNodeN -> appendChild($resultXML->CreateElement("SPORT"));
    		      $srNodsS -> setAttribute("ID", (int)$sport);
    		    }
    		  }
    		}
		  }
     }		
	}

	return $resultXML;
}

function fullGoods($id)
{
  global $db, $config, $user;
	$sql = "SELECT * FROM goods WHERE documentid=$id";	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
		$content = $myrow->content;
		$title = $myrow->title;
		$description = $myrow->description;
		$price1 = $myrow->price1;
		$price2 = $myrow->price2;
		$available = $myrow->available;
		$date = $myrow->date;
		$image = $myrow->image;
		$param1 = $myrow->param1;
		$param2 = $myrow->param2;
		$param3 = $myrow->param3;
		$param4 = $myrow->param4;
		$param5 = $myrow->param5;
		$param6 = $myrow->param6;
		$param7 = $myrow->param7;
		$param8 = $myrow->param8;
		$param9 = $myrow->param9;
		$param10 = $myrow->param10;
		$param11 = $myrow->param11;
		$param12 = $myrow->param12;
		$param13 = $myrow->param13; 		
		$pagetitle = $myrow->pagetitle;
		$keywords = $myrow->keywords;
		$metadescription = $myrow->metadescription;
		$quantity = $myrow->quantity;
	}

	$resultXML = new DOMDocument('1.0');	
	$resultRoot = $resultXML -> appendChild($resultXML->CreateElement("DOCUMENT"));	
		$resultRoot -> setAttribute("IID", $id);
		$resultRoot -> setAttribute("TYPE", 'GOODS');			
		$resultRoot -> setAttribute("TITLE", $title);
		if (strlen($description)) {$resultRoot -> setAttribute("DESCRIPTION", $description);}
    $resultRoot -> setAttribute("CONTENT", $content);		
		$resultRoot -> setAttribute("PRICE1", $price1);
		$resultRoot -> setAttribute("PRICE2", $price2);
    $resultRoot -> setAttribute("PRICE_FORMAT", number_format($price1, 0, ',', ' '));
    $resultRoot -> setAttribute("QUANTITY", $quantity);
		if (strlen($keywords)){$resultRoot -> setAttribute("KEYWORDS", $keywords);}
		if (strlen($metadescription)){$resultRoot -> setAttribute("METADESCRIPTION", $metadescription);}
		if (strlen($pagetitle)) {$resultRoot -> setAttribute("PAGETITLE", $pagetitle);}
		if (strlen($param1)) {$resultRoot -> setAttribute("PARAM1", $param1);}
		if (strlen($param2)) {$resultRoot -> setAttribute("PARAM2", $param2);}
		if (strlen($param3)) {$resultRoot -> setAttribute("PARAM3", $param3);}
		if (strlen($param4)) {$resultRoot -> setAttribute("PARAM4", $param4);}
		if (strlen($param5)) {$resultRoot -> setAttribute("PARAM5", $param5);}
		if (strlen($param6)) {$resultRoot -> setAttribute("PARAM6", $param6);}
		if (strlen($param7)) {$resultRoot -> setAttribute("PARAM7", $param7);}
		if (strlen($param8)) {$resultRoot -> setAttribute("PARAM8", $param8);}			
		if (strlen($param9)) {$resultRoot -> setAttribute("PARAM9", $param9);}	 
		if (strlen($param10)) {$resultRoot -> setAttribute("PARAM10", $param10);}			      			
		if (strlen($param11)) {$resultRoot -> setAttribute("PARAM11", $param11);}
		if (strlen($param12)) {$resultRoot -> setAttribute("PARAM12", $param12);}
		if (strlen($param13)) {$resultRoot -> setAttribute("PARAM13", $param13);}
		$resultRoot -> setAttribute("DATE", $date);				
		if (strlen($image)) {
      $resultRoot -> setAttribute("IMAGE", $config->upfolder.$image);
      $resultRoot -> setAttribute("IMAGE_BIG", $config->upfolder.'big_'.$image);
    }				

  	$sql2 = "SELECT * FROM image_owner WHERE docid = $id  ORDER BY sort DESC";	
  	$result2 = $db->execute($sql2);  		  
  	while ($myrow2 = mysql_fetch_object($result2)) {      
    	$srNodeN = $resultRoot -> appendChild($resultXML->createElement("PHOTO"));
  		$srNodeN -> setAttribute("IMAGE", $config->upfolder.$myrow2->image);
      $srNodeN -> setAttribute("IMAGE_SMALL", $config->upfolder.'small_'.$myrow2->image);
  		if (strlen($myrow2->title)) {$srNodeN -> setAttribute("TITLE", $myrow2->title);}
  	}
  	
		return $resultXML;
}

function fullSimple($id)
{
  global $db;
  $resultXML = new DOMDocument;
  $sql = "SELECT content FROM simple WHERE documentid=$id";
  $result = $db->execute($sql);
  $content = '';
	if ($myrow = mysql_fetch_object($result)) {
		$content = $myrow->content;		
	}
	$xml = new DOMDocument('1.0');
	$xml->loadXML($content);
	$root = $xml->firstChild;
	return $xml;
}

function fullQuestion($id)
{
  global $db;
  $resultXML = new DOMDocument;
	$sql = "SELECT * FROM question WHERE documentid=$id";  	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
	  $quest  = $myrow->quest;
	  $documentid = $myrow->documentid;
		$total = $myrow->total;		
  }
  
	$srNodeN = $resultXML -> appendChild($resultXML->createElement("VOTE"));
	if (isset($_REQUEST['graph']) &&  $_REQUEST['graph']=='true') {
	  $srNodeN -> setAttribute("GRAPH", 'TRUE');
	}
	$srNodeN -> setAttribute("RESULTS", 'TRUE');
	$srNodeN -> setAttribute("SHOW", "MULTIPLE");
	$srNodeN -> setAttribute("TYPE", "QUESTION");
	$srNodeQ = $srNodeN -> appendChild($resultXML->createElement("QUESTION"));
	$srNodeQ -> setAttribute("RESULTS", 'TRUE');
	$srNodeQ -> setAttribute("IID", $myrow->documentid);
	$srNodeQ -> setAttribute("QUESTIONID", $myrow->documentid);
	$sql2 = "select sid from alldocs where documentid=$myrow->documentid";
	$result2 = $db->execute($sql2);
	if ($myrow2 = mysql_fetch_row($result2))
	{
		$srNodeQ -> setAttribute("URL", "/?id=$myrow->documentid&sid=$myrow2[0]");
	}
	if (strlen($myrow->quest)>0)
	{
		$srNodeQ -> setAttribute("QUESTION", $myrow->quest);
	}
	if ($myrow->multianswer==1) {$srNodeQ -> setAttribute("MULTIPLE", "TRUE");}
		else {$srNodeQ -> setAttribute("MULTIPLE", "FALSE");}
	$srNodeQ -> setAttribute("TOTAL", $myrow->total);

	$sql2 = "SELECT id, answer, acount FROM answers WHERE questid=$myrow->documentid";
	$result2 = $db->execute($sql2);
	while ($myrow2 = mysql_fetch_row($result2))
	{
		$srNodeA = $srNodeQ -> appendChild($resultXML->createElement("ANSWER"));
		$srNodeA -> setAttribute("ANSWERID", $myrow2[0]);	
		$srNodeA -> setAttribute("CONTENT", $myrow2[1]);	
		$srNodeA -> setAttribute("TOTAL", $myrow2[2]);
		if ((int)$myrow->total>0) {$PERCENT = round((int)$myrow2[2]/(int)$myrow->total*100);}
		else {$PERCENT = 0;}
		$srNodeA -> setAttribute("PERCENT", $PERCENT);
		if ((int)$myrow->total>0) {$DOUBLEPERCENT = round((int)$myrow2[2]/(int)$myrow->total*10000)/100;}
		else {$DOUBLEPERCENT = 0;}
		$srNodeA -> setAttribute("DOUBLEPERCENT", $DOUBLEPERCENT);
	}

	return $resultXML;  
}

function fullForum($id)
{
  global $forumRoot,$db;
  
  $resultXML = new DOMDocument;  
  
	$stateid = "";
	$reading_user_id = 0;
	$last_read_id = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = $_COOKIE["stateid"];
	}
	if (strlen($stateid)>0) {
    $sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {
		  $reading_user_id = $myrow->documentid;
		}
		if ($reading_user_id>0) {      
      $sql = "SELECT last_read_id FROM forum_reading WHERE user_id=$reading_user_id AND tema_id=$id";
      $result = $db->execute($sql);
      if ($myrow = mysql_fetch_object($result)) {
        $last_read_id = $myrow->last_read_id;
      }
		}
	}
  
	$sql = "SELECT * FROM forum WHERE documentid=$id";  	
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
	  $title = $myrow->title;
	  $documentid = $myrow->documentid;
		$content = $myrow->content;		
		$userid = $myrow->userid;
		$username = $myrow->username;
		$date = $myrow->date;
		$image = $myrow->image;
		$parentid = $myrow->parentid;
	
    $forum = $resultXML->createElement("FORUM");
    $forumRoot = $resultXML->appendChild($forum);
    
    $forumRoot -> setAttribute("IID", $documentid);
    $forumRoot -> setAttribute("TITLE", stripslashes($title));
	}	
	  	
	$sql = "SELECT * FROM forum WHERE documentid=$id OR ownerid = $id ORDER BY documentid";  	
	$result = $db->execute($sql);
	
	while ($myrow = mysql_fetch_object($result)) {	 
	  $title = $myrow->title;
	  $active = $myrow->active;
	  $documentid = $myrow->documentid;
		$content = $myrow->content;		
		$userid = $myrow->userid;
		$username = $myrow->username;
		$date = $myrow->date;
		$image = $myrow->image;
		$image_pre = $myrow->image_pre;
		$parentid = $myrow->parentid;    		
    
    $document = $resultXML->createElement("DOCUMENT");
    $resultRoot = $forumRoot->appendChild($document);
  	
  	$resultRoot -> setAttribute("IID", $documentid);
  	$resultRoot -> setAttribute("TYPE", 'FORUM');			
  	if (strlen($title)>0) {
  		$resultRoot -> setAttribute("TITLE", stripslashes($title));
  	}
  	if ($active) {$resultRoot -> setAttribute("ACTIVE", 'TRUE');}
  	else {$resultRoot -> setAttribute("ACTIVE", 'FALSE');}
  	$resultRoot -> setAttribute("USERID", $userid);
  	if (strlen($username)>0) {
  		$resultRoot -> setAttribute("USERNAME", $username);
  	}
  	if (strlen($image)>0) {
  		$resultRoot -> setAttribute("IMAGE", $image);
  		$resultRoot -> setAttribute("IMAGE_PRE", $image_pre);
  	}
  	$resultRoot -> setAttribute("DATE", $date);		
  	$resultRoot -> setAttribute("PARENTID", $parentid);
  	if (strlen($content)>0) {
  		$resultRoot -> setAttribute("CONTENT", stripslashes($content));
  	}  			
	  if ($last_read_id == 0 || $last_read_id < $documentid) {
	   $resultRoot -> setAttribute("NEW", 'TRUE');
	  }  	
  }
  
	/* Если юзер залогинен - записываем его максимальное прочитанное id для этой темы */
	if ($reading_user_id>0) {
  	$sql = "REPLACE INTO forum_reading SET user_id=$reading_user_id, tema_id=$id, last_read_id=(SELECT MAX(documentid) FROM forum WHERE documentid=$id OR ownerid = $id);";
  	$result = $db->execute($sql);
	}
		
	return $resultXML;
}

function processDoc($id) {
	global $resultXML;
	global $doctype;
	global $db;
	
	//получаем контент

	$resultXML = new DOMDocument('1.0');
  switch ($doctype) {
      case 'simple':
    		//симпл не стоит выносить в отдельный показыватель
    		$sql = "SELECT content FROM simple WHERE documentid=$id";	
    		$result = $db->execute($sql);
    		if ($myrow = mysql_fetch_row($result))
    		{
    			$inputxml = $myrow[0];
    		}
    		$xml = new DOMDocument('1.0');
    		$xml->loadXML($inputxml);
    	
    		$root = $xml->firstChild;
    		$resultRoot = $resultXML;
    		processXML($root, $resultRoot);      
        break;
      case 'form':
    		$sql = "SELECT content FROM form WHERE documentid=$id";	
    		$result = $db->execute($sql);
    		if ($myrow = mysql_fetch_row($result))
    		{
    			$inputxml = $myrow[0];
    		}
    		$xml = new DOMDocument('1.0');
    		$xml->loadXML($inputxml);
    	
    		$root = $xml->firstChild;
    		$resultRoot = $resultXML;
    		processXML($root, $resultRoot);      
        break;
      case '':
        f404(6);
        break;
      default:      
        $resultXML = fullDocument($doctype,$id);      
  }   
    
	return $resultXML;
}

function f404($type) {
  header("HTTP/1.0 404 Not Found");
  $filename = $_SERVER['DOCUMENT_ROOT']."/404.html";
  $handle = fopen($filename, "rb");
  $contents = fread($handle, filesize($filename));
  fclose($handle);
  echo $contents;
  echo $type;
  exit();	
}
?>