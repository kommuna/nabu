<?php

namespace Nabu\Models;

use RestModel\Exceptions\ModelException;
use RestModel\Exceptions\UnprocessableEntity422;
use RestModel\Models\Model;


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

        $siteId = $this->getValue('site_id');

        $siteModel = new SiteModel($this->dbSettings, $this->logger);

        if($this->getValue('site_id')) {
            if(!$this->validateValue('site_id', $siteId)) {
                return;
            }
            $site = $siteModel->getById($siteId);
            if(!$site) {
                ModelException::throwException("Site with id '$siteId' doesn't registered!");
            }
            return;
        }

        $siteCode = $this->getValue('site');

        $siteId = null;

        if($siteCode) {

            $site = $siteModel->getByCode($siteCode);

            if(!$site) {
                ModelException::throwException("Site with code '$siteCode' doesn't registered!");
            }

            $siteId = $site['id'];
            $this->setValue('site_id', $siteId, true);

        }


        unset($this->fields['site']);
        unset($this->values['site']);

    }


}