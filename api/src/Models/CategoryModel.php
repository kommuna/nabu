<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;
use RestModel\Models\Model;


class CategoryModel extends Model {

    protected $tableName = 't_category';


    public function getFieldsValidators() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullType()),
            'code' => v::stringType()->length(1,32)->notEmpty(),
            'name' => v::stringType()->length(1,256)->notEmpty(),
            'description' => v::stringType()->notEmpty(),
            'priority' => v::intVal()->notEmpty(),
            'visible' => v::bool()
        ];
    }


}