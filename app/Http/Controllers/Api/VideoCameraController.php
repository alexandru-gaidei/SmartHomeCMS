<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoCameraRequest;
use App\Http\Resources\VideoCameraRecordResource;
use App\Http\Resources\VideoCameraResource;
use App\VideoCamera;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoCameraController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(VideoCamera::class, 'cam');
    }

    public function index()
    {
        return VideoCameraResource::collection(VideoCamera::latest()->paginate());
    }

    public function store(VideoCameraRequest $request)
    {
        $data = $request->all();
        $data['uuid'] = Str::uuid();
        $cam = VideoCamera::create($data);
        $this->initServer($cam);

        if($cam->store) {
            $cam->startRecording();
        }
        
        return new VideoCameraResource($cam); 
    }

    public function show(VideoCamera $cam)
    {
        return new VideoCameraResource($cam);
    }

    public function update(VideoCameraRequest $request, VideoCamera $cam)
    {
        $cam->update($request->except(['uuid']));
        $cam->refresh();

        $this->initServer($cam);

        $cam->stopRecording();

        if($cam->store) {
            $cam->startRecording();
        }

        return new VideoCameraResource($cam);
    }

    public function destroy(VideoCamera $cam)
    {
        $cam->stopRecording();
        $cam->delete();
        unlink(base_path("_cam_servers/rtsp/server-{$cam->id}.js"));
        return response(null, 204);
    }

    public function startRecording(VideoCamera $cam)
    {
        $cam->startRecording();
        return response(null, 204);
    }

    public function stopRecording(VideoCamera $cam)
    {
        $cam->stopRecording();
        return response(null, 204);
    }

    public function records(Request $request, VideoCamera $cam)
    {
        return VideoCameraRecordResource::collection($cam->records()->sortByDesc('created_at')->paginate(16));
    }

    public function metadata()
    {
        return response([
            'size_heights' => VideoCamera::$size_heights
        ]);
    }
    
    private function initServer(VideoCamera $cam)
    {
        $path = base_path("_cam_servers/rtsp/server-{$cam->id}.js");
        $stub_content = file_get_contents(base_path('_cam_servers/rtsp/' . VideoCamera::CAM_SERVER_RTSP_STUB));
        $stub_content = str_replace('{{url}}', $cam->stream_url, $stub_content);
        $stub_content = str_replace('{{port}}', 9050 + $cam->id, $stub_content);
        exec("echo \"{$stub_content}\" > {$path}");
    }
}
