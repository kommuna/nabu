<?php

namespace Transformers;

use League\Fractal;
use Models\FilesModel;
use Models\ThumbFilesModel;

class VideoTransformer extends Fractal\TransformerAbstract {

    static public function getDateTime($dt) {
        return !is_null($dt) ? date('c',strtotime($dt)) : null;
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'files'
    ];


    public function transform($videoArray) {

        $thumbId = is_null($videoArray['thumbnail_id']) ? 12 : $videoArray['thumbnail_id'];

        return [

            'id' => (int)$videoArray['id'],
            'type' => $videoArray['type'],
            'description' => $videoArray['description'],
            'duration' => (int)$videoArray['duration'],
            'thumbnail_id' => $thumbId == -1 ? 'custom' : (int)$thumbId,
            'thumbnails' => /*null,*/ (new ThumbFilesModel())->getThumbs($videoArray['id'], $thumbId),
            'view_count' => (int)$videoArray['view_count'],
            'click_url' => $videoArray['click_url'],
            'status' => (int)$videoArray['status'],
            'posted_on' => self::getDateTime($videoArray['posted_on']),
            'uploaded_on' => self::getDateTime($videoArray['uploaded_on']),
            'encoded_on' => self::getDateTime($videoArray['encoded_on']),
            'failed_on' => self::getDateTime($videoArray['failed_on']),
            'deleted_on' => self::getDateTime($videoArray['deleted_on']),
            'viewed_on' => self::getDateTime($videoArray['viewed_on']),
            'fail_reason' => $videoArray['fail_reason'],
            'updated_on' => self::getDateTime($videoArray['updated_on']),
        ];
    }

    public function includeFiles($videoArray) {
        $id = (int)$videoArray['id'];

        $files = $videoArray['status'] == 'R' ? (new FilesModel())->getVideoFiles($id) : [];

        return $this->collection($files, new VideoFilesTransformer());
    }


}