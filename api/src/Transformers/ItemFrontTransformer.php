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
            'promo_title' => $itemArray['promo_title'],
            'promo_url' => $itemArray['promo_url'],
            'posted_on' => $itemArray['posted_on'],
            'views_counter' => (int)$itemArray['views_counter'],
            'votes_positive' => (int)$itemArray['votes_positive'],
            'votes_negative' => (int)$itemArray['votes_negative'],
            'favorites_counter' => (int)$itemArray['favorites_counter'],
            'activated_on' => $itemArray['activated_on'],
            'deleted_on' => $itemArray['deleted_on'],
            'is_param_1' => (bool)$itemArray['is_param_1'],
            'tags' => $itemArray['tags'],
            'site' => isset($itemArray['site_code']) ? $itemArray['site_code'] : null,

        ];


    }
}