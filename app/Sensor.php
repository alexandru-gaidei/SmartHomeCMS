<?php

namespace App;

use App\Traits\SensorMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sensor extends Model
{
    use SoftDeletes;
    use SensorMeta;

    public const SRC_TYPE_FETCH = 'FETCH';
    public const SRC_TYPE_PUSH = 'PUSH';

    public const VAL_TYPE_BOOL = 'BOOL';
    public const VAL_TYPE_NUM = 'NUM';

    protected $fillable = ['group_id', 'name', 'source_type', 'source_url_fetch', 'parameter', 'identifier',
        'value_type', 'execute_at_rrule', 'min_value', 'value', 'max_value'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function history()
    {
        return $this->morphMany(History::class, 'historyable');
    }

    public function favorite()
    {
        return $this->morphOne(Favorite::class, 'favoriteable');
    }
}
