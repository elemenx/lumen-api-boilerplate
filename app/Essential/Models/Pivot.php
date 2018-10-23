<?php

namespace App\Essential\Models;

use Illuminate\Database\Eloquent\Relations\Pivot as BaseModel;
use ElemenX\ApiPagination\Paginatable;

class Pivot extends BaseModel
{
    use Paginatable;

    protected $guarded = [];
    protected $columns = [];

    public function scopeExclude($query, ...$value)
    {
        return $query->select(array_diff($this->columns, $value));
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }
}
