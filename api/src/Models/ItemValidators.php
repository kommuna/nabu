<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;



class ItemValidators {


    public function get() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullType()),
            'code' => false,
            'category_id' => v::intType()->positive()->min(0),
            'name' => v::stringType()->length(1,256)->notEmpty()->callback('Nabu\\Models\\ItemModel::isForbiddenTermExists'),
            'description' => v::oneOf(v::stringType(), v::nullType())->callback('Nabu\\Models\\ItemModel::isForbiddenTermExists'),
            'promo_title' => v::oneOf(v::stringType(), v::nullType()),
            'promo_url' => v::oneOf(v::stringType(), v::nullType()),
            'tags' => v::oneOf(v::arr(), v::nullType()),
            'actresses' => v::oneOf(v::arr(), v::nullType()),
            'forced_tags' => v::oneOf(v::arr(), v::nullType()),
            'forced_actresses' => v::oneOf(v::arr(), v::nullType()),
            'tags_id' => v::oneOf(v::arr(), v::nullType()),
            'actresses_id' => v::oneOf(v::arr(), v::nullType()),
            'views_counter' => false,
            'posted_on' => v::oneOf(v::date(), v::nullType()),
            'activated_on' => v::oneOf(v::date(), v::nullType()),
            'is_param_1' => v::oneOf(v::bool(), v::nullType()),
            'site_id' => v::oneOf(v::intType(), v::nullType()),
            // Will be deleted after 'beforeValidateValues' call.
            'site' => v::oneOf(v::stringType(), v::nullType()),
            'external_code' => v::oneOf(v::stringType()->length(1,50), v::nullType()),
            'deleted_on' => false,
            'favorites_counter' => false,
            'votes_negative' => false,
            'votes_positive' => false,
            'tagged_on' => false,
        ];
    }


}