<?php

namespace Transformers;

use League\Fractal;


class VideoFilesTransformer extends Fractal\TransformerAbstract {

    public function transform($filesArray) {

        return [

            'type' => $filesArray['type'],
            'url' =>  $filesArray['url'],
            'width' => is_null($filesArray['width']) ? null : (int)$filesArray['width'],
            'height' => is_null($filesArray['height']) ? null : (int)$filesArray['height'],
            'size' => (int)$filesArray['size'],
            'bitrate' => (int)$filesArray['bitrate'],
        ];
    }
}