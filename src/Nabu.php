<?php

namespace Nabu;

$loader = require './../vendor/autoload.php';

use Nabu\Models\CategoryModel as CM;
use Nabu\Models\ItemModel as IM;
use Nabu\Exceptions\NabuException as E;


class Nabu {

    protected static $settings;
    protected static $logger;
    protected $model;

    public function __construct($settings, $logger = null) {

        IM::init($settings, $logger);

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
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $data;
    }

    protected function add($data) {

        try {
            $id = $this->model->add($data);
            $ret = $this->model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $ret ;

    }

    protected function delete($id) {
        try {;
            $this->model->delete($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return true;
    }

    protected function markAsDelete($id) {

        try {;
            $this->model->markAsDeleted($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return true;
    }

    protected function edit($id, $data) {

        try {
            $id = $this->model->edit($id, $data);
            $data = $this->model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $data;
    }

    protected function listing($params = null) {

        try {
            $count = $this->model->getTotalCount($params);
            $rows = $this->model->getMany($params);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
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

}