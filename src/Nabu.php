<?php

$loader = require './../vendor/autoload.php';

use Models\CategoryModel as CM;
use Exceptions\NabuException as E;

class Nabu {

    public function __construct($settings) {

    }

    public function addCategory($data) {

        try {
            $model = new CM();
            $id = $model->add($data);
            $ret = $model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $ret ;

    }

    public function getCategory($id) {

        try {
            $model = new CM();
            $data = $model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $data;

    }

    public function deleteCategory($id) {

        try {
            $model = new CM();
            $model->delete($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return true;

    }

    public function editCategory($id, $data) {

        try {
            $model = new CM();
            $id = $model->edit($id, $data);
            $data = $model->get($id);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return $data;

    }

    public function getCategories(\Core\apiParams $params = null) {


        try {
            $model = new CM();
            $count = $model->getTotalCount($params);
            $rows = $model->getMany($params);
        } catch(\Exception $e) {
            E::throwException($e->getMessage());
        }

        return ['rows' => $rows, 'count' => $count];

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