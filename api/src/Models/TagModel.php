<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;
use RestModel\Models\Model;


class TagModel extends Model {

    protected $tableName = 't_recentsearchtext';

    public function getFieldsValidators()
    {

        return [
            'id' => v::oneOf(v::notEmpty()->numeric()->positive(), v::nullValue()),
            'hash' => v::string()->notEmpty(),
            'text' => v::oneOf(v::notEmpty()->string(), v::nullValue()),
            'counter' => false
        ];
    }

    public function getMostSearched($limit = 20) {
        $rows = $this->getORM()->select('text')->order_by_desc('counter')->limit($limit)->find_array();
        return $rows ?: [];
    }


}