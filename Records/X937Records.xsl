<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" indent="yes"/>

<xsl:template match="/records">
    
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified" attributeFormDefault="unqualified">

    <!-- Loop through all the records and generate subsitution groups -->
    <xsl:text>&#10;</xsl:text><xsl:comment>***************************</xsl:comment>
    <xsl:text>&#10;</xsl:text><xsl:comment> Record Subsitution Groups </xsl:comment>
    <xsl:text>&#10;</xsl:text><xsl:comment>***************************</xsl:comment>
    <xsl:for-each select="record">
        <!-- Spaces are not valid in element names, so strip them -->
        <xsl:variable name="recordName" select="translate(name, ' ', '')" />
        <xsl:text>&#10;</xsl:text><xsl:comment><xsl:value-of select="$recordName" /></xsl:comment>
        <xs:element name="{translate(name, ' ', '')}Abstract" abstract="true" />
        <xs:element name="{translate(name, ' ', '')}" type="{translate(name, ' ', '')}" substitutionGroup="{translate(name, ' ', '')}Abstract" />
        <xs:element name="{translate(name, ' ', '')}Stub" type="stubRecord" substitutionGroup="{translate(name, ' ', '')}Abstract" />
        <xsl:text>&#10;</xsl:text>
    </xsl:for-each>
    
    <!-- Stub records definition -->
    <xsl:text>&#10;</xsl:text><xsl:comment>*****************************</xsl:comment>
    <xsl:text>&#10;</xsl:text><xsl:comment> Stub Record Type Definition </xsl:comment>
    <xsl:text>&#10;</xsl:text><xsl:comment>*****************************</xsl:comment>
    <xs:complexType name="stubRecord">
        <xs:attribute name="type" type="RecordType" use="required"/>
    </xs:complexType>
    <xsl:text>&#10;</xsl:text>

    <!-- Loop through all the records and generate definitions groups -->
    <xsl:text>&#10;</xsl:text><xsl:comment>*************************</xsl:comment>
    <xsl:text>&#10;</xsl:text><xsl:comment> Record Type Definitions </xsl:comment>
    <xsl:text>&#10;</xsl:text><xsl:comment>*************************</xsl:comment>
    <xsl:for-each select="record">
        <!-- Spaces are not valid in element names, so strip them, store to a variable. -->
        <xsl:variable name="recordName" select="translate(name, ' ', '')" />
        <xsl:text>&#10;</xsl:text><xsl:comment><xsl:value-of select="$recordName" /></xsl:comment>
        <xs:complexType name="{$recordName}">
            <xs:sequence>
                <xs:element name="RecordType" type="N" />

                <!-- loop through all the elements fields and build their definitions -->
                <xsl:for-each select="fields/field">
                    <!-- Spaces are not valid in element names, so strip them, store to a variable. -->
                    <xsl:variable name="fieldName" select="translate(name, ' ', '')" />
                    <xsl:text>&#10;</xsl:text><xsl:comment><xsl:value-of select="$recordName" />/<xsl:value-of select="$fieldName" /></xsl:comment>
                    <xs:element name="{$fieldName}">
                        <!-- If a field is not mandatory, it may be omitted in our XML, so minOccurs = 0 -->
                        <xsl:if test="usage!='M'">
                            <xsl:attribute name="minOccurs">0</xsl:attribute>
                        </xsl:if>

                        <xs:simpleType>
                            <xs:restriction base="{type}" ><!-- Type here will corespond to type restrictions in the FieldTypes.xsd -->

                            <!-- add a restriction based upon the maximum valid length of a variable -->
                            <xsl:if test="length">
                                <xs:maxLength value="{length}" />
                            </xsl:if>

                            <!-- if we have a comprehensive dictonary, then we want to add its values as a enumeration restriction -->
                            <xsl:if test="dictonary/@comprehensive='true'">
                                <xsl:for-each select="dictonary/value">
                                    <xs:enumeration value="{@key}" />
                                </xsl:for-each>
                            </xsl:if>

                            <!-- if we have a reference to an external dictonary, then we want to add that dictonaries restrictions as well -->
                            <xsl:if test="dictonary/@ref and dictonary/@comprehensive='true'">
                                <!-- get the dictonary that matches -->
                                <xsl:variable name="dictId" select="dictonary/@ref" />
                                <!-- this will loop *only* on our matching dictonary -->
                                <xsl:for-each select="//dictonary[@id = $dictId]/value">
                                        <xs:enumeration value="{@key}" />
                                </xsl:for-each>
                            </xsl:if>

                            </xs:restriction>
                        </xs:simpleType>
                    </xs:element>
                <xsl:text>&#10;</xsl:text>
                </xsl:for-each>
                
            </xs:sequence>
            <xs:attribute name="type" type="RecordType" use="required" />
        </xs:complexType>
        
        <xsl:text>&#10;</xsl:text><xsl:comment><xsl:value-of select="$recordName" /> End</xsl:comment>
        <xsl:text>&#10;</xsl:text>
    </xsl:for-each>
</xs:schema>
</xsl:template>

</xsl:stylesheet>
