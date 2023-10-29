<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Library;

class TaxCountry {
    private $countryCode;
    private $taxRate;
    private $taxType;
    private $netAmount = 0;
    private $taxAmount = 0;

    public function __construct($countryCode, $taxRate, $taxType)
    {
        $this->taxRate = $taxRate;
        $this->countryCode = $countryCode;
        $this->taxType = $taxType;
    }

    public function addEntry($netAmount, $taxAmount) {
        if (isset($netAmount)) {
            $this->netAmount += $netAmount;
        }
        if (isset($taxAmount)) {
            $this->taxAmount += $taxAmount;
        }
    }

    public static function getCSVHeader() {
        return [
            'Satzart',
            'Land des Verbrauchs',
            'Umsatzsteuertyp',
            'Umsatzsteuersatz',
            'Steuerbemessungsgrundlage, Nettobetrag',
            'Umsatzsteuerbetrag'
        ];
    }

    public function getCSVLine_Satzart_1() {
        return [
            '1',
            $this->countryCode
        ];
    }

    public function getCSVLine_Satzart_2() {
        return [
            '2',
            $this->countryCode,
            $this->taxType,
            number_format((float)$this->taxRate, 2, '.', ''),
            number_format((float)$this->netAmount, 2, '.', ''),
            number_format((float)$this->taxAmount, 2, '.', '')
        ];
    }
}