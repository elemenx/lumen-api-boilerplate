<?php

namespace App\Models;

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
}
