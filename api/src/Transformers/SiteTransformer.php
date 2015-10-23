<?php

namespace Nabu\Transformers;

use League\Fractal;

class SiteTransformer extends Fractal\TransformerAbstract {


    public function transform($siteArray) {

        return [

            'id' => (int)$siteArray['id'],
            'code' => $siteArray['code'],
            'title' => $siteArray['title'],
            'url' => $siteArray['url'],
            'bgcolor' => $siteArray['bgcolor'],
            'is_logoexist' => (bool)$siteArray['is_logoexist'],
        ];

    }
}