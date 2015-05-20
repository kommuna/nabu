<?php

namespace Nabu;

$loader = require './../vendor/autoload.php';

use Nabu\Models\CategoryModel as CM;
use Nabu\Models\ItemModel as IM;
use Nabu\Exceptions\NabuException as E;


class Nabu {

    protected $settings;
    protected $model;

    public function __construct($settings) {

        $this->settings = $settings;

    }

    protected function setModel($model) {
        $this->model = $model;
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

        $this->setModel(new CM($this->settings));
        return $this->add($data);

    }

    public function getCategory($id) {

        $this->setModel(new CM($this->settings));
        return $this->get($id);

    }

    public function deleteCategory($id) {

        $this->setModel(new CM($this->settings));
        return $this->delete($id);

    }

    public function editCategory($id, $data) {

        $this->setModel(new CM($this->settings));
        return $this->edit($id,$data);

    }

    public function getCategories($params = null) {

        $this->setModel(new CM($this->settings));
        return $this->listing($params);

    }

    public function addItem($data) {

        $this->setModel(new IM($this->settings));
        return $this->add($data);

    }

    public function getItem($id) {

        $this->setModel(new IM($this->settings));
        return $this->get($id);

    }

    public function deleteItem($id) {

        $this->setModel(new IM($this->settings));
        return $this->delete($id);

    }

    public function editItem($id, $data) {

        $this->setModel(new IM($this->settings));
        return $this->edit($id,$data);

    }

    public function getItems($params = null) {

        $this->setModel(new IM($this->settings));
        return $this->listing($params);

    }

}