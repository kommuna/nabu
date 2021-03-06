<?php

namespace Nabu\Transformers;

use League\Fractal;

class CategoryTransformer extends Fractal\TransformerAbstract {


    public function transform($categoryArray) {

        return [

            'id' => (int)$categoryArray['id'],
            'code' => $categoryArray['code'],
            'name' => $categoryArray['name'],
            'description' => $categoryArray['description'],
            'priority' => (int)$categoryArray['priority'],
            'visible' => (bool)$categoryArray['visible'],

        ];

    }
}