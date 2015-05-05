<?php

namespace Transformers;

use League\Fractal;

class VideoStatsTransformer extends Fractal\TransformerAbstract {

    public function transform($arr) {

        return [
            'date' => date('c',strtotime($arr['date'])),
            'view_count' => (int)$arr['view_count'],
        ];
    }
}