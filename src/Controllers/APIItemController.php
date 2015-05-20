<?php

namespace Controllers;

use Models\ItemModel;

use Transformers\ItemTransformer;



class APIItemController extends APIController {

    public function __construct() {
        parent::__construct();
        $this->model = new ItemModel($this->app->appConfig['db']);
        $this->transformer = new ItemTransformer();
    }


}