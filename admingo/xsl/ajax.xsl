<?xml version="1.0" encoding="utf-8" standalone="no"?>
<!DOCTYPE xsl:stylesheet [
	<!ENTITY nbsp "&#160;">
	<!ENTITY copy "&#169;">
]>

<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
	<xsl:output
		standalone="no"
		method = "html"
		media-type = "text/html"	
		encoding = "utf-8"	
		indent = "no"
		omit-xml-declaration = "no"	
/>

<xsl:template match='/'>
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="*|@*">
	<xsl:copy><xsl:apply-templates select="@*|node()"/></xsl:copy>
</xsl:template>

<xsl:template name="multyUser">
    <div class="well well-sm multyCard" title="{@LOWERTITLE}" approval="{@APPROVAL}">
      <xsl:if test="@APPROVAL"><i class="glyphicon glyphicon-ok approval" title="Одобрен"></i></xsl:if>
      <div class="media">
        <a class="pull-left" href="{@URL}"><img class="media-object" src="{@IMAGE}"/></a>
        <div class="media-body">
            <a href="{@URL}" class="media-heading"><xsl:value-of select="@TITLE"/></a>
    		    <p><span class="label label-info"><xsl:value-of select="@FEEDBACKS"/> отзывов</span>&nbsp;<span class="label label-warning">0 сделок</span></p>
            <p>
            <xsl:if test="not(@MY='TRUE')">
              <a href="/profile/pm/{@IID}/" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-comment"></span> Сообщение</a>&nbsp;
            </xsl:if>
              <a href="#" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-heart"></span> Запомнить</a>
            </p>
        </div>
      </div>
    </div>	
</xsl:template>

</xsl:stylesheet>