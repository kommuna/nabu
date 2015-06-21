<?php

namespace Nabu;

$loader = require './../vendor/autoload.php';

use Nabu\Exceptions\ModelException;
use Nabu\Models\CategoryModel as CM;
use Nabu\Models\CounterQueues;
use Nabu\Models\ItemModel as IM;
use Nabu\Models\SolrModel as SM;
use Nabu\Exceptions\NabuException as E;


class Nabu {

    protected static $settings;
    protected static $logger;
    protected $model;

    public function __construct($settings, $logger = null) {

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

    protected function increaseViewsCounter($code) {

        try {

            $model = $this->model->getByCode($code);

            if($model) {
                $this->model->edit($model[0]['id'], ["views_counter" => (int)$model[0]["views_counter"] + 1]);
            } else {
                ModelException::throwException("Item with 'code' = $code doesn't exists");
            }


        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return true;

    }

    public function addCategory($data) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->add($data);

    }

    public function getCategory($id) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->get($id);

    }

    public function deleteCategory($id) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->delete($id);

    }

    public function editCategory($id, $data) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->edit($id,$data);

    }

    public function getCategories($params = null) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->listing($params);

    }

    public function addItem($data) {

        $data['posted_on'] = !empty($data['posted_on']) ? $data['posted_on'] : date('c');
        IM::setForbiddenTerms(self::$settings['forbiddenTerms']);

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->add($data);

    }

    public function getItem($id) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->get($id);

    }

    public function deleteItem($id) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->markAsDelete($id);

    }

    public function editItem($id, $data) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->edit($id,$data);

    }

    public function getItems($params = null) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->listing($params);

    }

    public function searchItems($params = null) {

        return $this->setModel(new SM(self::$settings['solr'], self::$logger))->listing($params);
    }

    public function increaseViewsCounterByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseViewsCounter($code);

    }

    public function increaseVotesPositiveByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseVotesPositive($code);

    }

    public function increaseVotesNegativeByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseVotesNegative($code);

    }

    public function increaseFavoritesCounterByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseFavoritesCounter($code);

    }

}