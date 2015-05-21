<?xml version="1.0" encoding="utf-8" standalone="no"?>
<!DOCTYPE xsl:stylesheet [
	<!ENTITY nbsp "&#160;">
	<!ENTITY copy "&#169;">
	<!ENTITY laquo "&#171;">
	<!ENTITY raquo "&#187;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output standalone="no" method="html" media-type="text/html" encoding="utf-8" indent="no" omit-xml-declaration="no"/>

<xsl:include href="ajax.xsl"/>

<xsl:template match="/">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="*|@*">
	<xsl:copy><xsl:apply-templates select="@*|node()"/></xsl:copy>
</xsl:template>

<xsl:variable name="logined">
<xsl:choose>
	<xsl:when test="/PAGE/CURRENT_STATE/PHP/DOCUMENT">true</xsl:when>
	<xsl:otherwise>false</xsl:otherwise>
</xsl:choose>
</xsl:variable>

<xsl:variable name="moder">
<xsl:choose>
	<xsl:when test="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@STATUS&gt;0">true</xsl:when>
	<xsl:otherwise>false</xsl:otherwise>
</xsl:choose>
</xsl:variable>

<xsl:variable name="admin">
<xsl:choose>
	<xsl:when test="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@STATUS=2">true</xsl:when>
	<xsl:otherwise>false</xsl:otherwise>
</xsl:choose>
</xsl:variable>

<xsl:template name="age">
<xsl:param name="age"/>
<xsl:value-of select="$age"/>&nbsp;
<xsl:choose>
  <xsl:when test="$age &gt;= 10 and $age &lt;= 20 ">лет</xsl:when>
	<xsl:when test="$age mod 10 = 1">год</xsl:when>	
	<xsl:when test="$age mod 10 = 2 or $age mod 10 = 3 or $age mod 10 = 4">года</xsl:when>
	<xsl:otherwise>лет</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="PAGE">
<xsl:text disable-output-escaping="yes">
<![CDATA[
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
]]>
</xsl:text>
<html>
<xsl:text disable-output-escaping="yes">
<![CDATA[
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if IE 9]><html class="ie9"><![endif]-->
]]>
</xsl:text>
<head>
	<title>Weeaaa</title>
<xsl:variable name="description">
<xsl:choose>
	<xsl:when test="CONTENT/DOCUMENT/@METADESCRIPTION">
	 <xsl:value-of select="CONTENT/DOCUMENT/@METADESCRIPTION"/>
	</xsl:when>
	<xsl:when test="CONTENT/INDEX/INSERTDOCUMENT/DOCUMENT/@METADESCRIPTION">
	 <xsl:value-of select="CONTENT/INDEX/INSERTDOCUMENT/DOCUMENT/@METADESCRIPTION"/>
	</xsl:when>	
	<xsl:otherwise></xsl:otherwise>
</xsl:choose>
</xsl:variable>
<xsl:variable name="keywords">
<xsl:choose>
	<xsl:when test="CONTENT/DOCUMENT/@KEYWORDS">
	 <xsl:value-of select="CONTENT/DOCUMENT/@KEYWORDS"/>
	</xsl:when>
	<xsl:when test="CONTENT/INDEX/INSERTDOCUMENT/DOCUMENT/@KEYWORDS">
	 <xsl:value-of select="CONTENT/INDEX/INSERTDOCUMENT/DOCUMENT/@KEYWORDS"/>
	</xsl:when>	
	<xsl:otherwise></xsl:otherwise>
</xsl:choose>
</xsl:variable>
<xsl:choose>  
  <xsl:when test="CONTENT/OBJECT">
    <meta property="og:url" content="http://{/PAGE/@SERVERNAME}{@URL}" />
    <meta property="og:image" content="{/PAGE/CONTENT/OBJECT/@image}" />
    <meta property="og:title" content="weeaaa.ru" />
    <meta property="og:description" content="{/PAGE/CONTENT/OBJECT/@text_without_html}" />    
  </xsl:when>
  <xsl:otherwise>
    <meta name="description" content="{$description}"/>
    <meta name="keywords" content="{$keywords}"/>	
    <link rel="icon" type="image/png" href="/img/l.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>  
    <meta property="og:title" content="service soc. photo on the map" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="http://weeaaa.ru" />
    <meta property="og:site_name" content="" />
    <meta property="og:description" content="This is weeaaa service." />
  </xsl:otherwise>
</xsl:choose>

  <link rel="stylesheet" href="/css/bootstrap.css" type="text/css" media="screen"/>			
  <link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css"/>
  <link href="http://fonts.googleapis.com/css?family=Lobster&amp;subset=cyrillic" rel="stylesheet" type="text/css"/>  
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/emoji.css"/>
  <link rel="stylesheet" type="text/css" href="/css/bootstrap-switch.css"/>  
  <link rel="stylesheet" type="text/css" href="/css/bootstrap-datetimepicker.min.css"/>
  <link rel="stylesheet" type="text/css" href="/css/simple-slider.css"/>
  <link rel="stylesheet" type="text/css" href="/css/simple-slider-volume.css"/>
  
  <script src="/js/jquery-2.1.0.min.js"></script>
</head>
<body>
  <div id="header">
    <a class="logo" href="/" title="Weeaaa!">eeaaa!</a>
    <xsl:if test="CONTENT/INDEX">
      <div class="searchDiv">
        <div class="mapSearch">
          <input type="text" id="autocompleteField" value=""/>
          <input type="text" id="textsearch" value="" placeholder="Поиск по ключевым словам"/>
          <div class="x"></div>
        </div>
        <div class="settings" id="settings">
          <button class="btn btn-sm btn-default settings-icon"><i class="glyphicon glyphicon-cog "></i> Настройки</button>
          <div class="win">
            <div class="arr"></div>
            <div id="slider_radius">
              <input type="text" stop-event="stop_dragged" data-slider="true" data-slider-theme="volume" value="2000" data-slider-range="100,5000" data-slider-step="50"/>
              <span id="current_radius">2000&nbsp;m</span>
            </div>          
            <label>
              Добавлено:
              <select class="form-control" name="period">
                <option value="">Не важно</option>
                <option value="0.5">За полчаса</option>
                <option value="1">За час</option>
                <option value="24">За день</option>
                <option value="72">За 3 дня</option>
                <option value="120">За 5 дней</option>
                <option value="168">За неделю</option>
                <option value="">Указать дату...</option>            
              </select>
            </label>
            <div class="period">
              <input type="input" name="period_from" id="period_from"/> - <input type="input" name="period_to" id="period_to"/>
              <button type="button" class="btn btn-default btn-xs" id="period_submit" title="Найти"><i class="glyphicon glyphicon-search"></i></button>
            </div>
            <label>
              <span class="vk"></span>
              <input type="checkbox" name="vk" class="switcher" checked="checked"/>
            </label>
            <label>
              <span class="inst"></span>
              <input type="checkbox" name="inst" class="switcher" checked="checked"/>
            </label>   
            <button class="btn btn-primary" id="refreshMap2"><i class="glyphicon glyphicon-refresh"></i> Обновить</button>     
          </div>
        </div>
        <div class="presets">
          <div class="btn-group">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              Все рубрики <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu" id="tagMenu">
              <li><a style="background-color:#5cb85c" class="tag" href="#" data-value="продажа" data-class="btn-success">продажа</a></li>
              <li><a style="background-color:#d9534f" class="tag" href="#" data-value="важное событие" data-class="btn-danger">важное событие</a></li>
              <li><a style="background-color:#5bc0de" class="tag" href="#" data-value="новость" data-class="btn-info">новость</a></li>
              <li><a style="background-color:#777" class="tag" href="#" data-value="разное" data-class="">разное</a></li>
              <li class="divider"></li>
              <li><a href="#" data-value="" data-class="btn-default">Все рубрики</a></li>
            </ul>          
          </div>       
          <button class="btn btn-sm btn-default" id="preset1"><i class="glyphicon glyphicon-map-marker"></i> Рядом</button>
          <button class="btn btn-sm btn-default" id="preset2"><i class="glyphicon glyphicon-time"></i> Свежак</button>
          <button class="btn btn-sm btn-default" id="findme" title="Найти меня"><i class="glyphicon glyphicon-screenshot"></i></button>
          <button class="btn btn-sm btn-default" id="showmy">
            <xsl:if test="$logined='true'"><xsl:attribute name="style">display:inline-block;</xsl:attribute></xsl:if>
            <i class="glyphicon glyphicon-flag"></i>&nbsp;<span>Мои объекты</span></button>       
        </div>
      </div>
    </xsl:if>
    
    <div class="rBlock">  
      <ul class="login" id="profileBlock">     
  <xsl:choose>
  	<xsl:when test="$logined='true'">
  	   <li>
    	   <a href="/profile/" class="profileLink">
    	     <img src="{/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IMAGE_SMALL}"/>
    	     <xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@TITLE"/>  	     
    	   </a>  	   
  	   </li>
  	</xsl:when>
  	<xsl:otherwise>      
        <li><div>Войти</div></li>
        <li><div class="btn btn-social-icon btn-facebook" id="fbAuth" url="{/PAGE/CURRENT_STATE/PHP/@FB_AUTH}"><i class="fa fa-facebook"></i></div></li>
        <li><div class="btn btn-social-icon btn-vk" id="vkAuth" url="{/PAGE/CURRENT_STATE/PHP/@VK_AUTH}"><i class="fa fa-vk"></i></div></li>
        <li><div class="btn btn-social-icon btn-google-plus" id="gAuth" url="{/PAGE/CURRENT_STATE/PHP/@G_AUTH}"><i class="fa fa-google-plus"></i></div></li>
        <li><div class="btn btn-social-icon btn-pinterest" id="okAuth" url="{/PAGE/CURRENT_STATE/PHP/@OK_AUTH}"><i class="fa">Ok</i></div></li>
    </xsl:otherwise>
  </xsl:choose>
      </ul>         
      <a href="/all/" class="btn btn-sm btn-default about" title="Все интересные моменты">&nbsp;<i class="glyphicon glyphicon-globe"></i>&nbsp;</a>
      <a href="/about/" class="btn btn-sm btn-default about" title="О проекте">&nbsp;<i class="glyphicon glyphicon-info-sign"></i>&nbsp;</a>

      <a href="/profile/pm/" class="btn btn-default btn-sm pmlink auth{$logined}" title="Мои сообщения">&nbsp;<i class="glyphicon glyphicon-envelope"></i>&nbsp;
          <xsl:if test="/PAGE/CURRENT_STATE/PHP/NEWMESS"><span><xsl:value-of select="count(/PAGE/CURRENT_STATE/PHP/NEWMESS)"/></span></xsl:if>
      </a>
      <span class="btn btn-default btn-sm evlink auth{$logined}" title="Мои события">&nbsp;<i class="glyphicon glyphicon-fire"></i>&nbsp;
          <xsl:if test="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@NEWEVENTS&gt;0 and not(/PAGE/CONTENT/PROFILE/EVENTS)">
            <span><xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@NEWEVENTS"/></span>
          </xsl:if>
          <div class="win">
            <div class="arr"></div>
            <div class="scrolllist" id="eventList">
              <xsl:for-each select="/PAGE/CURRENT_STATE/PHP/EVENT">
                <xsl:call-template name="eventItem"/>
              </xsl:for-each>
            </div>
          </div>
      </span>

    </div>
  </div>
  <div class="wrap">
    <xsl:apply-templates select="CONTENT"/>
  </div>
    
  <script src="/js/bootstrap.min.js"></script>	   
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false&amp;libraries=places"></script>
  <script src="/js/richmarker-compiled.js"></script>
  <script src="/js/markerclusterer.js"></script>
  <script src="/js/infobubble.js"></script>
  <script src="/js/ejs_production.js"></script>
  <script src="/js/scrollto.js"></script>
  <script src="/js/ajaxupload.js"></script>
  <script src="/js/bootstrap-switch.min.js"></script>  
  <script src="/js/moment-with-langs.min.js"></script>
  <script src="/js/bootstrap-datetimepicker.js"></script>  
  <script src="/js/simple-slider.js"></script>  
      
  <script src="/js/lib.js"></script>
  <script src="/js/yoEjs.js"></script>  
  <script src="/js/map.js"></script>      
  <script src="/js/soc.js"></script>
  <script src="/js/search.js"></script>  
  <script src="/js/share42.js"></script>    
  <script>
    $(function() {
                  
<xsl:if test="CONTENT/INDEX">
      <xsl:if test="CONTENT/INDEX/PHP/@lat">
        gmap.coords_start = {lat:<xsl:value-of select="CONTENT/INDEX/PHP/@lat"/>, lng:<xsl:value-of select="CONTENT/INDEX/PHP/@lon"/>};
      </xsl:if>
      gmap.init();
</xsl:if>
<xsl:if test="$logined='true'">
      lib.userid = <xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IID"/>;
      lib.userimage = '<xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IMAGE_SMALL"/>';
      lib.userstatus = '<xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@STATUS"/>';
</xsl:if>
<xsl:if test="CONTENT/OBJECT">
      $('.messList').scrollTo(10000);
</xsl:if>
<xsl:if test="/PAGE/CONTENT/PROFILE/PM/USER/@IID">
    lib.currentAdresatId = '<xsl:value-of select="/PAGE/CONTENT/PROFILE/PM/USER/@IID"/>';                
</xsl:if>
<xsl:if test="/PAGE/CONTENT/PROFILE/EVENTS/EVENT">
  $.ajax({
     type: "POST",
     url: "/admingo/handlers/event.php",
     data: 'action=read&amp;id='+<xsl:value-of select="/PAGE/CONTENT/PROFILE/EVENTS/EVENT[1]/@id"/>,
     async: true,
     success: function(response) {}
  });
</xsl:if>
    });	
	</script>
	<script src="/js/main.js"></script>
<xsl:text disable-output-escaping="yes">
<![CDATA[	
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter29061585 = new Ya.Metrika({id:29061585,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/29061585" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->	
]]>
</xsl:text>
<xsl:choose>
	<xsl:when test="/PAGE/CONTENT/INDEX">
<xsl:text disable-output-escaping="yes">
<![CDATA[
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='//www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t14.11;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показано число просмотров за 24"+
" часа, посетителей за 24 часа и за сегодня' "+
"border='0' width='0' height='0'><\/a>")
//--></script><!--/LiveInternet-->

]]>
</xsl:text>	
	</xsl:when>
	<xsl:otherwise>
<xsl:text disable-output-escaping="yes">
<![CDATA[
<div style="margin:5px;">
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='//www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t14.11;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показано число просмотров за 24"+
" часа, посетителей за 24 часа и за сегодня' "+
"border='0' width='88' height='31'><\/a>")
//--></script><!--/LiveInternet-->
</div>
]]>
</xsl:text>
	</xsl:otherwise>
</xsl:choose>
</body>
</html>
</xsl:template>

<xsl:template name="eventItem">
  <a href="/#object{@oid}" class="item" iid="{@id}">
    <span class="imgwrap tomap" lat="{@lat}" lng="{@lng}" oid="{@oid}"><img src="{@image}" class="img"/></span>
    <span class="profile" target="_new"><img src="{@userimage}"/><xsl:value-of select="@username"/></span>: 
    <xsl:choose>
    	<xsl:when test="@action='comment'"><xsl:value-of select="@value"/></xsl:when>
    	<xsl:when test="@action='getlink'">проявил интерес</xsl:when>
    	<xsl:otherwise>
    	 проголосовал 
        <xsl:choose>
        	<xsl:when test="@value=1"><i class="glyphicon glyphicon-thumbs-up"></i></xsl:when>
        	<xsl:otherwise><i class="glyphicon glyphicon-thumbs-down"></i></xsl:otherwise>
        </xsl:choose>	 
    	</xsl:otherwise>
    </xsl:choose>                  
  </a>
</xsl:template>

<xsl:template match="EVENT_LIST">
  <xsl:for-each select="EVENT">
    <xsl:call-template name="eventItem"/>
  </xsl:for-each>
</xsl:template>

<xsl:template match="INDEX">
    <div class="row">
      <div class="col-sm-6 mapWrap">
        <div id="map"></div>
        <xsl:if test="not(PHP/@hint)">
          <div class="hint"><i class="glyphicon glyphicon-info-sign"></i> Для создания объекта - сделайте двойной клик по карте<span class="x"></span></div>
        </xsl:if>
        <button class="btn btn-primary" id="refreshMap"><i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i> Обновить</button>
      </div>
      <div class="col-sm-6 listWrap">
        <div class="itemsList" id="itemsList">
          <div class="preloader"></div>
        </div>
        <div class="objectFormWrap">
          <form id="objectForm" autocomplite="off">
            <button type="button" class="close" data-dismiss="modal">отмена</button>
            <button class="btn btn-primary" type="submit" style="margin: -7px 0 10px 0;"><i class="glyphicon glyphicon-ok"></i> Сохранить</button>
            <h4 class="modal-title">Создать объект на карте</h4>
            <div class="modal-preloader"></div>          
            <input type="hidden" name="action" value="add"/>
            <input type="hidden" name="oid" value=""/>
            <input type="hidden" name="socid" value=""/>
            <input type="hidden" name="lat" value=""/>
            <input type="hidden" name="lng" value=""/>
            <div class="infopanel"></div>
            <div class="form-group">
              <button class="btn" id="attachImage"><i class="glyphicon glyphicon-download"></i> загрузить фото</button>
              <span class="fileinfo"></span>
							<div id="files"></div>
            </div>
            <div class="form-group">
              <textarea class="form-control" name="text" placeholder="Пояснителный текст (куплю, продам, сдам, приглашаю в гости, хочу обсудить и и т.п.)"></textarea>
            </div>
            <div class="form-group link">
              <label><b>Или </b> вставьте ссылку и информация подгрузится автоматически</label>
              <input type="text" name="link" class="form-control" value="" autocomplete="off" placeholder="подгрузить инфу по ссылке"/>
            </div>                        
            <div class="form-group rubrika">
              <p><b>Рубрика</b></p>
              <label class="checkbox-inline" style="background-color:#5cb85c">
                <input type="radio" name="tag" value="продажа" checked="checked"/> продажа
              </label>
              <label class="checkbox-inline" style="background-color:#d9534f">
                <input type="radio" name="tag" value="важное событие"/> важное событие
              </label>
              <label class="checkbox-inline" style="background-color:#5bc0de">
                <input type="radio" name="tag" value="новость"/> новость
              </label>
              <label class="checkbox-inline" style="background-color:#777">
                <input type="radio" name="tag" value="разное"/> разное
              </label>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-ok"></i> Сохранить</button>
            </div>
          </form>     
        </div>
      </div>
    </div>  
    
  <div class="modal fade modalInfo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="modalInfo">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <xsl:text disable-output-escaping="yes"><![CDATA[<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>]]></xsl:text>      
        <div id="modalInfoContent"></div>
      </div>
    </div>
  </div>     
  
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalObject">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <xsl:text disable-output-escaping="yes"><![CDATA[<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>]]></xsl:text>
          <h4 class="modal-title">Создать объект на карте</h4>
        </div>                    
        <div class="modal-body">
          <div class="modal-preloader"></div>
          <form id="objectForm" autocomplite="off">
            <input type="hidden" name="action" value="add"/>
            <input type="hidden" name="lat" value=""/>
            <input type="hidden" name="lng" value=""/>
            <div class="infopanel"></div>
            <div class="form-group">
              <button class="btn" id="attachImage"><i class="glyphicon glyphicon-download"></i> загрузить фото</button>
              <span class="fileinfo"></span>
							<div id="files"></div>
            </div>
            <div class="form-group">
              <textarea class="form-control" name="text" placeholder="Пояснителный текст (куплю, продам, сдам, приглашаю в гости, хочу обсудить и и т.п.)"></textarea>
            </div>
            <div class="form-group link">
              <label><b>Или </b> вставьте ссылку и информация подгрузится автоматически</label>
              <input type="text" name="link" class="form-control" value="" autocomplete="off" placeholder="подгрузить инфу по ссылке"/>
            </div>                        
            <div class="form-group link">
              <p>Рубрика</p>
              <label class="checkbox-inline">
                <input type="checkbox" name="tag" value="продажа"/> продажа
              </label>
              <label class="checkbox-inline">
                <input type="checkbox" name="tag" value="важное событие"/> важное событие
              </label>
              <label class="checkbox-inline">
                <input type="checkbox" name="tag" value="новость"/> новость
              </label>
              <label class="checkbox-inline">
                <input type="checkbox" name="tag" value="разное"/> разное
              </label>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-ok"></i> Сохранить</button>
            </div>
          </form>      
        </div>
      </div>
    </div>
  </div>
</xsl:template>

<xsl:template match="USER">
<div class="row userView">
  <div class="col-md-3 left">
    <img src="{@image}" class="img-thumbnail ava"/>
    <ul class="nav">
       <li>
<xsl:choose>
	<xsl:when test="@my">
	 <a href="/?stateid=exit"><i class="glyphicon glyphicon-log-out"></i> Выход</a>
	 <a href="/profile/pm/"><i class="glyphicon glyphicon-envelope"></i> Сообщения <xsl:if test="count(/PAGE/CURRENT_STATE/PHP/NEWMESS) &gt; 0"><span class="badge"><xsl:value-of select="count(/PAGE/CURRENT_STATE/PHP/NEWMESS)"/></span></xsl:if></a>
	 <a href="/profile/events/"><i class="glyphicon glyphicon-fire"></i> События <xsl:if test="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@NEWEVENTS &gt; 0 and not(/PAGE/CONTENT/PROFILE/EVENTS)"><span class="badge"><xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@NEWEVENTS"/></span></xsl:if></a>
	</xsl:when>
	<xsl:otherwise>
	  <a href="/profile/pm/{@iid}/" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-envelope"></i> Написать сообщение</a> 	  
	</xsl:otherwise>
</xsl:choose>               
       </li>
    </ul>               
  </div>
  <div class="col-md-9 right">      
    <xsl:apply-templates/>      
  </div>
</div>
</xsl:template>

<xsl:template match="PROFILE">
<div class="row userView">
<xsl:choose>
	<xsl:when test="$logined='true'">	 
    <div class="col-md-3 left">
      <img src="{@image}" class="img-thumbnail ava"/>
      <ul class="nav">
         <li>
	 <a href="/?stateid=exit"><i class="glyphicon glyphicon-log-out"></i> Выход</a>
	 <a href="/profile/pm/"><i class="glyphicon glyphicon-envelope"></i> Сообщения <xsl:if test="count(/PAGE/CURRENT_STATE/PHP/NEWMESS) &gt; 0"><span class="badge"><xsl:value-of select="count(/PAGE/CURRENT_STATE/PHP/NEWMESS)"/></span></xsl:if></a>
	 <a href="/profile/events/"><i class="glyphicon glyphicon-fire"></i> События <xsl:if test="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@NEWEVENTS &gt; 0 and not(/PAGE/CONTENT/PROFILE/EVENTS)"><span class="badge"><xsl:value-of select="/PAGE/CURRENT_STATE/PHP/DOCUMENT/@NEWEVENTS"/></span></xsl:if></a>         
         </li>
      </ul>               
    </div>
    <div class="col-md-9 right">      
      <xsl:apply-templates/>      
    </div>
	</xsl:when>
	<xsl:otherwise>
    <div class="alert alert-danger" role="alert">Необходимо авторизоваться</div>	  
	</xsl:otherwise>
</xsl:choose>        
</div>
</xsl:template>

<xsl:template match="PROFILE/EVENTS">
  <h2>Мои события</h2>
  <div class="eventsFullList">
    <xsl:for-each select="EVENT">
      <xsl:call-template name="eventItem"/>
    </xsl:for-each>  
  </div>
  <div class="clearfix preloader" id="autoloading"></div>
  <script>
    window.paramsAutoLoad = {};
    paramsAutoLoad.start = 20;
    paramsAutoLoad.count = 20;
    paramsAutoLoad.data = '';    
  </script>  
  <script src="/js/autoload_events.js"></script>  
</xsl:template>

<xsl:template match="USER/INDEX|PROFILE/INDEX">
    <h1><xsl:value-of select="../@title"/></h1>
    <xsl:if test="../@vk">      
      <p><a href="{../@vk}"><span class="btn btn-social-icon btn-xs btn-vk"><i class="fa fa-vk"></i></span> Профиль Вконтакте</a></p>
    </xsl:if>
    <xsl:if test="../@fb">      
      <p><a href="{../@fb}"><span class="btn btn-social-icon btn-xs btn-facebook"><i class="fa fa-facebook"></i></span> Профиль Facebook</a></p>
    </xsl:if>    
    <xsl:if test="../@google">      
      <p><a href="{../@google}"><span class="btn btn-social-icon btn-xs btn-google-plus"><i class="fa fa-google-plus"></i></span> Профиль google+</a></p>
    </xsl:if>    
    <xsl:if test="../@ok">      
      <p><a href="{../@ok}"><span class="btn btn-social-icon btn-xs btn-pinterest"><i class="fa">Ок</i></span> Профиль Одноклассники</a></p>
    </xsl:if>
    
    <xsl:if test="OBJECTS/ITEM">
      <h3>Объекты</h3>
      <div class="itemsList">
      <xsl:for-each select="OBJECTS/ITEM">        
        <xsl:call-template name="objectItem"/>
      </xsl:for-each>
      </div>
    </xsl:if>  
</xsl:template>

<xsl:template match="ALL">
  <div class="allpage">
    <h3>Все интересные моменты</h3>
    <xsl:apply-templates select="LIST/PARTS"/>    
    <div class="itemsList">
			<div class="row">				
          <xsl:for-each select="LIST/OBJECTS/ITEM">
            <div class="col-lg-6 col-sm-6">            
              <xsl:call-template name="objectItem"/>
            </div>
            <xsl:if test="position() mod 2 = 0">
<xsl:text disable-output-escaping="yes">
<![CDATA[
  </xdiv><xdiv class="row">
]]>
</xsl:text>            
            </xsl:if>            
          </xsl:for-each>
      </div>
    </div>
    <xsl:apply-templates select="LIST/PARTS"/>
  </div>
</xsl:template>

<xsl:template name="objectItem">
  <div class="item" iid="{@iid}">
    <div class="img tomap" lat="{@lat}" lng="{@long}" iid="{@iid}"><img src="{@src}"/></div>
    <div class="info">
      <div class="time"><i class="glyphicon glyphicon-time"></i> <xsl:call-template name="dateFormat2"><xsl:with-param name="date" select="@created"/></xsl:call-template></div>
      <xsl:if test="@la_userid">
        <div class="la">
          <a href="/u{@la_userid}/" target="_new" class="profile">
            <img src="{@la_userimage}"/> <xsl:value-of select="@la_username"/>: 
          </a>
          <xsl:choose>
          	<xsl:when test="@la_action='comment'">
          	 <xsl:value-of select="@la_value"/>
          	</xsl:when>
          	<xsl:otherwise>
          	 проголосовал <i>
              <xsl:attribute name="class">              
                <xsl:choose>
                	<xsl:when test="@la_value=1">glyphicon glyphicon-thumbs-up</xsl:when>
                	<xsl:otherwise>glyphicon glyphicon-thumbs-down</xsl:otherwise>
                </xsl:choose>
              </xsl:attribute>                     
             </i>
          	</xsl:otherwise>
          </xsl:choose>
        </div>
      </xsl:if>
      <p><xsl:value-of select="@text"/></p>
      <div class="source source_own"></div>
      <div class="actions">
        <xsl:if test="not(@my)">
          <div class="btn-toolbar likes" socid="{@iid}">            
            <div class="btn-group">
              <a href="#" value="-1" oid="{@iid}" socid="{@iid}" title="Не очень">
                <xsl:attribute name="class">              
                  <xsl:choose>
                  	<xsl:when test="@myvote='-1'">btn btn-danger active</xsl:when>
                  	<xsl:when test="@myvote='1'">btn btn-danger disabled</xsl:when>
                  	<xsl:otherwise>btn btn-danger</xsl:otherwise>
                  </xsl:choose>
                </xsl:attribute>                      
              <i class="glyphicon glyphicon-thumbs-down"></i></a>                          
              <span class="rate"><xsl:value-of select="@rate"/></span>
              <a href="#" value="1" oid="{@iid}" socid="{@iid}" title="Нравится">
                <xsl:attribute name="class">              
                  <xsl:choose>
                  	<xsl:when test="@myvote='1'">btn btn-success active</xsl:when>
                  	<xsl:when test="@myvote='-1'">btn btn-success disabled</xsl:when>
                  	<xsl:otherwise>btn btn-success</xsl:otherwise>
                  </xsl:choose>
                </xsl:attribute>                      
              <i class="glyphicon glyphicon-thumbs-up"></i></a>                          
            </div>
          </div>
        </xsl:if>
        <a href="/object{@iid}" class="btn btn-sm forum btn-primary">                
          <i class="glyphicon glyphicon-bullhorn"></i> Обсудить <xsl:if test="@fcount&gt;0">(<xsl:value-of select="@fcount"/>)</xsl:if>              
        </a>
        <xsl:if test="@userid=/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IID or $moder='true'">
          <div style="margin-top:10px;">
            <button class="btn btn-sm btn-danger removeObject" oid="{@iid}"><i class="glyphicon glyphicon-remove"></i> Удалить</button>
          </div>
        </xsl:if>
      </div>                        
                                          
    </div>
  </div>   
</xsl:template>

<xsl:template match="OBJECT">
<div class="row objectView">
<xsl:choose>
	<xsl:when test="@error">
	 <p><xsl:value-of select="@error"/></p>
	</xsl:when>
	<xsl:otherwise>
<xsl:if test="@text">
  <div class="row objectViewText">
    <p><xsl:value-of select="@text"/></p>
    <a href="/#object{@id}" lat="{@lat}" lng="{@lng}" class="btn btn-sm tomap btn-default"><i class="glyphicon glyphicon-map-marker"></i> На карте</a>
  </div>
</xsl:if>  
  <div class="col-md-6 left">    
    <div class="img">
      <img src="{@image}"/>
    </div>
    <div class="bot">
      <div class="source source_own"></div>
      <div class="time"><i class="glyphicon glyphicon-time"></i> <span><xsl:value-of select="@date"/></span></div>
      <a href="{@author_link}" rel="nofollow" target="_new" class="profile obj"><img src="{@author_image}"/><span><xsl:value-of select="@author_name"/></span></a>              
    </div>     

    <div class="share" style="float:left;">
      <div class="share42init" id="share42init" oid="{@id}"  
        data-url=""
        data-title=""
        data-image=""
        data-path="../img/"
        data-icons-file="share42_icons.png"
        data-description=""
        data-top1="110"
        data-top2="20"
        data-margin="-90"
      ></div>
    </div>
    <div class="btn-toolbar likes" socid="{@socid}" style="float: left;margin:3px 10px 0 0;">
      <xsl:variable name="likeclass">
      <xsl:choose>
      	<xsl:when test="@myvote=1">active</xsl:when>
      	<xsl:when test="@myvote=-1">disabled</xsl:when>
      	<xsl:otherwise></xsl:otherwise>
      </xsl:choose>    
      </xsl:variable>
      <xsl:variable name="dislikeclass">
      <xsl:choose>
      	<xsl:when test="@myvote=1">disabled</xsl:when>
      	<xsl:when test="@myvote=-1">active</xsl:when>
      	<xsl:otherwise></xsl:otherwise>
      </xsl:choose>    
      </xsl:variable>                
      <div class="btn-group">        
        <a href="#" class="btn btn-danger {$dislikeclass}" value="-1" oid="{@id}" socid="{@socid}" title="Не очень"><i class="glyphicon glyphicon-thumbs-down"></i></a>
        <span class="rate"><xsl:value-of select="@rate"/></span>
        <a href="#" class="btn btn-success {$likeclass}" value="1" oid="{@id}" socid="{@socid}" title="Нравится"><i class="glyphicon glyphicon-thumbs-up"></i></a>                      
      </div>
    </div>    
    <div class="section" style="margin-top: 6px;">
      <span>
      <xsl:attribute name="class">              
        <xsl:choose>
        	<xsl:when test="@tag = 'продажа'">label label-success</xsl:when>
        	<xsl:when test="@tag = 'важное событие'">label label-danger</xsl:when>
        	<xsl:when test="@tag = 'новость'">label label-info</xsl:when>
        	<xsl:otherwise>label label-default</xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>      
      <xsl:value-of select="@tag"/></span>
    </div>    
    <xsl:if test="@link">
      <p><a href="{@link}" rel="nofollow" target="_new"><xsl:value-of select="@link"/></a></p>
    </xsl:if>    
  </div>
  <div class="col-md-6 right">
    <div class="messList" style="height:400px;">
      <xsl:for-each select="FMESS">
        <div class="item">
          <div class="time"><i class="glyphicon glyphicon-time"></i><xsl:call-template name="dateFormat2"><xsl:with-param name="date" select="@date"/></xsl:call-template></div>
          <a href="" class="profile-sm" userid="{@userid}"><img src="{@userimage}"/> <xsl:value-of select="@username"/></a>
          <p><xsl:value-of select="@content"/></p>
        </div>
      </xsl:for-each>
    </div>
    <form id="forumForm">
      <input type="hidden" name="action" value="add"/>
      <input type="hidden" name="oid" value="{@id}"/>
      <input type="hidden" name="socid" value="{@socid}"/>
      <input type="hidden" name="image" value=""/>
      <input type="hidden" name="text" value=""/>            
      <input type="hidden" name="lat" value=""/>
      <input type="hidden" name="lng" value=""/>
      <input type="hidden" name="answer_userid" value=""/>
      <input type="hidden" name="answer_username" value=""/>
      <xsl:choose>
      	<xsl:when test="$logined='true'">
          <div class="input-group" style="width: 390px;">
            <div class="ava"><img src="{/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IMAGE_SMALL}"/></div>
            <div style="margin-left:47px;width: 430px;">
              <textarea class="form-control" name="content" style="width: 390px;"></textarea>
              <span class="input-group-btn">
                <button class="btn btn-primary btn-sm" type="submit" title="Отправить"><i class="glyphicon glyphicon-send"></i></button>
              </span>
            </div>
          </div>      	
      	</xsl:when>
      	<xsl:otherwise>
      	 <div class="error">Чтобы иметь возможно комментировать - зарегистрируйтесь</div>
      	</xsl:otherwise>
      </xsl:choose>            
    </form>    
  </div>
	</xsl:otherwise>
</xsl:choose>
</div>      
</xsl:template>

<xsl:template match="CONTENT">
<xsl:choose>
	<xsl:when test="INDEX">
	 <xsl:apply-templates/>
	</xsl:when>
	<xsl:otherwise>
	 
	   <xsl:apply-templates/>
	 
	</xsl:otherwise>
</xsl:choose>    
</xsl:template>

<xsl:template name="documents">
	<xsl:param name="caption" select="//SECTION[@CURRENT='TRUE']/@NAME"/>		
	<h1><xsl:value-of select="$caption"/></h1>
	
	<xsl:apply-templates select="SEARCHRESULT/PARTS"/>
	
	<xsl:for-each select="SEARCHRESULT/DOCUMENT">		
			<xsl:call-template name="document"/>			
	</xsl:for-each>	
</xsl:template>

<xsl:template match="DOCUMENT">
	<xsl:call-template name="document"/>
</xsl:template>

<xsl:template name="document">
<xsl:choose>
	<xsl:when test="@SHOW='MULTIPLE'">			
	<!-- Множественный показыватель -->	
    <a class="newsListItem clearfix" href="{@URL}">
      <xsl:if test="@IMAGE"><img src="{@IMAGE}"/></xsl:if>      
      <b><xsl:value-of select="@TITLE"/></b>
      <xsl:if test="@DESCRIPTION"><p><xsl:value-of select="@DESCRIPTION"/></p></xsl:if>
    </a>	
	</xsl:when>
	<xsl:otherwise>
  	<!-- Полный показыватель -->
  	
    <div class="textpage">
        <h1><xsl:value-of select="@TITLE"/></h1><br/>
        <xsl:apply-templates/>  
    </div>  	            	
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<!-- разбивка на страницы -->
<xsl:template match="PARTS">
<xsl:if test="@TOTAL&gt;1">
<nav>
  <ul class="pagination">
    <xsl:apply-templates select="PREVPART" />
    <xsl:apply-templates select="PART" />
    <xsl:apply-templates select="NEXTPART" />
  </ul>
</nav>
</xsl:if>
</xsl:template>

<xsl:template match="PREVPART">
    <li>
      <a aria-label="Previous">
        <xsl:attribute name="href">              
          <xsl:choose>
          	<xsl:when test="@CURRENTPAGE&gt;1"><xsl:value-of select="../@LINK"/>&amp;page=<xsl:value-of select="@CURRENTPAGE"/></xsl:when>
          	<xsl:otherwise><xsl:value-of select="../@LINK"/></xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>      
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
</xsl:template>

<xsl:template match="NEXTPART">
    <li>
      <a href="{../@LINK}&amp;page={@CURRENTPAGE}" aria-label="Next">       
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
</xsl:template>

<xsl:template match="PART">
<li>
  <xsl:if test="@ACTIVE='TRUE'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>
  <a>
        <xsl:attribute name="href">              
          <xsl:choose>
          	<xsl:when test="@CURRENTPAGE&gt;1"><xsl:value-of select="../@LINK"/>&amp;page=<xsl:value-of select="@CURRENTPAGE"/></xsl:when>
          	<xsl:otherwise><xsl:value-of select="../@LINK"/></xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>
    <xsl:value-of select="@CURRENTPAGE"/></a>
</li>
</xsl:template>
<!--  end  разбивка на страницы -->

<xsl:template name="dateFormat">
  <xsl:param name="date"/>
	<xsl:value-of select="substring($date,9,2)"/>&nbsp;
  <xsl:call-template name="month"><xsl:with-param name="month" select="substring($date,6,2)"/></xsl:call-template>&nbsp;
  <xsl:value-of select="substring($date,1,4)"/>
</xsl:template>

<xsl:template name="dateFormat2">
  <xsl:param name="date"/>
	<xsl:value-of select="substring($date,9,2)"/>.<xsl:value-of select="substring($date,6,2)"/>.<xsl:value-of select="substring($date,1,4)"/> 
</xsl:template>

<xsl:template name="dateFormat3">
  <xsl:param name="date"/>
	<xsl:value-of select="substring($date,9,2)"/>&nbsp;<xsl:call-template name="month"><xsl:with-param name="month" select="substring($date,6,2)"/></xsl:call-template>&nbsp;<xsl:value-of select="substring($date,1,4)"/> г. 
</xsl:template>

<xsl:template name="dateFormat4">
  <xsl:param name="date"/>	
  <xsl:value-of select="substring($date,12,2)"/>:<xsl:value-of select="substring($date,15,2)"/> 
</xsl:template>

<xsl:template name="dateFormat5">
  <xsl:param name="date"/>
  <span><xsl:call-template name="month2"><xsl:with-param name="month" select="substring($date,6,2)"/></xsl:call-template></span>
	<xsl:value-of select="substring($date,9,2)"/>
</xsl:template>

<xsl:template name="month">
<xsl:param name="month"/>
<xsl:choose>
	<xsl:when test="$month='01'">января</xsl:when>
	<xsl:when test="$month='02'">февраля</xsl:when>
	<xsl:when test="$month='03'">марта</xsl:when>
	<xsl:when test="$month='04'">апреля</xsl:when>
	<xsl:when test="$month='05'">мая</xsl:when>
	<xsl:when test="$month='06'">июня</xsl:when>
	<xsl:when test="$month='07'">июля</xsl:when>
	<xsl:when test="$month='08'">августа</xsl:when>
	<xsl:when test="$month='09'">сентября</xsl:when>
	<xsl:when test="$month='10'">октября</xsl:when>
	<xsl:when test="$month='11'">ноября</xsl:when>
	<xsl:when test="$month='12'">декабря</xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template name="month2">
<xsl:param name="month"/>
<xsl:choose>
	<xsl:when test="$month='01'">Январь</xsl:when>
	<xsl:when test="$month='02'">Февраль</xsl:when>
	<xsl:when test="$month='03'">Март</xsl:when>
	<xsl:when test="$month='04'">Апрель</xsl:when>
	<xsl:when test="$month='05'">Май</xsl:when>
	<xsl:when test="$month='06'">Июнь</xsl:when>
	<xsl:when test="$month='07'">Июль</xsl:when>
	<xsl:when test="$month='08'">Август</xsl:when>
	<xsl:when test="$month='09'">Сентябрь</xsl:when>
	<xsl:when test="$month='10'">Октябрь</xsl:when>
	<xsl:when test="$month='11'">Ноябрь</xsl:when>
	<xsl:when test="$month='12'">Декабрь</xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template name="footer">
	<footer class="footer footer-sub">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-sm-6">
					<p>© Baby Keeper</p>
				</div>
				<div class="col-lg-6 col-sm-6">
					<p class="copyright"></p>
				</div>
			</div>
		</div>
	</footer>
</xsl:template>

<xsl:template name="indexBanner">
		<div class="banner-content">
			<div class="container">
				<div class="row">
					<!-- Start Header Text -->
					<div class="col-md-7 col-sm-7">
						<h1>Ваш ребенок будет в безопасности</h1>
						<p><b>Baby Follower</b> поможет Вам найти надежного человека, который будет провожать Вашего ребенка в школу, детский сад, или на кружек</p>
						<ul class="banner-list">
							<li><i class="fa fa-check"></i><b>Надежно:</b> все провожатые проходят собседование по skype</li>
							<li><i class="fa fa-check"></i><b>Удобно:</b> наш сервис позволяет быстро найти нужно Вам человека</li>
							<li><i class="fa fa-check"></i><b>Бесплатно:</b> наш сервис абсолютно бесплатен</li>
						</ul>
					</div>
<xsl:choose>
	<xsl:when test="$logined='true'">
	   <div class="col-lg-4 col-md-4 col-md-offset-1 col-sm-5">
	     <a href="/search" class="searchCircleLink">Найти<br/>провожатого</a>
	   </div>
	</xsl:when>
	<xsl:otherwise>
					<div class="col-lg-4 col-md-4 col-md-offset-1 col-sm-5">
						<div class="banner-form">
							<div class="form-title">
								<h2>Войти или <a href="/reg" class="reg">присоединиться</a></h2>
							</div>
							<div class="form-body">
								<form id="loginForm" class="form" method="post">
								  <div class="btn navbar-btn btn-block btn-social btn-facebook" id="fbAuth" url="{/PAGE/CURRENT_STATE/PHP/@FB_AUTH}"><i class="fa fa-facebook"></i> Facebook <i class="pre"></i></div> 
								  <div class="btn navbar-btn btn-block btn-social btn-vk" id="vkAuth" url="{/PAGE/CURRENT_STATE/PHP/@VK_AUTH}"><i class="fa fa-vk"></i> Вконтакте <i class="pre"></i></div>
								  <div class="btn navbar-btn btn-block btn-social btn-dropbox" id="emailSignin"><i class="fa fa-envelope-o"></i> E-mail <i class="pre"></i></div>
								  <div class="email-signin">
								    <div class="infopanel"></div>
  									<div class="form-group">
  										<input name="email" type="text" class="form-control" required="" placeholder="E-mail"/>
  									</div>
  									<div class="form-group">
  										<input name="password" type="password" class="form-control" required="" placeholder="Пароль"/>
  									</div>  									
  									<div class="form-group">
  										<button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-send"></i> Войти</button>
  									</div>  									
  								</div>									
								</form>
							</div>
						</div>
					</div>
	</xsl:otherwise>
</xsl:choose>
				</div>
			</div>
		</div>
</xsl:template>

<xsl:template match="PROFILE/PM">
<xsl:choose>
	<xsl:when test="$logined='true'">
<div class="clearfix">
<div class="userContent bp clearfix">  
  <xsl:choose>
  	<xsl:when test="DIALOGS">
        <h2>Сообщения</h2>
<xsl:choose>
	<xsl:when test="DIALOGS/DIALOG">        
        <div class="dialogList">
          <xsl:for-each select="DIALOGS/DIALOG">
            <a href="/profile/pm/{@USERID}/" iid="{@USERID}">
              <xsl:attribute name="class">
              <xsl:choose>
              	<xsl:when test="@NEW='TRUE'">new item clearfix</xsl:when>
              	<xsl:otherwise>item clearfix</xsl:otherwise>
              </xsl:choose>
              </xsl:attribute>
            <div class="clearfix">
              <div class="userdata">
                <span class="userSmall">
                  <img src="{@IMAGE}" class="img-thumbnail"/>
                  <xsl:choose>
                  	<xsl:when test="@ONLINE"><div title="пользователь online" class="online"></div></xsl:when>
                  	<xsl:otherwise><div title="пользователь offline" class="offline"></div></xsl:otherwise>
                  </xsl:choose>
                  <div class="ulikes"><div><xsl:value-of select="@LIKES"/></div></div>
                </span>
                <span class="uname"><xsl:value-of select="@TITLE"/></span>
                <span class="date"><xsl:call-template name="dateFormat"><xsl:with-param name="date" select="@DATE"/></xsl:call-template></span>
              </div>
              <div class="mess">                
                <xsl:if test="@MYLAST='TRUE'">
                  <img src="{/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IMAGE_SMALL}" class="img-thumbnail"/>
                </xsl:if>
                <xsl:value-of select="@CONTENT"/></div>
              <div class="x" adresatid="{@USERID}" title="Удалить диалог"></div>
            </div></a>
          </xsl:for-each>            
        </div> 
	</xsl:when>
	<xsl:otherwise>
	 <p>У Вас пока нет диалогов. Чтобы написать человеку - войдите на его страницу и нажмите кнопку "сообщение".</p>
	</xsl:otherwise>
</xsl:choose>               	  
  	</xsl:when>
  	<xsl:otherwise>
  	      <h2>Сообщения <small><xsl:value-of select="USER/@TITLE"/></small></h2>          
          <div class="pmList">			
<xsl:choose>
	<xsl:when test="MESS">
            <xsl:for-each select="MESS">
              <xsl:sort select="@IID" data-type="number"/>
              <xsl:call-template name="pm_item"/>
      			</xsl:for-each>	
	</xsl:when>
	<xsl:otherwise><img src="/img/pm.jpg" class="zaglushka"/>
	</xsl:otherwise>
</xsl:choose>                                                                                                              
          </div>
          <div class="pmForm">
            <div class="he">
              <a href="{USER/@URL}" title="{USER/@TITLE}">
                <img src="{USER/@IMAGE_SMALL}" class="img-thumbnail"/>
                <xsl:choose>
                	<xsl:when test="USER/@ONLINE"><div title="пользователь online" class="online"></div></xsl:when>
                	<xsl:otherwise><div title="пользователь offline" class="offline"></div></xsl:otherwise>
                </xsl:choose>
              </a>
            </div>                      
            <div class="me">
              <a href="{/PAGE/CURRENT_STATE/PHP/DOCUMENT/@URL}" title="{/PAGE/CURRENT_STATE/PHP/DOCUMENT/@TITLE}"><img src="{/PAGE/CURRENT_STATE/PHP/DOCUMENT/@IMAGE_SMALL}" class="img-thumbnail"/></a>
            </div>
            <div class="form">
              <form id="pmForm">
                <input type="hidden" name="action" value="add"/>
                <input type="hidden" name="adresatid" value="{USER/@IID}"/>
                <div class="form-group">
                  <textarea name="content" class="form-control" required="true"></textarea>
                </div>
                <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-send"></i> Отправить</button>
                <button id="pmAttach" type="button" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-picture"></i> Прикрепить файл</button>
      					<div class="attachment show">
      					  <span class="fileinfo"></span>
      					</div>
              </form>              
            </div>                        
          </div>	          
  	</xsl:otherwise>
  </xsl:choose>
</div>
</div>
	</xsl:when>
	<xsl:otherwise>
    <div class="content bp clearfix">
      <h1>Необходимо авторизоваться</h1>
      <div class="cont">	
    </div></div>	
	</xsl:otherwise>
</xsl:choose>    
</xsl:template>

<xsl:template match="PM_GET">
  <xsl:for-each select="MESS">
    <xsl:call-template name="pm_item"/>
  </xsl:for-each>
</xsl:template>

<xsl:template name="pm_item">
  <div class="item clearfix" iid="{@IID}"><div class="clearfix">
    <div class="author"><a href="/u{@USERID}/"><img src="{@IMAGE}" class="img-thumbnail"/></a></div>
    <div class="mess">
      <span class="date"><xsl:call-template name="dateFormat"><xsl:with-param name="date" select="@DATE"/></xsl:call-template></span>
      <div class="uname"><xsl:value-of select="@USERNAME"/></div>              
      <xsl:value-of select="@CONTENT"/>
  		<xsl:for-each select="FILE[not(@TYPE='IMG')]">
  		 файл: <a href="{@URL}" target="_new"><xsl:value-of select="@NAME"/></a><br/>
  		</xsl:for-each>
      <xsl:if test="FILE[@TYPE='IMG']">      
    		<div class="clear"></div>
    		<xsl:for-each select="FILE[@TYPE='IMG']">
    		 <img src="{@URL}"/>
    		</xsl:for-each>
        <div class="clear"></div>  	        
      </xsl:if>       
    </div>
    <!--<div class="x" iid="{@IID}" title="Удалить"></div>-->
  </div></div>
</xsl:template>

</xsl:stylesheet>