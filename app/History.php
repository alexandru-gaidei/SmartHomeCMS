<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    public const OK   = 'OK';
    public const FAIL = 'FAIL';
    public const CHART_DAYS_AGO = 180;

    protected $fillable = ['historyable_id', 'historyable_type', 'status', 'data', 'value', 'ocurrence_at'];

    public function historyable()
    {
        return $this->morphTo();
    }
}
