<?php

namespace App\Essential\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use ElemenX\ApiPagination\Paginatable;

class Model extends BaseModel
{
    use Paginatable;

    protected $guarded = [];
    protected $columns = [];

    public function scopeExclude($query, ...$value)
    {
        return $query->select(array_diff($this->columns, $value));
    }

    public function getDescriptionForEvent(string $event)
    {
        return '该对象被' . trans('event_types.' . $event);
    }
}
