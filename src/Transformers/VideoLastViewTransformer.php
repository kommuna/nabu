<?php

namespace Transformers;

use League\Fractal;

class VideoLastViewTransformer extends Fractal\TransformerAbstract {

    static public function getDateTime($dt) {
        return !is_null($dt) ? date('c',strtotime($dt)) : null;
    }

    public function transform($arr) {

        return [

            'id' => (int)$arr['id'],
            'viewed_on' => self::getDateTime($arr['viewed_on']),
            'view_count' => (int)$arr['view_count'],
        ];
    }
}