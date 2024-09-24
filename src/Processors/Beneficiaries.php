<?php
declare(strict_types=1);

namespace ImportV1\Processors;

use ImportV1\Processor;
use Loxya\Models\Beneficiary;
use Loxya\Models\Person;

class Beneficiaries extends Processor
{
    public $autoFieldsMap = [
        'id' => null,
        'label' => null,
        'firstName' => null,
        'lastName' => null,
        'adresse' => null,
        'codePostal' => null,
        'ville' => null,
        'email' => null,
        'tel' => null,
        'poste' => null,
        'remarque' => null,
        'nomStruct' => null,
        'typeRetour' => null,

        // Added in _preProcess method
        'personId' => ['type' => 'int', 'field' => 'person_id'],
    ];

    public function __construct()
    {
        $this->model = new Beneficiary;
    }

    // ------------------------------------------------------
    // -
    // -    Specific Methods
    // -
    // ------------------------------------------------------

    protected function _preProcess(array $data): array
    {
        return array_map(function ($item) {
            $nomPrenom = explode(' ', $item['nomPrenom']);
            $personData = [
                'first_name' => $nomPrenom[0],
                'last_name' => $nomPrenom[1],
                'email' => $item['email'],
                'phone' => $item['tel'],
                'street' => $item['adresse'],
                'postal_code' => $item['codePostal'],
                'locality' => $item['ville'],
            ];
            $personIdent = [
                'first_name' => $nomPrenom[0],
                'last_name' => $nomPrenom[1],
                'email' => $item['email'],
            ];
            $person = Person::firstOrCreate($personIdent, $personData);
            $item['personId'] = (int)$person->id;

            return $item;
        }, $data);
    }
}
