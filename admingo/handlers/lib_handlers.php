<?php
class UserInfo
{
  var $logined;
  var $userid;
  var $active;
  var $email;
  var $password;
  var $title;
  var $lat;
  var $lng;
  var $about;
  var $accesstime;
  var $age;
  var $birthday;
  var $image;
  var $sex;
  var $agree;
  var $vkid;
  var $fbid;
  var $googleid;
  var $okid;
  var $status;
  var $action_read;
  
  function __construct() {
    global $db;
  	if (isset($_COOKIE["stateid"]) && strlen($_COOKIE["stateid"])>0) {
  		$stateid = clearField($_COOKIE["stateid"]);
  		$sql = "SELECT *, floor(datediff(now(),birthday)/365.25) AS age FROM user WHERE stateid = '$stateid'";  		
  		$result = $db->execute($sql);		
  		$this->logined = mysql_num_rows($result);
  		if ($this->logined) {
  			$myrow = mysql_fetch_object($result);
  			$this->userid = $myrow->documentid;
  			$this->email = $myrow->email;  			
  			$this->active = $myrow->active;  		
  			$this->title = $myrow->title; 
  			$this->image = $myrow->image;
  			$this->password = $myrow->password;
        $this->lat = $myrow->lat;
        $this->lng = $myrow->lng;
        $this->about = $myrow->about;
        $this->accesstime = $myrow->accesstime;
        $this->age = $myrow->age;
        $this->birthday = $myrow->birthday;
        $this->sex = $myrow->sex;
        $this->agree = $myrow->agree;
        $this->vkid = $myrow->vkid;
        $this->fbid = $myrow->fbid;
        $this->googleid = $myrow->googleid;
        $this->okid = $myrow->okid;        
        $this->status = $myrow->status;       
        $this->action_read = $myrow->action_read;
  		}
  	}
  } 
}

  function tags2real($arr, $arr_real, $cont) {
    for($i=0;$i<count($arr);$i++) {
      if (strpos($cont,$arr[$i])!== false) {
        $cont = str_replace ($arr[$i], $arr_real[$i], $cont);
      }
    }  
    return $cont;
  }
    
  function content2realtags($CONTENT, $direction_2real) {
    $smiles = array('[*O:-)*]','[*:-)*]','[*:-(*]','[*;-)*]','[*:-P*]','[*8-)*]','[*:-D*]','[*:-[*]','[*=-O*]','[*:-**]','[*:`(*]','[*:-X*]','[*&gt;:o*]','[*:-|*]','[*:-/*]','[**JOKINGLY**]','[*]:-&gt;*]','[*[:-}*]','[**KISSED**]','[*:-!*]','[**TIRED**]','[**STOP**]','[**KISSING**]','[*@}-&gt;--*]','[**THUMBS UP**]','[**DRINK**]','[**IN LOVE**]','[*@=*]','[**HELP**]','[*/m/*]','[*%)*]','[**OK**]','[**WASSUP**]','[**SORRY**]','[**BRAVO**]','[**ROFL**]','[**PARDON**]','[**NO**]','[**CRAZY**]','[**DONT_KNOW**]','[**DANCE**]','[**YAHOO**]','[**NEW_PACK**]','[**TEASE**]','[**SALIVA**]','[**DASH**]','[**WILD**]','[**TRAINING**]','[**FOCUS**]','[**HANG**]','[**DANCE**]','[**DANCE2**]','[**MEGA_SHOK**]','[**TO_PICK_ONES_NOSE**]','[**YU**]','[**HUNTER**]','[**KUKU**]','[**FUCK**]','[**FAN**]','[**ASS**]','[**LOCOMOTIVE**]','[**BB**]','[**CONCUSSION**]','[**PLEASANTRY**]','[**DISAPPEAR**]','[**SUICIDE**]','[**PILOT**]','[**DOWN**]','[**ENERGY**]','[**STINKER**]','[**PREVED**]','[**I-M_SO_HAPPY**]','[**PRANKSTER**]','[**LAUGH**]','[**BOAST**]','[**THANK_YOU**]','[**SHOUT**]','[**VICTORY**]','[**WINK**]','[**SPITEFUL**]','[**BYE**]','[**THIS**]','[**DON-T_MENTION**]','[**SARCASTIC_HAND**]','[**FIE**]','[**SWOON**]','[**SCARE**]','[**ANGER**]','[**YESS**]','[**VAVA**]','[**SCRATCH_ONE-S_HEAD**]','[**NONO**]','[**WHISTLE**]','[**UMNIK**]','[**ZOOM**]','[**HEAT**]','[**DECLARE**]','[**IDEA**]','[**ON_THE_QUIET**]','[**GIVE_HEART**]','[**GIVE_FLOWERS**]','[**FRIENDS**]','[**PUNISH**]','[**PORKA**]','[**PARTY**]','[**GIRL_SMILE**]','[**TENDER**]','[**FLIRT**]','[**CURTSEY**]','[**GOGOT**]','[**GIRL_WINK**]','[**GIRL_BLUM**]','[**GIRL_HIDE**]','[**GIRL_CRAZY**]','[**GIRL_WACKO**]','[**GIRL_IN_LOVE**]','[**GIRL_DANCE**]','[**KISS2**]','[**GIRL_PINKGLASSESF**]','[**GIRL_MAD**]','[**HISTERIC**]','[**GIRL_SIGH**]','[**GIRL_SAD**]','[**GIRL_CRAY**]','[**GIRL_CRAY2**]','[**GIRL_IMPOSSIBLE**]','[**GIRL_DRINK**]','[**GIRL_MIRROR**]','[**NAILS**]','[**GIRL_HOSPITAL**]','[**GIRL_KID**]','[**GIRL_HAIR_DRIER**]','[**GIRL_WITCH**]','[**FIRST_MOVIE**]','[**SLAP_IN_THE_FACE**]','[**FRIENDSHIP**]','[**GIRL_KISSES**]','[**ON_HANDS**]','[**IT_IS_LOVE**]','[**SUPPER_FOR_A_TWO**]','[**SEX_BEHIND**]','[**SEX_BED**]','[**BABY1**]','[**BABY2**]','[**BABY3**]','[**BABY4**]','[**BABY5**]','[**MUSIC_FORGE**]','[**MUSIC_SAXOPHONE**]','[**MUSIC_FLUTE**]','[**MUSIC_VIOLIN**]','[**MUSIC_PIANO**]','[**MUSIC_DRUMS**]','[**MUSIC_ACCORDION**]','[**VINSENT**]','[**FRENK**]','[**TOMMY**]','[**BIG_BOSS**]','[**HI**]','[**BUBA**]','[**RUSSIAN_RU**]','[**BRUNETTE**]','[**GIRL_DEVIL**]','[**GIRL_WEREWOLF**]','[**QUEEN**]','[**KING**]','[**BEACH**]','[**SMOKE**]','[**SCENIC**]','[**READER**]','[**READ**]','[**RTFM**]','[**TO_KEEP_ORDER**]','[**WIZARD**]','[**LAZY**]','[**DENTAL**]','[**SUPERSTITION**]','[**CRAZY_PILOT**]','[**TO_BECOME_SENILE**]','[**DOWNLOAD**]','[**TELEPHONE**]','[**DIVER**]','[**WAKE_UP**]','[**ICE_CREAM**]','[**JOURNALIST**]','[**SOAP_BUBBLES**]','[**BODY_BUILDER**]','[**CUP_OF_COFFEE**]','[**SOCCER**]','[**SWIMMER**]','[**PIRATE**]','[**CLOWN**]','[**JESTER**]','[**CANNIBAL_DRUMS**]','[**PIONEER**]','[**MOIL**]','[**PAINT**]','[**SUPERMAN**]','[**COLD**]','[**ILLNESS**]','[**WINNER**]','[**POLICE**]','[**TOILET_PLUMS**]','[**DEATH**]','[**ZOMBIE**]','[**UFO**]','[**SUN**]','[**PUMPKIN_GRIEF**]','[**PUMPKIN_SMILE**]','[**POOH_GO**]');
    $smiles_real = array('<img src="/smiles/aa.gif"/>','<img src="/smiles/ab.gif"/>','<img src="/smiles/ac.gif"/>','<img src="/smiles/ad.gif"/>','<img src="/smiles/ae.gif"/>','<img src="/smiles/af.gif"/>','<img src="/smiles/ag.gif"/>','<img src="/smiles/ah.gif"/>','<img src="/smiles/ai.gif"/>','<img src="/smiles/aj.gif"/>','<img src="/smiles/ak.gif"/>','<img src="/smiles/al.gif"/>','<img src="/smiles/am.gif"/>','<img src="/smiles/an.gif"/>','<img src="/smiles/ao.gif"/>','<img src="/smiles/ap.gif"/>','<img src="/smiles/aq.gif"/>','<img src="/smiles/ar.gif"/>','<img src="/smiles/as.gif"/>','<img src="/smiles/at.gif"/>','<img src="/smiles/au.gif"/>','<img src="/smiles/av.gif"/>','<img src="/smiles/aw.gif"/>','<img src="/smiles/ax.gif"/>','<img src="/smiles/ay.gif"/>','<img src="/smiles/az.gif"/>','<img src="/smiles/ba.gif"/>','<img src="/smiles/bb.gif"/>','<img src="/smiles/bc.gif"/>','<img src="/smiles/bd.gif"/>','<img src="/smiles/be.gif"/>','<img src="/smiles/bf.gif"/>','<img src="/smiles/bg.gif"/>','<img src="/smiles/bh.gif"/>','<img src="/smiles/bi.gif"/>','<img src="/smiles/bj.gif"/>','<img src="/smiles/bk.gif"/>','<img src="/smiles/bl.gif"/>','<img src="/smiles/bm.gif"/>','<img src="/smiles/bn.gif"/>','<img src="/smiles/bo.gif"/>','<img src="/smiles/bp.gif"/>','<img src="/smiles/bq.gif"/>','<img src="/smiles/br.gif"/>','<img src="/smiles/bs.gif"/>','<img src="/smiles/bt.gif"/>','<img src="/smiles/bu.gif"/>','<img src="/smiles/bv.gif"/>','<img src="/smiles/bw.gif"/>','<img src="/smiles/bx.gif"/>','<img src="/smiles/by.gif"/>','<img src="/smiles/bz.gif"/>','<img src="/smiles/ca.gif"/>','<img src="/smiles/cb.gif"/>','<img src="/smiles/cc.gif"/>','<img src="/smiles/cd.gif"/>','<img src="/smiles/ce.gif"/>','<img src="/smiles/cf.gif"/>','<img src="/smiles/cg.gif"/>','<img src="/smiles/ch.gif"/>','<img src="/smiles/ci.gif"/>','<img src="/smiles/cj.gif"/>','<img src="/smiles/ck.gif"/>','<img src="/smiles/cl.gif"/>','<img src="/smiles/cm.gif"/>','<img src="/smiles/cn.gif"/>','<img src="/smiles/co.gif"/>','<img src="/smiles/cp.gif"/>','<img src="/smiles/cq.gif"/>','<img src="/smiles/cr.gif"/>','<img src="/smiles/cs.gif"/>','<img src="/smiles/ct.gif"/>','<img src="/smiles/cu.gif"/>','<img src="/smiles/cv.gif"/>','<img src="/smiles/cw.gif"/>','<img src="/smiles/cx.gif"/>','<img src="/smiles/cy.gif"/>','<img src="/smiles/cz.gif"/>','<img src="/smiles/da.gif"/>','<img src="/smiles/db.gif"/>','<img src="/smiles/dc.gif"/>','<img src="/smiles/dd.gif"/>','<img src="/smiles/de.gif"/>','<img src="/smiles/df.gif"/>','<img src="/smiles/dg.gif"/>','<img src="/smiles/dh.gif"/>','<img src="/smiles/di.gif"/>','<img src="/smiles/dj.gif"/>','<img src="/smiles/dk.gif"/>','<img src="/smiles/dl.gif"/>','<img src="/smiles/dm.gif"/>','<img src="/smiles/dn.gif"/>','<img src="/smiles/do.gif"/>','<img src="/smiles/dp.gif"/>','<img src="/smiles/dq.gif"/>','<img src="/smiles/dr.gif"/>','<img src="/smiles/ds.gif"/>','<img src="/smiles/dt.gif"/>','<img src="/smiles/du.gif"/>','<img src="/smiles/dv.gif"/>','<img src="/smiles/dw.gif"/>','<img src="/smiles/dx.gif"/>','<img src="/smiles/dy.gif"/>','<img src="/smiles/dz.gif"/>','<img src="/smiles/ea.gif"/>','<img src="/smiles/eb.gif"/>','<img src="/smiles/ec.gif"/>','<img src="/smiles/ed.gif"/>','<img src="/smiles/ee.gif"/>','<img src="/smiles/ef.gif"/>','<img src="/smiles/eg.gif"/>','<img src="/smiles/eh.gif"/>','<img src="/smiles/ei.gif"/>','<img src="/smiles/ej.gif"/>','<img src="/smiles/ek.gif"/>','<img src="/smiles/el.gif"/>','<img src="/smiles/em.gif"/>','<img src="/smiles/en.gif"/>','<img src="/smiles/eo.gif"/>','<img src="/smiles/ep.gif"/>','<img src="/smiles/eq.gif"/>','<img src="/smiles/er.gif"/>','<img src="/smiles/es.gif"/>','<img src="/smiles/et.gif"/>','<img src="/smiles/eu.gif"/>','<img src="/smiles/ev.gif"/>','<img src="/smiles/ew.gif"/>','<img src="/smiles/ex.gif"/>','<img src="/smiles/ey.gif"/>','<img src="/smiles/ez.gif"/>','<img src="/smiles/fa.gif"/>','<img src="/smiles/fb.gif"/>','<img src="/smiles/fc.gif"/>','<img src="/smiles/fd.gif"/>','<img src="/smiles/fe.gif"/>','<img src="/smiles/ff.gif"/>','<img src="/smiles/fg.gif"/>','<img src="/smiles/fh.gif"/>','<img src="/smiles/fi.gif"/>','<img src="/smiles/fj.gif"/>','<img src="/smiles/fk.gif"/>','<img src="/smiles/fl.gif"/>','<img src="/smiles/fm.gif"/>','<img src="/smiles/fn.gif"/>','<img src="/smiles/fo.gif"/>','<img src="/smiles/fp.gif"/>','<img src="/smiles/fq.gif"/>','<img src="/smiles/fr.gif"/>','<img src="/smiles/fs.gif"/>','<img src="/smiles/ft.gif"/>','<img src="/smiles/fu.gif"/>','<img src="/smiles/fv.gif"/>','<img src="/smiles/fw.gif"/>','<img src="/smiles/fx.gif"/>','<img src="/smiles/fy.gif"/>','<img src="/smiles/fz.gif"/>','<img src="/smiles/ga.gif"/>','<img src="/smiles/gb.gif"/>','<img src="/smiles/gc.gif"/>','<img src="/smiles/gd.gif"/>','<img src="/smiles/ge.gif"/>','<img src="/smiles/gf.gif"/>','<img src="/smiles/gg.gif"/>','<img src="/smiles/gh.gif"/>','<img src="/smiles/gi.gif"/>','<img src="/smiles/gj.gif"/>','<img src="/smiles/gk.gif"/>','<img src="/smiles/gl.gif"/>','<img src="/smiles/gm.gif"/>','<img src="/smiles/gn.gif"/>','<img src="/smiles/go.gif"/>','<img src="/smiles/gp.gif"/>','<img src="/smiles/gq.gif"/>','<img src="/smiles/gr.gif"/>','<img src="/smiles/gs.gif"/>','<img src="/smiles/gt.gif"/>','<img src="/smiles/gu.gif"/>','<img src="/smiles/gv.gif"/>','<img src="/smiles/gw.gif"/>','<img src="/smiles/gx.gif"/>','<img src="/smiles/gy.gif"/>','<img src="/smiles/gz.gif"/>','<img src="/smiles/ha.gif"/>','<img src="/smiles/hb.gif"/>','<img src="/smiles/hc.gif"/>','<img src="/smiles/hd.gif"/>','<img src="/smiles/he.gif"/>','<img src="/smiles/hf.gif"/>','<img src="/smiles/hg.gif"/>','<img src="/smiles/hh.gif"/>','<img src="/smiles/hi.gif"/>','<img src="/smiles/hj.gif"/>','<img src="/smiles/hk.gif"/>','<img src="/smiles/hl.gif"/>','<img src="/smiles/hm.gif"/>','<img src="/smiles/hn.gif"/>','<img src="/smiles/ho.gif"/>','<img src="/smiles/hp.gif"/>','<img src="/smiles/hq.gif"/>','<img src="/smiles/hr.gif"/>','<img src="/smiles/hs.gif"/>','<img src="/smiles/ht.gif"/>','<img src="/smiles/hu.gif"/>','<img src="/smiles/hv.gif"/>','<img src="/smiles/hw.gif"/>','<img src="/smiles/hx.gif"/>','<img src="/smiles/hy.gif"/>','<img src="/smiles/hz.gif"/>','<img src="/smiles/ia.gif"/>','<img src="/smiles/ib.gif"/>');  
    $tags = array('[b]','[i]','[quote]','[/b]','[/i]','[/quote]');
    $tags_real = array('<b>','<i>','<div class="quote">','</b>','</i>','</div>');
    
    if ($direction_2real) {
  		$CONTENT = tags2real($smiles, $smiles_real, $CONTENT);
      $CONTENT = tags2real($tags, $tags_real, $CONTENT);  
      $CONTENT = str_replace (chr(13).chr(10), '<br/>', $CONTENT);
    }
    else {
  		$CONTENT = tags2real($smiles_real, $smiles, $CONTENT);
      $CONTENT = tags2real($tags_real, $tags, $CONTENT);  
      $CONTENT = str_replace ('<br/>', chr(13).chr(10), $CONTENT);    
    }
    return $CONTENT;
	}
	
	function siteUsersView ($node, $result, $faces) {
	  global $resultXML, $config;
		while ($myrow = mysql_fetch_object($result)) {
			$srNodeN = $node -> appendChild($resultXML->createElement("DOCUMENT"));
			$srNodeN -> setAttribute("IID", $myrow->documentid);
			$srNodeN -> setAttribute("SHOW", "MULTIPLE");
			$srNodeN -> setAttribute("TYPE", "SITEUSER");
      if (isset($faces)) {			
  			if (strlen($faces[$myrow->documentid][1])) $srNodeN -> setAttribute("FACE1", '/uploads/'.$myrow->documentid.'/'.$faces[$myrow->documentid][1]);
  			if (strlen($faces[$myrow->documentid][2])) $srNodeN -> setAttribute("FACE2", '/uploads/'.$myrow->documentid.'/'.$faces[$myrow->documentid][2]);
  			if (strlen($faces[$myrow->documentid][3])) $srNodeN -> setAttribute("FACE3", '/uploads/'.$myrow->documentid.'/'.$faces[$myrow->documentid][3]);
  		}
  		if (!$config->sub && $_SERVER['REMOTE_ADDR']=='127.0.0.1') {
  		  $srNodeN -> setAttribute("URL", "site.php?id=".$myrow->documentid);
  		} else {
  		  $srNodeN -> setAttribute("URL", 'http://'.$myrow->path.'.'.$_SERVER['SERVER_NAME']);
  		}      
			if (strlen($myrow->title)>0) {
				$srNodeN -> setAttribute("TITLE", $myrow->title);
			}
			if (strlen($myrow->path)>0) {
				$srNodeN -> setAttribute("PATH", $myrow->path);
			}
			if (strlen($myrow->www)>0) {
				$srNodeN -> setAttribute("WWW", $myrow->www);
			}			
			if (strlen($myrow->logo)>0) {
				$srNodeN -> setAttribute("LOGO", '/uploads/'.$myrow->documentid.'/'.$myrow->logo);
			}
			if (strlen($myrow->logo_small)>0) {
				$srNodeN -> setAttribute("LOGO_SMALL", '/uploads/'.$myrow->documentid.'/'.$myrow->logo_small);
			}			
			$srNodeN -> setAttribute("SPEC", $myrow->spec);
			$srNodeN -> setAttribute("RATE", round($myrow->rate,1));
			$srNodeN -> setAttribute("RATECOUNT", $myrow->ratecount);
			$srNodeN -> setAttribute("PRICE1", number_format($myrow->price1,0,' ',' '));
			$srNodeN -> setAttribute("PRICE2", number_format($myrow->price2,0,' ',' '));
			$srNodeN -> setAttribute("WORKSCOUNT", $myrow->workscount);
			$srNodeN -> setAttribute("WORKS_ON_PROJECT", $myrow->works_on_project);
		}	 	 
	}
	
	function prepareSQLSiteUsers ($spec, $sort, $start, $count) {
	  global $db;
    $sortSQL = 'rate DESC, ratecount DESC';        
    if (strlen($sort)) { 
      switch ($sort) {
        case 'rate-':
          $sortSQL = 'rate, ratecount';
          break;        
        case 'ratecount':
          $sortSQL = 'ratecount DESC';
          break;          
        case 'ratecount-':
          $sortSQL = 'ratecount';
          break;
        case 'price':
          if ($spec == 1)
            $sortSQL = 'price1 DESC';
          else
            $sortSQL = 'price2 DESC';
          break;          
        case 'price-':
          if ($spec == 1)
            $sortSQL = 'price1';
          else
            $sortSQL = 'price2';        
          break;                              
        case 'workscount':
          $sortSQL = 'workscount DESC';
          break;          
        case 'workscount-':
          $sortSQL = 'workscount';
          break;          
      }      
    }
    
		$sql = "SELECT * FROM siteuser 
            WHERE siteuser.spec LIKE '%$spec%' 
            AND active = 1 AND status = 0    
            ORDER BY $sortSQL, works_on_project DESC
            LIMIT $start, $count";
		$result = $db->execute($sql);
		
    $ids_a = array();
    while ($myrow = mysql_fetch_object($result)) {
      array_push($ids_a, $myrow->documentid);
    }				
    if (count($ids_a)) {
      $ids = implode(",", $ids_a);
      $faces = array();
      $sql2 = "SELECT * FROM work WHERE studioid IN ($ids) AND face>0";
      $result2 = $db->execute($sql2);
      while ($myrow2 = mysql_fetch_object($result2)) {
        $faces[$myrow2->studioid][$myrow2->face] = $myrow2->preview;
      }
    }
    
    return array($result, $faces, $sql);  
  }
	
?>