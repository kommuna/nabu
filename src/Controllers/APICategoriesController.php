<?php

namespace Controllers;

use Models\CategoryModel;

use Transformers\CartegoryTransformer;



class APICategoriesController extends APIController {

    public function __construct() {
        parent::__construct();
        $this->model = new CategoryModel();
        $this->transformer = new CartegoryTransformer();
    }


}