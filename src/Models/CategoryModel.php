<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;


class CategoryModel extends Model {

    protected $tableName = 't_category';


    public function getFieldsValidators() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => v::string()->length(1,32)->notEmpty(),
            'name' => v::string()->length(1,256)->notEmpty(),
            'description' => v::string()->notEmpty(),
            'priority' => v::int()->notEmpty(),
            'visible' => v::bool()->notEmpty()
        ];
    }


}