<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;
use RestModel\Models\Model;


class TagModel extends Model {

    protected $tableName = 't_recentsearchtext';

    public function getFieldsValidators()
    {

        return [
            'id' => v::oneOf(v::notEmpty()->numeric()->positive(), v::nullType()),
            'hash' => v::stringType()->notEmpty(),
            'text' => v::oneOf(v::notEmpty()->stringType(), v::nullType()),
            'counter' => false
        ];
    }

    public function getMostSearched($limit = 20) {
        $rows = $this->getORM()->select('text')->order_by_desc('counter')->limit($limit)->find_array();
        return $rows ?: [];
    }


}