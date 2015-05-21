<?xml version="1.0" encoding="utf-8" standalone="no"?>
<!DOCTYPE xsl:stylesheet [
	<!ENTITY nbsp "&#160;">
	<!ENTITY copy "&#169;">
	<!ENTITY laquo "&#171;">
	<!ENTITY raquo "&#187;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output standalone="no" method="html" media-type="text/html" encoding="utf-8" indent="no" omit-xml-declaration="no"/>

<xsl:template match="/">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="*|@*">
	<xsl:copy><xsl:apply-templates select="@*|node()"/></xsl:copy>
</xsl:template>

<xsl:template match="COOLFILTER">
<xsl:for-each select="param[position()&lt;10]">
		<tr>
			<td bgColor="#f4f4f4" valign="top"><xsl:value-of select="@description"/>:</td>
			<td></td>
			<td>
        <select name="{@name}" id="{@name}">
            <option value="">--</option>
          <xsl:for-each select="value">
            <option value="{@value}"><xsl:value-of select="."/></option>
          </xsl:for-each>
        </select>
      </td>
		</tr>		
</xsl:for-each>
</xsl:template>

</xsl:stylesheet>