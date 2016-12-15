<?php

/**
 * NABU
 *
 * Main module
 *
 */

namespace Nabu;

// PSR-4 Autoload
require './../vendor/autoload.php';

use RestModel\Exceptions\ModelException;
use RestModel\Models\SolrModel as SM;
use Nabu\Models\CategoryModel as CM;
use Nabu\Models\SiteModel as SIM;
use Nabu\Models\CounterQueues;
use Nabu\Models\ItemModel as IM;
use Nabu\Models\ItemValidators as IV;
use Nabu\Exceptions\NabuException as E;


class Nabu {

    protected static $settings;
    protected static $logger;
    protected $model;

    /**
     * Constructor
     *
     * @param $settings - NABU's setting
     * @param null $logger - PSR-3 logger
     */
    public function __construct($settings, $logger = null) {

        self::$settings = $settings;
        self::$logger = $logger;

        // Get forrbidden terms from config file
        IM::setForbiddenTerms(self::$settings['forbiddenTerms']);

    }

    /**
     * Model setter method
     *
     * @param $model – model setter
     * @return $this – current object
     */
    protected function setModel($model) {
        $this->model = $model;
        return $this;
    }

    /**
     * Wrapper method to get item data by ID
     *
     * @param $id – item ID
     * @return mixed – item data
     * @throws static – method can stop by NabuException
     */
    protected function get($id) {
        try {
            $data = $this->model->get($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return $data;
    }

    /**
     * Wrapper method to add item data to Nabu
     *
     * @param $data – array of item fields and values
     * @return mixed – array of added item fileds and values
     * @throws static – method can stop by NabuException
     */
    protected function add($data) {

        try {
            $id = $this->model->add($data);
            $ret = $this->model->get($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

        return $ret;

    }

    /**
     * Wrapper method to delete item by ID from NABU
     *
     * @param $id – item ID
     * @throws static – method can stop by NabuException
     */
    protected function delete($id) {

        try {
            $this->model->delete($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }

    }

    /**
     * Wrapper method to mark as deleted item by ID from NABU
     *
     * @param $id – item ID
     * @throws static – method can stop by NabuException
     */
    protected function markAsDelete($id) {

        try {
            $this->model->markAsDeleted($id);
        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }
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

    /**
     * Wrapper method to get array (list) of items with total count
     *
     * @param $params – params to affect list of arrays [order, filter, offset, limit]
     * @throws static – method can stop by NabuException
     * @return array - array with two items:
     *
     *     data – array of items
     *     count – total count of item (without limit parameter)
     */
    protected function listing($params = null, $fields = []) {

        try {

            if(method_exists($this->model, 'addFields') && $fields) {
                $this->model->addFields($fields);
            }
            $rows = $this->model->getMany($params);
            $count = $this->model->getTotalCount($params);

        } catch(ModelException $e) {
            E::throwException($e->getErrors());
        }
        return ['data' => $rows, 'count' => $count];

    }

    /**
     * Increase view counter by item code
     *
     * @param $code – item code
     * @throws static – method can stop by NabuException
     */
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

    }

    /**
     * Create category
     *
     *
     * @param $data – category data
     * @return mixed – create category data or NabuException with error description
     */
    public function addCategory($data) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->add($data);

    }

    /**
     * Get category data by ID
     *
     * @param $id –category id
     * @return array – category data or NabuException with error description
     */
    public function getCategory($id) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->get($id);

    }

    /**
     * Delete category date by ID
     *
     * @param $id - category id
     * @throws static – method can stop by NabuException
     */
    public function deleteCategory($id) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->delete($id);

    }

    /**
     * Edit category
     *
     * @param $id – category id
     * @param $data array – field that should be editted with values
     * @return array – editted category
     * @throws static – method can stop by NabuException
     *
     */
    public function editCategory($id, $data) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->edit($id,$data);

    }

    /**
     * Get category list
     *
     * @param $params – params to affect list of arrays [order, filter, offset, limit]
     * @return array – categories list
     * @throws static – method can stop by NabuException
     *
     */
    public function getCategories($params = null) {

        return $this->setModel(new CM(self::$settings['db'], self::$logger))->listing($params);

    }


    /**
     * Create item
     *
     *
     * @param $data – item data
     * @return mixed – create item data or NabuException with error description
     */
    public function addItem($data) {

        $data['posted_on'] = !empty($data['posted_on']) ? $data['posted_on'] : date('c');
        return $this->setModel(new IM(self::$settings['db'], self::$logger))->add($data);

    }

    /**
     * Get item data by item ID
     *
     * @param $id – item ID
     * @return array – item data or NabuException with error description
     */
    public function getItem($id) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->get($id);

    }

    /**
     * Delete (mark as deleted) item by ID
     *
     * @param $id – item ID
     * @throws static – method can stop by NabuException
     */
    public function deleteItem($id) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->markAsDelete($id);

    }

    /**
     * Edit item by item ID
     *
     * @param $id – item ID
     * @param $data array – field that should be editted with values
     * @return array – editted item
     * @throws static – method can stop by NabuException
     */
    public function editItem($id, $data) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->edit($id,$data);

    }

    /**
     * Get items list
     *
     * @param $params – params to affect list of arrays [order, filter, offset, limit]
     * @return array – items list
     * @throws static – method can stop by NabuException
     *
     */
    public function getItems($params = null) {

        return $this->setModel(new IM(self::$settings['db'], self::$logger))->listing($params);

    }


    /**
     * Search items (Solr model)
     *
     * @param $params – params to affect list of arrays [order, filter, offset, limit] to affect list of arrays [order, filter, offset, limit]
     * @return array – items list
     */
    public function searchItems($params = null) {

        if($params) {
            $query = $params->getQuery();
            if($query) {
                (new CounterQueues(self::$settings['rabbitMQ']))->addSearchedText($query);    
            }
        }
        return $this->setModel((new SM(self::$settings['solr'], self::$logger))->setFieldsValidators((new IV())->get()))
            ->listing($params,['id', 'code', 'category_id', 'name', 'description', 'activated_on', 'is_param_1',
                'views_counter', 'votes_positive', 'votes_negative', 'favorites_counter', 'promo_title', 'promo_url',
                'site', 'tags']);
    }

    /**
     * Increase item's view counter by item code
     *
     * @param $code – item code
     * @throws static – method can stop by NabuException
     */
    public function increaseViewsCounterByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseViewsCounter($code);

    }

    /**
     * Increase item's positive votes counter by item code
     *
     * @param $code – item code
     * @throws static – method can stop by NabuException
     */
    public function increaseVotesPositiveByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseVotesPositive($code);

    }

    /**
     * Increase item's negative votes counter by item code
     *
     * @param $code – item code
     * @throws static – method can stop by NabuException
     */
    public function increaseVotesNegativeByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseVotesNegative($code);

    }

    /**
     * Increase item's favorites votes counter by item code
     *
     * @param $code – item code
     * @throws static – method can stop by NabuException
     */
    public function increaseFavoritesCounterByCode($code) {

        (new CounterQueues(self::$settings['rabbitMQ']))->increaseFavoritesCounter($code);

    }

    /**
     * Create site
     *
     *
     * @param $data – site data
     * @return mixed – create category data or NabuException with error description
     */
    public function addSite($data) {

        return $this->setModel(new SIM(self::$settings['db'], self::$logger))->add($data);

    }

    /**
     * Get site data by ID
     *
     * @param $id –site id
     * @return array – site data or NabuException with error description
     */
    public function getSite($id) {

        return $this->setModel(new SIM(self::$settings['db'], self::$logger))->get($id);

    }

    /**
     * Delete site date by ID
     *
     * @param $id - category id
     * @throws static – method can stop by NabuException
     */
    public function deleteSite($id) {

        return $this->setModel(new SIM(self::$settings['db'], self::$logger))->delete($id);

    }

    /**
     * Edit site
     *
     * @param $id – site id
     * @param $data array – field that should be editted with values
     * @return array – editted site
     * @throws static – method can stop by NabuException
     *
     */
    public function editSite($id, $data) {

        return $this->setModel(new SIM(self::$settings['db'], self::$logger))->edit($id,$data);

    }

    /**
     * Get sites list
     *
     * @param $params – params to affect list of arrays [order, filter, offset, limit]
     * @return array – sites list
     * @throws static – method can stop by NabuException
     *
     */
    public function getSites($params = null) {

        return $this->setModel(new SIM(self::$settings['db'], self::$logger))->listing($params);

    }

    /**
     * Get items list for tags/actresses rematching
     * @param int $limit - count of movies for rematch
     * @return generator
     */
    public function getMoviesListForRematchAsGenerator($limit = 100)
    {
        foreach($this->setModel(new IM(self::$settings['db'], self::$logger))->getMoviesListForRematchAsGenerator($limit) as $row) {
            yield $row;
        }

    }

}