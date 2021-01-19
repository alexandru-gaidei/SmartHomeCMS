<?php

namespace App;

use App\Traits\Action as ActionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use SoftDeletes;
    use ActionTrait;

    const VAL_TYPE_MIN = 'MIN';
    const VAL_TYPE_MAX = 'MAX';

    const TYPE_UI       = 'UI';
    const TYPE_HTTP_GET = 'HTTP_GET';
    const TYPE_MAIL     = 'MAIL';

    public static $VAL_TYPES = [
        self::VAL_TYPE_MIN => 'Min (switch - off)',
        self::VAL_TYPE_MAX => 'Max (switch - on)',
    ];

    public static $TYPES = [
        self::TYPE_UI       => 'UI Notifications',
        self::TYPE_HTTP_GET => 'HTTP GET',
        self::TYPE_MAIL     => 'Email Notifications',
    ];

    protected $fillable = ['sensor_id', 'name', 'value_type', 'type', 'subject'];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
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
