<?php

namespace Nabu\Transformers;

use League\Fractal;

class ItemTransformer extends Fractal\TransformerAbstract {


    public function transform($itemArray) {

        return [

            'id' => (int)$itemArray['id'],
            'code' => $itemArray['code'],
            'category_id' => (int)$itemArray['category_id'],
            'name' => $itemArray['name'],
            'description' => $itemArray['description'],
            'posted_on' => $itemArray['posted_on'],
            'activated_on' => $itemArray['activated_on'],
            'deleted_on' => $itemArray['deleted_on'],
            'is_param_1' => (bool)$itemArray['is_param_1'],
            'tags' => $itemArray['tags'],

        ];
    }
}