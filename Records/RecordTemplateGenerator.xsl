<?xml version="1.0" encoding="UTF-8" ?>


<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" indent="yes"/>
	
<xsl:template match="/records">
	<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified" attributeFormDefault="unqualified">

		<xsl:for-each select="record">

			<xs:complexType>

				<xsl:attribute name="name">
					<xsl:value-of select="translate(name, ' ','')"/>
				</xsl:attribute>

		        <xs:sequence>
		        	<xs:element name="RecordType" type="Numeric" />
		        	
		        	<xsl:for-each select="fields/field">

					<xs:element>
						<xsl:attribute name="name">
							<xsl:value-of select="translate(name, ' ','')"/>
						</xsl:attribute>
						<xsl:attribute name="type">
							<xsl:choose>
								<xsl:when test="type='N'">Numeric</xsl:when>
								<xsl:when test="type='NB'">NumericBlank</xsl:when>
								<xsl:when test="type='A'">Alphameric</xsl:when>
								<xsl:when test="type='AN'">Alphanumeric</xsl:when>
								<xsl:when test="type='B'">Blank</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="type" />
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:if test="usage!='M'">
							<xsl:attribute name="minOccurs">0</xsl:attribute>
						</xsl:if>
	        		</xs:element>
	        		
        			</xsl:for-each>
        			
		        </xs:sequence>
		        <xs:attribute name="type" type="RecordType" use="required" />
   			</xs:complexType>
		</xsl:for-each>
	</xs:schema>
</xsl:template>

</xsl:stylesheet>


