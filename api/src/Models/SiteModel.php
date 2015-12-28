<?php

namespace Nabu\Models;

use Respect\Validation\Validator as v;
use RestModel\Exceptions\ModelException;
use RestModel\Models\Model;


class SiteModel extends Model {

    protected $tableName = 't_site';


    public function getFieldsValidators() {

        return [
            'id' => v::oneOf(v::numeric()->positive(), v::nullValue()),
            'code' => v::alnum('_-')->noWhitespace()->length(1,256)->notEmpty(),
            'title' => v::string()->length(1,256)->notEmpty(),
            'url' => v::string()->notEmpty(),
            'bg_color' => v::oneOf(v::string()->length(0, 7, true), v::nullValue()),
            'is_hidden' => v::oneOf(v::bool(), v::nullValue()),
            'is_logo_exist' => v::oneOf(v::bool(), v::nullValue()),
        ];
    }

    protected function beforeDelete($id) {

        $itemModel = new ItemModel($this->dbSettings, $this->logger);

        $items = $itemModel->getByField('site_id', $id);

        if($items) {
            ModelException::throwException("There are albums which relate to site with id = '{$id}'");
        }
    }


}