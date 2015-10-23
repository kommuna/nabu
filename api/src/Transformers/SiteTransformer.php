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
            'bg_color' => $siteArray['bg_color'],
            'is_logo_exist' => (bool)$siteArray['is_logo_exist'],
        ];

    }
}