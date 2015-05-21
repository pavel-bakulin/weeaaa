/PAGE/SECTION_LIST/SEARCHSECTIONS/SECTION[@SECTIONID=000]/@ACTIVE='TRUE'
/PAGE/SECTION_LIST/SEARCHSECTIONS/SECTION[@ACTIVE='TRUE']/@NAME
SEARCHRESULT/DOCUMENT/@DESCRIPTION

<xsl:for-each select="/PAGE/SECTION_LIST/SEARCHSECTIONS/SECTION">
  <li>
    <xsl:if test="@ACTIVE='TRUE'"><xsl:attribute name="class">current</xsl:attribute></xsl:if>
    <a href="{@URL}"><xsl:value-of select="@NAME"/></a></li>
</xsl:for-each>

<xsl:apply-templates select="SECTION_LIST"/>  
<xsl:apply-templates select="CONTENT"/>

<xsl:text disable-output-escaping="yes">
<![CDATA[

]]>
</xsl:text>

<xsl:text disable-output-escaping="yes">
<![CDATA[

]]>
</xsl:text>

<xsl:for-each select="">
</xsl:for-each>

<xsl:if test="">
</xsl:if>

<xsl:attribute name="class">              
  <xsl:choose>
  	<xsl:when test="@ACTIVE='TRUE'">selected-button</xsl:when>
  	<xsl:otherwise>button</xsl:otherwise>
  </xsl:choose>
</xsl:attribute>

<xsl:choose>
	<xsl:when test="">
	</xsl:when>
	<xsl:otherwise>
	</xsl:otherwise>
</xsl:choose>

<xsl:apply-templates select="CONTENT"/>
<xsl:apply-templates/>

<xsl:template match="TITLE_HELLO">
</xsl:template>

<xsl:value-of select="$vashakorzina"/>

<xsl:value-of select="@TITLE"/>
<xsl:value-of select="@NAME"/>

<xsl:call-template name=""/>

<xsl:variable name="varr">значение</xsl:variable>

<xsl:sort select="@SUM" order="descending" data-type="number"/>

<xsl:param name="xxx" select="'yyy'"/>
<xsl:with-param name="xxx" select="'yyy'"/>

<xsl:call-template name="underRubricatorPanel">
	<xsl:with-param name="captionImage" select="'znakom.jpg'"/>
	<xsl:with-param name="moreText" select="'Посмотреть все анкеты'"/>
	<xsl:with-param name="moreUrl" select="'?IID=5988'"/>
</xsl:call-template>

<NEWS>
<SEARCHRESULT 
 SECTIONID="224810"  
 DOCTYPE="News"
 COUNTONPAGE="4"
/>
</NEWS>

<BANNERS>
<SEARCHRESULT 
 SECTIONID="224803"  
 DOCTYPE="Banner" 
 RANDOM="TRUE" 
 COUNT="3" 
/>
</BANNERS>
