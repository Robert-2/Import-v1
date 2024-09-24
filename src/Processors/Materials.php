<?php
declare(strict_types=1);

namespace ImportV1\Processors;

use ImportV1\Processor;
use Loxya\Models\Material;
use Loxya\Models\Park;
use Loxya\Models\Category;
use Loxya\Models\SubCategory;

class Materials extends Processor
{
    public $autoFieldsMap = [
        'id' => null,
        'label' => ['type' => 'string', 'field' => 'name'],
        'ref' => ['type' => 'string', 'field' => 'reference'],
        'panne' => ['type' => 'int', 'field' => 'out_of_order_quantity'],
        'externe' => null,
        'categorie' => null,
        'sousCateg' => null,
        'Qtotale' => ['type' => 'int', 'field' => 'stock_quantity'],
        'tarifLoc' => ['type' => 'float', 'field' => 'rental_price'],
        'valRemp' => ['type' => 'float', 'field' => 'replacement_price'],
        'dateAchat' => null,
        'ownerExt' => null,
        'remarque' => ['type' => 'string', 'field' => 'note'],

        // Added in _preProcess method
        'parkId' => ['type' => 'int', 'field' => 'park_id'],
        'categoryId' => ['type' => 'int', 'field' => 'category_id'],
        'subCategoryId' => ['type' => 'int', 'field' => 'sub_category_id'],
    ];

    public function __construct()
    {
        $this->model = new Material;
    }

    // ------------------------------------------------------
    // -
    // -    Specific Methods
    // -
    // ------------------------------------------------------

    protected function _preProcess(array $data): array
    {
        $transliterator = null;
        if (in_array('intl', get_loaded_extensions())) {
            $transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
        }

        return array_map(function ($item) use ($transliterator) {
            if ($transliterator) {
                $item['ref'] = $transliterator->transliterate($item['ref']);
            }

            $item['parkId'] = 1;
            if (!empty($item['ownerExt'])) {
                $parkIdent = ['name' => $item['ownerExt']];
                $park = Park::firstOrCreate($parkIdent, $parkIdent);
                $item['parkId'] = (int)$park->id;
            }

            $categoryIdent = ['name' => $item['categorie']];
            $category = Category::firstOrCreate($categoryIdent, $categoryIdent);
            $item['categoryId'] = (int)$category->id;

            $item['subCategoryId'] = null;
            if ($item['sousCateg'] !== null) {
                $subcategoryIdent = ['name' => $item['sousCateg']];
                $subCategoryData = [
                    'name' => $item['sousCateg'],
                    'category_id' => $item['categoryId'],
                ];
                $subcategory = SubCategory::firstOrCreate($subcategoryIdent, $subCategoryData);
                $item['subCategoryId'] = (int)$subcategory->id;
            }

            return $item;
        }, $data);
    }
}
