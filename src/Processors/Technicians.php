<?php
declare(strict_types=1);

namespace ImportV1\Processors;

use ImportV1\Processor;
use Loxya\Models\Person;
use Loxya\Models\Technician;

class Technicians extends Processor
{
    public $autoFieldsMap = [
        'id' => null,
        'idUser' => null,
        'surnom' => ['type' => 'string', 'field' => 'nickname'],
        'prenom' => null,
        'nom' => null,
        'email' => null,
        'tel' => null,
        'adresse' => null,
        'cp' => null,
        'ville' => null,
        'GUSO' => null,
        'CS' => null,
        'birthDay' => null,
        'birthPlace' => null,
        'habilitations' => null,
        'categorie' => null,
        'SECU' => null,
        'SIRET' => null,
        'assedic' => null,
        'intermittent' => null,
        'diplomes_folder' => null,

        // Added in _preProcess method
        'personId' => ['type' => 'int', 'field' => 'person_id'],
        'notes' => ['type' => 'string', 'field' => 'note'],
    ];

    public function __construct()
    {
        $this->model = new Technician;
    }

    // ------------------------------------------------------
    // -
    // -    Specific Methods
    // -
    // ------------------------------------------------------

    protected function _preProcess(array $data): array
    {
        return array_map(function ($item) {
            $personData = [
                'first_name' => $item['prenom'],
                'last_name' => $item['nom'],
                'email' => $item['email'],
                'phone' => $item['tel'],
                'street' => $item['adresse'],
                'postal_code' => $item['cp'],
                'locality' => $item['ville'],
            ];
            $personIdent = [
                'first_name' => $item['prenom'],
                'last_name' => $item['nom'],
                'email' => $item['email'],
            ];
            $person = Person::firstOrCreate($personIdent, $personData);
            $item['personId'] = (int)$person->id;

            $extraData = [
                'SECU' => "N° de Sécurité Sociale",
                'GUSO' => "N° GUSO",
                'CS' => "N° Congés Spectacle",
                'assedic' => "N° Pôle Emploi",
                'birthDay' => "Date de naissance",
                'birthPlace' => "Lieu de naissance",
                'habilitations' => "Habilitations",
                'categorie' => "Compétences",
                'intermittent' => "Est intermittent",
                'SIRET' => "N° SIRET",
            ];
            $notes = [];
            foreach ($extraData as $field => $info) {
                $value = $item[$field];
                $emptyValues = [null, '', 'N/A', 'undefined'];
                if (!in_array($value, $emptyValues)) {
                    if ($field === 'intermittent') {
                        $value = $value === '1' ? 'Oui' : 'Non';
                    }
                    $notes[] = sprintf('%s : %s', $info, $value);
                }
            }
            $item['notes'] = implode("\n", $notes);

            return $item;
        }, $data);
    }
}
