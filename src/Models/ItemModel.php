<?php

namespace Models;

use Respect\Validation\Validator as v;


class ItemModel extends Model {

    protected $tableName = 't_item';


    public function getFieldsValidators() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => v::string()->length(1,32)->notEmpty(),
            'category_id' => v::int()->positive()->min(1),
            'name' => v::string()->length(1,256)->notEmpty(),
            'description' => v::string(),
            'posted_on' => v::oneOf(v::date(), v::nullValue()),
            'deleted_on' => false,
        ];
    }


}