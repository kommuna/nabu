<?php

namespace Nabu;

$loader = require './../vendor/autoload.php';

use Nabu\Models\CategoryModel as CM;
use Nabu\Exceptions\NabuException as E;


class Nabu {

    private $settings;

    public function __construct($settings) {

        $this->settings = $settings;

    }

    public function addCategory($data) {

        try {
            $model = new CM($this->settings);
            $id = $model->add($data);
            $ret = $model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $ret ;

    }

    public function getCategory($id) {

        try {
            $model = new CM($this->settings);
            $data = $model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $data;

    }

    public function deleteCategory($id) {

        try {
            $model = new CM($this->settings);
            $model->delete($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return true;

    }

    public function editCategory($id, $data) {

        try {
            $model = new CM($this->settings);
            $id = $model->edit($id, $data);
            $data = $model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $data;

    }

    public function getCategories($params = null) {


        try {
            $model = new CM($this->settings);
            $count = $model->getTotalCount($params);
            $rows = $model->getMany($params);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return ['data' => $rows, 'count' => $count];

    }

    public function addItem() {

    }

    public function getItem() {

    }

    public function deleteItem() {

    }

    public function editItem() {

    }

    public function getItems() {

    }

}