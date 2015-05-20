<?php

namespace Nabu\Controllers;

use \Nabu\Models\ItemModel;

use \Nabu\Transformers\ItemTransformer;



class APIItemController extends APIController {

    public function __construct() {
        parent::__construct();
        $this->model = new ItemModel($this->app->appConfig['db']);
        $this->transformer = new ItemTransformer();
    }


}