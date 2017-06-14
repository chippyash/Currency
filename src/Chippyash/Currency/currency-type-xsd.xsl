<xsl:stylesheet version="1.0"
                xmlns:xs="http://www.w3.org/2001/XMLSchema"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/">
        <xs:schema xmlns="http://schema.zf4.biz/schema/currency-type"
                   xmlns:xs="http://www.w3.org/2001/XMLSchema"
                   targetNamespace="http://schema.zf4.biz/schema/currency-type"
                   attributeFormDefault="unqualified"
                   elementFormDefault="qualified"
        >
            <xs:simpleType name="crcyType">
                <xs:annotation>
                    <xs:documentation>
                        Currencies supported by the application
                    </xs:documentation>
                </xs:annotation>
                <xs:restriction base="xs:string">
                    <xs:minLength value="3"/>
                    <xs:maxLength value="3"/>
                    <xsl:apply-templates/>
                    </xs:restriction>
            </xs:simpleType>
        </xs:schema>
    </xsl:template>

    <xsl:template match="//currency">
        <xs:enumeration value="{@code}"/>
    </xsl:template>
</xsl:stylesheet>