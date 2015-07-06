<?php

namespace Nabu\Models;

//use \Nabu\Core\apiParams;
use \Nabu\Exceptions\ModelException;
use ORM;

abstract class Model {

    protected static $connectionName = 'nabu';

    protected static $logger;

    protected $validators = [];
    protected $fields = [];
    protected $values = [];
    protected $errors = [];
    protected $postponeDeleteOnFieldName = 'deleted_on';

    public function getFieldsValidators() {
        return $this->validators;
    }

    public static function setLogger($logger) {

        self::$logger = $logger;

        ORM::configure('logging', true, self::$connectionName);
        ORM::configure('logger', function($log_string, $query_time) use ($logger) {
            $logger->addDebug($log_string . ' in ' . $query_time);
        }, self::$connectionName);

    }

    public static function init($dbSettings, $logger = null) {

        ORM::configure("pgsql:host={$dbSettings['host']};dbname={$dbSettings['dbname']}", null, self::$connectionName);
        ORM::configure('username', $dbSettings['username'], self::$connectionName);
        ORM::configure('password', $dbSettings['password'], self::$connectionName);


        if($logger && isset($dbSettings['debug']) && $dbSettings['debug']) {

            self::setLogger($logger);

        }

    }


    public function __construct($dbSettings = null, $logger = null) {

        if(!is_null($dbSettings)) {
            self::init($dbSettings, $logger);
        }

        $this->fields = $this->getFieldsValidators();

        //fields deleted_on can not be set|changed and not include on validation.
        if(isset($this->fields[$this->postponeDeleteOnFieldName])) {
            unset($this->fields[$this->postponeDeleteOnFieldName]);
        }

    }

    protected function setFieldsValidators($validators) {
        $this->validators = $validators;
    }

    protected function flushValues() {
        foreach($this->fields as $key => $v) {
            if($this->fields[$key]) {
                $this->setValue($key, null, true);
            }
        }
    }

    protected function setError($key, $value) {
        $this->errors[$key] = $value;
    }

    protected function getError($key) {
        return isset($this->errors[$key]) ? $this->errors[$key] : null;
    }

    protected function flushErrors() {
        $this->errors = [];
    }

    public function getErrors() {
        return $this->errors;
    }


    public function setValues($values, $updateMode = false) {

        $this->flushErrors();

        if(!$updateMode) {
            $this->flushValues();
        }

        foreach($values as $key => $value) {
            $this->setValue($key, $value, $updateMode);
        }

        return $this;
    }

    public function setValue($field, $value, $force = false) {
/*
        if(isset($this->fields[$field]) && !$this->fields[$field]) {
            return;
        }
*/

        if($force || (isset($this->fields[$field]) && $this->fields[$field])) {
            $this->values[$field] = $value;
        }
    }

    public function getValue($key) {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

    protected function beforeValidateValues() {}

    protected function afterValidateValues() {}


    public function validateValues() {

        $this->beforeValidateValues();

        foreach($this->values as $field => $val) {
            $this->validateValue($field, $this->values[$field]);
        }

        if($this->errors) {

            ModelException::throwException($this->errors);
        }

        $this->afterValidateValues();

        return $this;
    }

    protected function validateValue($field, $value) {

        if(!isset($this->fields[$field])) {

            $this->setError($field, "Field doesn't exist");

        } elseif($this->fields[$field] && !$this->fields[$field]->validate($value)) {

            $this->setError($field, "Missing or invalid value");
        }

    }

    public function save() {

        if(isset($this->values['id']) && $this->values['id']) {
            $row = ORM::for_table($this->tableName, self::$connectionName)->find_one($this->values['id']);
        } else {
            $row = ORM::for_table($this->tableName, self::$connectionName)->create();
        }

        foreach($this->values as $field => $value) {
            if($field === 'id') {
                continue;
            }
            $row->set($field, $value);
        }

        try {

            $row->save();

        } catch (\Exception $e) {
            ModelException::throwException($e->getMessage());
        }

        $this->values['id'] = $row->id();

        return $this->values['id'];


    }


    public function getByCode($code) {
        $row = ORM::for_table($this->tableName, self::$connectionName)->where_equal('code', $code);
        return $row ? $row->as_array() : [];
    }

    public function getById($id) {
        $row = ORM::for_table($this->tableName, self::$connectionName)->find_one($id);
        return $row ? $row->as_array() : [];
    }

    public function getTotalCount($params = null) {

        $orm = ORM::for_table($this->tableName, self::$connectionName);

        $orm = $this->applyFilterToORM($orm, $params);

        if(isset($this->fields[$this->postponeDeleteOnFieldName])) {
            $orm->where_null($this->postponeDeleteOnFieldName);
        }

        $count = $orm->count();
/*
        if($params && $params->getOffset()) {
            $count = $params->getOffset() < $count ? $count - $params->getOffset() : 0;
        }
*/

        return $count;

    }


    protected function applyFilterToORM(ORM $orm, $params = null) {

        if(is_null($params)) {
            return $orm;
        }

        $filter = $params->getFilter();
        $fields = $this->getFieldsValidators();

        foreach(array_keys($fields) as $field) {

            if(!isset($filter[$field])) {
                continue;
            } else {
                $fieldParams = $filter[$field];
            }

            $fromToFlag = false;

            if(is_array($fieldParams)) {

                if(isset($fieldParams['from']) && is_scalar($fieldParams['from'])) {

                    // Date fields should end by '_on' (posted_on)
                    if(substr($field, 0, 3) != 'is_' && substr($field, -3) == '_on') {

                        /* if(strpos($field, '+') !== false) {
                            $field = substr($field, 0, strpos($field, '+'));
                        }*/

                        $time = strtotime($fieldParams['from']);

                        $from = $time !== false ? date("Y-m-d H:i:s", $time) : false;
                    } else {
                        $from = $fieldParams['from'];
                    }

                    if($from !== false) {
                        $orm->where_gte($field, $from);
                        $fromToFlag = true;
                    }
                }


                if(isset($fieldParams['to']) && is_scalar($fieldParams['to'])) {

                    if(substr($field, 0, 3) != 'is_' && substr($field, -3) == '_on') {
                        $time = strtotime($fieldParams['to']);
                        $to = $time !== false ? date("Y-m-d H:i:s", $time) : false;
                    } else {
                        $to = $fieldParams['to'];
                    }

                    if($to !== false) {
                        $orm->where_lte($field, $to);
                        $fromToFlag = true;
                    }
                }

                if(!$fromToFlag) {
                    $orm->where_in($field, $fieldParams);
                }
            } else {

                // Logical fields should start by 'is_' (is_logo_on)
                if(substr($field, 0,3) == 'is_') {
                    $fieldParams = (int)($fieldParams);

                // Date fields should end by '_on' (posted_on)
                } elseif(substr($field, -3) == '_on') {
                    $time = strtotime($fieldParams);
                    $fieldParams = $time !== false ? date("Y-m-d H:i:s", $time) : false;
                }

                if(strpos($fieldParams, '%') !== false) {
                    $orm->where_like($field, $fieldParams);
                } else {
                    $orm->where_equal($field, $fieldParams);
                }

            }
        }

        return $orm;
    }

    protected function applyOrderToORM(ORM $orm, $params = null) {

        if(is_null($params)) {
            return $orm;
        }

        $orders = $params->getOrder();

        foreach($orders as $order) {

            if(!is_array($order)) {
                ModelException::throwException("Wrong 'order' parameter");
            }

            $orderField = array_keys($order);

            if(!is_array($orderField) || !isset($orderField[0])) {
                ModelException::throwException("Wrong 'order' parameter");
            }

            $orderField = $orderField[0];

            $fields = $this->getFieldsValidators();

            if(!isset($fields[$orderField])) {
                continue;
            }

            if(strtolower($order[$orderField]) == 'asc') {
                $orm->order_by_asc($orderField);
            }

            if(strtolower($order[$orderField]) == 'desc') {
                $orm->order_by_desc($orderField);
            }
        }

        return $orm;
    }

    public function getMany($params = null) {

        $orm = ORM::for_table($this->tableName, self::$connectionName);

        if(isset($this->fields[$this->postponeDeleteOnFieldName])) {
            $orm->where_null($this->postponeDeleteOnFieldName);
        }

        if($params && $params->getOffset()) {
            $orm->offset($params->getOffset());
        }

        if($params && $params->getLimit()) {
            $orm->limit($params->getLimit());
        }

        $orm = $this->applyFilterToORM($orm, $params);
        $orm = $this->applyOrderToORM($orm, $params);

        return $orm->find_array();
    }

    public function delete($id) {

        $row = ORM::for_table($this->tableName, self::$connectionName)->find_one($id);

        if($row) {
            try {
                $row->delete();
            } catch(\Exception $e) {
                ModelException::throwException($e->getMessage());
            }
        }

    }

    public function markAsDeleted($id) {

        $row = ORM::for_table($this->tableName, self::$connectionName)->find_one($id);

        if($row) {
            $row->set_expr($this->postponeDeleteOnFieldName, 'NOW()');
            try {
                $row->save();
            } catch(\Exception $e) {
                ModelException::throwException($e->getMessage());
            }
        }
    }

    public function get($id) {

        $item = $this->getById($id);

        if(!$item) {
            ModelException::throwException("Entity with id = $id doesn't exist!");
        }

        return $item;

    }

    public function add($data) {

        if(isset($data['id'])) {
            unset($data['id']);
        }

        $id = $this->setValues($data)->validateValues()->save();

        return $id;

    }

    public function edit($id, $data) {


        $item = $this->getById($id);

        if(!$item) {
            ModelException::throwException("Entity with id = $id doesn't exist!");
        }

        $data['id'] = $id;

        return $this->setValues($data, true)->validateValues()->save();

    }

}