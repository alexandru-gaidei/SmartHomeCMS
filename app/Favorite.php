<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    const TYPE_SENSOR = 'SENSOR';
    const TYPE_ACTION = 'ACTION';

    protected $fillable = ['favoriteable_id', 'favoriteable_type', 'order', 'name'];

    public function favoriteable()
    {
        return $this->morphTo();
    }
}
