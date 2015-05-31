<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;


class ItemValidators {


    public function get() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => false,
            'category_id' => v::int()->positive()->min(0),
            'name' => v::string()->length(1,256)->notEmpty(),
            'description' => v::oneOf(v::string(), v::nullValue()),
            'promo_title' => v::oneOf(v::string(), v::nullValue()),
            'promo_url' => v::oneOf(v::string(), v::nullValue()),
            'tags' => v::oneOf(v::arr(), v::nullValue()),
            'posted_on' => v::oneOf(v::date(), v::nullValue()),
            'activated_on' => v::oneOf(v::date(), v::nullValue()),
            'is_param_1' => v::oneOf(v::bool(), v::nullValue()),
            'deleted_on' => false,
        ];
    }


}