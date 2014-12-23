<?php

/**
 * 相片信息视图
 * 周东宝2014-12-23
 */

namespace Common\Model;

class PhotoViewModel extends \Think\Model\ViewModel {

    public $viewFields = array(
        'Photo' => array(
            'id',
            'album_id',
            'photo_name',
            'photo_url',
            'exttype',
            'extfield',
            'description',
            'list_order',
            'status',
            'view_times',
            'allow_comment',
            'create_time',
            'update_time',
            '_type' => 'LEFT',
        ),
        'PhotoComment' => array(
            'id' => 'photoComment_id',
            'content',
            'status' => 'photoComment_status',
            'user_id',
            'user_name',
            'ip_address',
            'parent_id',
            'create_time' => 'photoComment_createTime',
            'update_time' => 'photoComment_updateTime',
            '_type' => 'LEFT',
            '_on' => 'Photo.id = PhotoComment.photo_id'
        )
    );

}

?>