<?php

namespace Nabu\Models;

use RestModel\Exceptions\ModelException;
use RestModel\Exceptions\UnprocessableEntity422;
use RestModel\Models\Model;
use PDO;


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

        $actresses = $this->getValue('actresses');
        $actresses = $actresses ? '{'.implode(",",$actresses).'}' : '{}';
        $this->setValue('actresses', $actresses);

        $ftags = $this->getValue('forced_tags');
        $ftags = $ftags ? '{'.implode(",",$ftags).'}' : '{}';
        $this->setValue('forced_tags', $ftags);

        $factresses = $this->getValue('forced_actresses');
        $factresses = $factresses ? '{'.implode(",",$factresses).'}' : '{}';
        $this->setValue('forced_actresses', $factresses);

        $tagsId = $this->getValue('tags_id');
        $tagsId = $tagsId ? '{'.implode(",",$tagsId).'}' : '{}';
        $this->setValue('tags_id', $tagsId);

        $actressesId = $this->getValue('actresses_id');
        $actressesId = $actressesId ? '{'.implode(",",$actressesId).'}' : '{}';
        $this->setValue('actresses_id', $actressesId);
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

    public function getMoviesListForRematchAsGenerator($limit = 100)
    {
        
        $sql = 'SELECT id, name, description, array_to_json(tags) as tags, array_to_json(actresses) as actresses, 
                array_to_json(forced_tags) as forced_tags, array_to_json(forced_actresses) as forced_actresses
                FROM t_item i 
                ORDER BY tagged_on, id
                LIMIT ' . (int)$limit;

        $pdo = $this->getPDO();

        $result = $pdo->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            error_log($row['id']);
            $return = [];
            $return['id'] = $row['id'];
            $return['match_data'] = [];
            $return['match_data']['texts'] = [$row['name'], $row['description']];

            $tags = $row['tags'] ? json_decode($row['tags'], JSON_OBJECT_AS_ARRAY) : [];
            $tags = array_map(function ($item) {
                return ['type' => 't', 'value' => $item];
            }, $tags);

            $actresses = $row['actresses'] ? json_decode($row['actresses'], JSON_OBJECT_AS_ARRAY) : [];
            $actresses = array_map(function ($item) {
                return ['type' => 'a', 'value' => $item];
            }, $actresses);

            $ftags = $row['forced_tags'] ? json_decode($row['forced_tags'], JSON_OBJECT_AS_ARRAY) : [];
            $ftags = array_map(function ($item) {
                return ['type' => 'ft', 'value' => $item];
            }, $ftags);

            $factresses = $row['forced_actresses'] ? json_decode($row['forced_actresses'], JSON_OBJECT_AS_ARRAY) : [];
            $factresses = array_map(function ($item) {
                return ['type' => 'fa', 'value' => $item];
            }, $factresses);

            $tags = array_merge($tags, $actresses, $ftags, $factresses);

            if ($tags) {
                $return['match_data']['tag_originals'] = $tags;
            }

            yield $return;
        }
    }


}