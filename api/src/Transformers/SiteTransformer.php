<?php

namespace Nabu\Transformers;

use League\Fractal;

class SiteTransformer extends Fractal\TransformerAbstract {


    public function transform($categoryArray) {

        return [

            'id' => (int)$categoryArray['id'],
            'code' => $categoryArray['code'],
            'title' => $categoryArray['title'],
            'url' => $categoryArray['url'],
        ];

    }
}