<?php
declare(strict_types=1);

namespace ImportV1\Processors;

use ImportV1\Processor;
use Loxya\Models\Company;

class Companies extends Processor
{
    public $autoFieldsMap = [
        'id' => null,
        'label' => ['type' => 'string', 'field' => 'legal_name'],
        'SIRET' => null,
        'type' => null,
        'NomRS' => null,
        'interlocteurs' => null,
        'adresse' => ['type' => 'string', 'field' => 'street'],
        'codePostal' => ['type' => 'string', 'field' => 'postal_code'],
        'ville' => ['type' => 'string', 'field' => 'locality'],
        'email' => ['type' => 'string', 'field' => 'email'],
        'tel' => ['type' => 'string', 'field' => 'phone'],
        'listePlans' => null,
        'decla' => null,
        'remarque' => null,

        // Added in _preProcess method
        'notes' => ['type' => 'string', 'field' => 'note'],
    ];

    public function __construct()
    {
        $this->model = new Company;
    }

    // ------------------------------------------------------
    // -
    // -    Specific Methods
    // -
    // ------------------------------------------------------

    protected function _preProcess(array $data): array
    {
        return array_map(function ($item) {
            $extraData = [
                'SIRET' => "N° SIRET",
                'type' => "Type",
                'label' => "Label",
                'interlocteurs' => "Interlocuteurs (ID bénéficiaires Robert v1)",
                'decla' => "Déclaration",
                'remarque' => "Remarques",
            ];
            $notes = [];
            foreach ($extraData as $field => $info) {
                $value = $item[$field];
                $emptyValues = [null, '', 'N/A', 'undefined'];
                if (!in_array($value, $emptyValues)) {
                    $notes[] = sprintf('%s : %s', $info, $value);
                }
            }
            $item['notes'] = implode("\n", $notes);

            return $item;
        }, $data);
    }
}
