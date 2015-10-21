<?php

namespace Nabu\Controllers;

use Nabu\Models\CategoryModel;
use Nabu\Transformers\CategoryTransformer;
use RestModel\Controllers\APIController;



class APICategoriesController extends APIController {

    public function __construct() {
        parent::__construct();
        $this->model = new CategoryModel($this->app->appConfig['db'], $this->app->log);
        $this->transformer = new CategoryTransformer();
    }


}