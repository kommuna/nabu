<?php

namespace Nabu\Models;


class ItemModel extends Model {

    protected $tableName = 't_item';

    public function __construct($dbSettings = null, $logger = null) {

        $this->setFieldsValidators((new ItemValidators)->get());

        parent::__construct($dbSettings, $logger);

    }


    protected function afterValidateValues() {

        $tags = $this->getValue('tags');
        if($tags) {
            $tags = '{'.implode(",",$tags).'}';
        }
        $this->setValue('tags', $tags);
    }


}