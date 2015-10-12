<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;



class ItemValidators {


    public function get() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => false,
            'category_id' => v::int()->positive()->min(0),
            'name' => v::string()->length(1,256)->notEmpty()->callback('Nabu\\Models\\ItemModel::isForbiddenTermExists'),
            'description' => v::oneOf(v::string(), v::nullValue())->callback('Nabu\\Models\\ItemModel::isForbiddenTermExists'),
            'promo_title' => v::oneOf(v::string(), v::nullValue()),
            'promo_url' => v::oneOf(v::string(), v::nullValue()),
            'tags' => v::oneOf(v::arr(), v::nullValue()),
            'views_counter' => false,
            'posted_on' => v::oneOf(v::date(), v::nullValue()),
            'activated_on' => v::oneOf(v::date(), v::nullValue()),
            'is_param_1' => v::oneOf(v::bool(), v::nullValue()),
            'site_id' => v::oneOf(v::int(), v::nullValue()),
            'site_code' => false,
            'deleted_on' => false,
            'favorites_counter' => false,
            'votes_negative' => false,
            'votes_positive' => false,
        ];
    }


}