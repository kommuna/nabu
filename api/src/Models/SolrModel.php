<?php

namespace Nabu\Models;

use \Nabu\Exceptions\ModelException;
use \SolrClient;
use \SolrQuery;

class SolrModel {

    protected $validators = [];
    protected $client;
    protected $logger;
    protected $totalResultSetCount = 0;

    public function __construct($config, $logger) {

        $this->logger = $logger;

        $this->setFieldsValidators((new ItemValidators())->get());
        $this->client = new SolrClient($config);


    }

    protected function setFieldsValidators($validators) {
        $this->validators = $validators;
    }

    public function getFieldsValidators() {
        return $this->validators;
    }

    public function getMany($params = null) {

        $query = new SolrQuery();

        $query->addField('id')
            ->addField('code')
            ->addField('category_id')
            ->addField('description')
            ->addField('activated_on')
            ->addField('is_param_1')
            ->addField('promo_title')
            ->addField('promo_url');

        if($params && $params->getOffset()) {
            $query->setStart($params->getOffset());
        }

        if($params && $params->getLimit()) {
            $query->setRows($params->getLimit());
        }

        if($params && $params->getQuery()) {
            $query->setQuery($params->getQuery());
        }




        $this->applyFilter($query, $params);
        $this->applyOrder($query, $params);


        $response = $this->client->query($query);



        return $response;


    }


    public function getTotalCount($params = null) {
        return $this->totalResultSetCount;
    }


    protected function applyFilter(\SolrQuery $solrQuery, $params = null)
    {

        if (is_null($params)) {
            return $solrQuery;
        }

        $filter = $params->getFilter();
        $fields = $this->getFieldsValidators();

        foreach (array_keys($fields) as $field) {

            if (!isset($filter[$field])) {
                continue;
            } else {
                $fieldParams = $filter[$field];
            }

            if (is_array($fieldParams)) {


                $from = $to = false;

                if (isset($fieldParams['from']) && is_scalar($fieldParams['from'])) {

                    // Date fields should end by '_on' (posted_on)
                    if (substr($field, -3) == '_on') {

                        $time = strtotime($fieldParams['from']);

                        $from = $time !== false ? date("c", $time) : false;
                    } else {
                        $from = $fieldParams['from'];
                    }
                }


                if (isset($fieldParams['to']) && is_scalar($fieldParams['to'])) {

                    if (substr($field, -3) == '_on') {
                        $time = strtotime($fieldParams['to']);
                        $to = $time !== false ? date("c", $time) : false;
                    } else {
                        $to = $fieldParams['to'];
                    }
                }

                if ($from !== false && $to !== false) {

                    $solrQuery->addFilterQuery("$field:[$from TO $to]");

                } elseif ($from !== false) {

                    $solrQuery->addFilterQuery("$field:[$from TO *]");

                } elseif ($to !== false) {

                    $solrQuery->addFilterQuery("$field:[* TO $to]");

                } else {
                    $solrQuery->addFilterQuery("$field:(" . implode(' OR ', $fieldParams) . ")");

                }


            } else {

                // Logical fields should start by 'is_' (is_logo_on)
                if (substr($field, 0, 3) == 'is_') {

                    $fieldParams = $fieldParams ? 'true' : 'false';

                    // Date fields should end by '_on' (posted_on)
                } elseif (substr($field, -3) == '_on') {

                    $time = strtotime($fieldParams);
                    $fieldParams = $time !== false ? date("c", $time) : false;

                }

                $solrQuery->addFilterQuery("$field:$fieldParams");


            }
        }

        return $solrQuery;
    }

    protected function applyOrder(\SolrQuery $solrQuery, $params = null) {

        if(is_null($params)) {
            return $solrQuery;
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
                $solrQuery->addSortField($orderField, \SolrQuery::ORDER_ASC);
            }

            if(strtolower($order[$orderField]) == 'desc') {
                $solrQuery->addSortField($orderField, \SolrQuery::ORDER_DESC);
            }
        }

        return $solrQuery;
    }


}