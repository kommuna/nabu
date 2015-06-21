<?php

namespace Nabu\Models;

use Nabu\Exceptions\Unavalable451;


class ItemModel extends Model {

    static private $forbiddenTerms = [];


    protected $tableName = 't_item';

    public function __construct($dbSettings = null, $logger = null) {

        $this->setFieldsValidators((new ItemValidators)->get());

        parent::__construct($dbSettings, $logger);

    }

    static public function setForbiddenTerms($forbiddenTerms) {
        self::$forbiddenTerms = $forbiddenTerms;
    }

    static public function getForbiddenTerms() {
        return self::$forbiddenTerms;
    }


    static public function isForbiddenTermExists($textToCheck) {


        foreach(self::$forbiddenTerms as $term) {
            if(mb_strpos($textToCheck, $term) !== false) {
                Unavalable451::throwException("'$textToCheck' contain forbidden '$term'");
            }
        }

        return true;
    }


    protected function afterValidateValues() {

        $tags = $this->getValue('tags');
        if($tags) {
            $tags = '{'.implode(",",$tags).'}';
        }
        $this->setValue('tags', $tags);
    }


}