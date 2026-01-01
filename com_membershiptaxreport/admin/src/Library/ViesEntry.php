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
            'USt-IdNr.',
            'Betrag (Euro)',
            'Art der Leistung'
        ];
    }

    private function getFixedVATNumber():string {
        if (str_starts_with($this->vatNumber, $this->countryCode)) {
            return $this->vatNumber;
        }
        return $this->countryCode . $this->vatNumber;
    }

    public function getCSVLine() {

        return [
            $this->getFixedVATNumber(),
            (int)round($this->netAmount),
            $this->type
        ];
    }
}
