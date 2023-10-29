<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Library;

class ViesEntry {
    private $countryCode;
    private $vatNumber;
    private $type = 'L';
    private $netAmount;


    public function __construct($countryCode, $vatNumber, $netAmount)
    {

        if ($countryCode == 'GR') {
            $countryCode = 'EL';
        }

        $this->countryCode = $countryCode;
        $this->vatNumber = $vatNumber;
        $this->netAmount = $netAmount;
    }

    public function addNetAmount(int $netAmount) {
        $this->netAmount += $netAmount;
    }

    public static function getCSVHeader() {
        return [
            'Länderkennzeichen',
            'USt-IdNr.',
            'Betrag (Euro)',
            'Art der Leistung'
        ];
    }

    public function getCSVLine() {
        return [
            $this->countryCode,
            $this->vatNumber,
            (int)round($this->netAmount),
            $this->type
        ];
    }
}
