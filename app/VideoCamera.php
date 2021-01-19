<?php

namespace App;

use App\Traits\VideoCamera as VideoCameraTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCamera extends Model
{
    use SoftDeletes, VideoCameraTrait;

    public const CAM_SERVER_RTSP_STUB = 'server.js.stub';

    protected $fillable = ['uuid', 'group_id', 'name', 'store', 'length', 'size_height', 'stream_url', 'pid', 'keep_days'];
}
