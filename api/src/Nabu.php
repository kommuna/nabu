<?php

namespace Nabu;

$loader = require './../vendor/autoload.php';

use Nabu\Exceptions\ModelException;
use Nabu\Models\CategoryModel as CM;
use Nabu\Models\ItemModel as IM;
use Nabu\Models\SolrModel as SM;
use Nabu\Exceptions\NabuException as E;


class Nabu {

    protected static $settings;
    protected static $logger;
    protected $model;

    public function __construct($settings, $logger = null) {

        IM::init($settings['db'], $logger);

        self::$settings = $settings;
        self::$logger = $logger;

    }

    protected function setModel($model) {
        $this->model = $model;
        return $this;
    }

    protected function get($id) {
        try {
            $data = $this->model->get($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return $data;
    }

    protected function add($data) {

        try {
            $id = $this->model->add($data);
            $ret = $this->model->get($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return $ret ;

    }

    protected function delete($id) {
        try {;
            $this->model->delete($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return true;
    }

    protected function markAsDelete($id) {

        try {;
            $this->model->markAsDeleted($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return true;
    }

    protected function edit($id, $data) {

        try {
            $id = $this->model->edit($id, $data);
            $data = $this->model->get($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return $data;
    }

    protected function listing($params = null) {

        try {

            $rows = $this->model->getMany($params);
            $count = $this->model->getTotalCount($params);

        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return ['data' => $rows, 'count' => $count];

    }

    public function addCategory($data) {

        return $this->setModel(new CM())->add($data);

    }

    public function getCategory($id) {

        return $this->setModel(new CM())->get($id);

    }

    public function deleteCategory($id) {

        return $this->setModel(new CM())->delete($id);

    }

    public function editCategory($id, $data) {

        return $this->setModel(new CM())->edit($id,$data);

    }

    public function getCategories($params = null) {

        return $this->setModel(new CM())->listing($params);

    }

    public function addItem($data) {

        $data['posted_on'] = !empty($data['posted_on']) ? $data['posted_on'] : date('Y-m-d H:i:s');

        return $this->setModel(new IM())->add($data);

    }

    public function getItem($id) {

        return $this->setModel(new IM())->get($id);

    }

    public function deleteItem($id) {

        return $this->setModel(new IM())->markAsDelete($id);

    }

    public function editItem($id, $data) {

        return $this->setModel(new IM())->edit($id,$data);

    }

    public function getItems($params = null) {

        return $this->setModel(new IM())->listing($params);

    }

    public function searchItems($params = null) {
        return $this->setModel(new SM())->listing($params);
    }

}