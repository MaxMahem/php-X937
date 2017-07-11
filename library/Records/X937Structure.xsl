<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                              xmlns:xs="http://www.w3.org/2001/XMLSchema">

    <!-- some transformers do not seem to indent this right so we omit indentation -->
    <xsl:output method="xml" indent="yes"/>
    <xsl:strip-space elements="*" />
    
    <!-- the root transformation -->
    <xsl:template match="/file">
        <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified" attributeFormDefault="unqualified">
            <!-- include our dependant xsds -->
            <xs:include schemaLocation="Fields\FieldTypes.xsd"/>
            <xs:include schemaLocation="{@filenameRoot}-Records.xsd"/>
            
            <xsl:apply-templates/>
        </xs:schema>
    </xsl:template>
    
    <!-- transformatin for all our control elements -->
    <xsl:template match="control">
        <xs:element name="{@name}"> 
            <!-- if our control is conditional, then minOccurs is 0 -->
            <!-- otherwise we don't need to do anything since the default is 1 -->
            <xsl:if test="@usage='C'">
                <xsl:attribute name="minOccurs">0</xsl:attribute>
            </xsl:if>
            
            <!-- if our control has a maxOccurs, then we pass along its value. -->
            <!-- otherwise we don't need to do anything since the default is 1 -->
            <xsl:if test="@maxOccurs">
                <xsl:attribute name="maxOccurs">
                    <xsl:value-of select="@maxOccurs" />
                </xsl:attribute>
            </xsl:if>
            
            
            <!-- Allow for child element definition -->
            <xsl:comment><xsl:value-of select="@name"/> start</xsl:comment>
            <xs:complexType>
                <xs:sequence>
                    <!-- lookout! Recurion occurs here -->
                    <xsl:apply-templates />
                </xs:sequence>
                <!-- if the control has an id, then the product must have one as well -->
                <xsl:if test="@id">
                    <xs:attribute name="id"/>
                </xsl:if>
            </xs:complexType>
            
            <!-- if the control has a child control with an id we want to enforce a uniquness constraint -->
            <xsl:if test="control/@id">
                <xsl:comment><xsl:value-of select="control/@name"/> uniquness constraint</xsl:comment>
                <xs:unique name="unique{control/@name}">
                    <xs:selector xpath="{control/@name}"/>
                    <xs:field xpath="@id"/>
                </xs:unique>
            </xsl:if>
            
        </xs:element>
        
        <xsl:comment><xsl:value-of select="@name"/> end</xsl:comment>
    </xsl:template>
    
    <!-- transformatin for all our record elements -->
    <xsl:template match="record">
        <!-- records get ref's instead of names+Abstract, for subsitution groups -->
        <xs:element ref="{@name}Abstract">
            <!-- if our record is conditional, then minOccurs is 0 -->
            <!-- otherwise we don't need to do anything since the default is 1 -->
            <xsl:if test="@usage='C'">
                <xsl:attribute name="minOccurs">0</xsl:attribute>
            </xsl:if>
            
            <!-- if our record is forbidden, then it may not occur. Set maxOccurs to 0 -->
            <xsl:if test="@usage='F'">
                <xsl:attribute name="maxOccurs">0</xsl:attribute>
            </xsl:if>
            
            <!-- if our records has a maxOccurs, then we pass along its value. -->
            <!-- otherwise we don't need to do anything since the default is 1 -->
            <xsl:if test="@maxOccurs">
                <xsl:attribute name="maxOccurs">
                    <xsl:value-of select="@maxOccurs" />
                </xsl:attribute>
            </xsl:if>
        </xs:element>
    </xsl:template>

</xsl:stylesheet>
