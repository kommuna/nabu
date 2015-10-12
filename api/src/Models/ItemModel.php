<?php

namespace Nabu\Models;

use Nabu\Exceptions\BadRequest400;
use Nabu\Exceptions\UnprocessableEntity422;


class ItemModel extends Model {

    static private $forbiddenTerms = [];


    protected $tableName = 't_item';

    protected $dbSettings;
    protected $logger;

    public function __construct($dbSettings = null, $logger = null) {

        $this->setFieldsValidators((new ItemValidators)->get());

        $this->dbSettings = $dbSettings;
        $this->logger = $logger;
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
                UnprocessableEntity422::throwException("'$textToCheck' contain forbidden '$term'");
            }
        }

        return true;
    }


    protected function afterValidateValues() {

        $tags = $this->getValue('tags');

        $tags = $tags ? '{'.implode(",",$tags).'}' : '{}';

        $this->setValue('tags', $tags);

    }

    protected function beforeValidateValues() {

        $siteCode = $this->getValue('site');

        $siteId = null;

        if($siteCode) {
            $siteModel = new SiteModel($this->dbSettings, $this->logger);
            $siteId = $siteModel->getByCode($siteCode);

            if(!$siteId) {
                BadRequest400::throwException("Site with code '$siteCode' doesn't registered!");
            }
        }

        $this->setValue('site_id', $siteId);

    }


}