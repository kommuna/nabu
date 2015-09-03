<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;


class SiteModel extends Model {

    protected $tableName = 't_site';


    public function getFieldsValidators() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => v::string()->length(1,256)->notEmpty(),
            'title' => v::string()->length(1,256)->notEmpty(),
            'url' => v::string()->notEmpty()
        ];
    }


}