<?php

namespace Controllers;

use Models\CategoryModel;

use Transformers\CategoryTransformer;



class APICategoriesController extends APIController {

    public function __construct() {
        parent::__construct();
        $this->model = new CategoryModel($this->app->appConfig['db']);
        $this->transformer = new CategoryTransformer();
    }


}