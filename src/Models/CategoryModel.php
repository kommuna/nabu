<?php

namespace Models;

use Respect\Validation\Validator as v;


class CategoryModel extends Model {

    protected $tableName = 'player';



    public function getFieldsValidators() {

        $htmlColorRegex = '/^#(?:[0-9a-fA-F]{3}){1,2}$/';

        return [
            'id' => false,
            'code' => v::string()->length(1,32)->notEmpty(),
            'name' => v::string()->length(1,256)->notEmpty(),
            'description' => v::string()->notEmpty(),
            'priority' => v::int()->notEmpty(),
            'visible' => v::bool()->notEmpty()
        ];
    }


    public function delete($id) {



    }





}