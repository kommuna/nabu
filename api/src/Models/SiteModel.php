<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;
use RestModel\Models\Model;


class SiteModel extends Model {

    protected $tableName = 't_site';


    public function getFieldsValidators() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => v::string()->length(1,256)->notEmpty(),
            'title' => v::string()->length(1,256)->notEmpty(),
            'url' => v::string()->notEmpty(),
            'bgcolor' => v::oneOf(v::string()->length(0, 7, true), v::nullValue()),
            'is_logoexists' => v::oneOf(v::bool()->notEmpty(), v::nullValue()),
        ];
    }


}