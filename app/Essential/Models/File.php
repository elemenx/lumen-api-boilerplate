<?php

namespace App\Essential\Models;

class File extends Model
{
    protected $columns = [
        'id',
        'object_type',
        'object_id',
        'uploader_type',
        'uploader_id',
        'mime',
        'path',
        'width',
        'height',
        'sequence',
        'created_at',
        'updated_at',
    ];

    public $cacheOptions = [
        'order' => 'sequence'
    ];

    public function object()
    {
        return $this->morphTo();
    }
}
