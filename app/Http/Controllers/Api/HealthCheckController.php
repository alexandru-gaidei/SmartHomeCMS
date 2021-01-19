<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class HealthCheckController extends Controller
{
    public function index()
    {
        try { $redis      = $this->getRedisStatus(); }           catch(\Exception $err) { logger()->error('HealthCheck:redis:' . $err->getMessage()); $redis = false; }
        try { $echo       = $this->getWebsocketServerStatus(); } catch(\Exception $err) { logger()->error('HealthCheck:echo:' . $err->getMessage()); $echo = false; }
        try { $queue      = $this->getQueueStatus(); }           catch(\Exception $err) { logger()->error('HealthCheck:queue:' . $err->getMessage()); $queue = false; }
        try { $ffmpeg     = $this->getFFMPEGStatus(); }          catch(\Exception $err) { logger()->error('HealthCheck:ffmpeg:' . $err->getMessage()); $ffmpeg = false; }
        try { $cam_serv   = $this->getCamServStatus(); }         catch(\Exception $err) { logger()->error('HealthCheck:cam-servers:' . $err->getMessage()); $cam_serv = false; }
        try { $writable   = $this->getWritableStatus(); }         catch(\Exception $err) { logger()->error('HealthCheck:writable:' . $err->getMessage()); $writable = false; }

        $all = $redis && $echo && $queue && $ffmpeg && $cam_serv && $writable;

        return response()->json([
            'main' => [
                'status' => $all ? 'OK' : 'FAIL',
                'info' => [
                    'OK'   => 'All good',
                    'FAIL' => 'Please check each service status',
                ]
            ],
            'items' => [
                'queue' => [
                    'status' => $queue ? 'OK' : 'FAIL',
                    'info' => [
                        'OK'   => 'Queue is running',
                        'FAIL' => 'Queue is stopped, please run supervisor or command `php /path/to/project/webroot/artisan queue:work --timeout=120`',
                    ]
                ],
                'redis-server' => [
                    'status' => $redis ? 'OK' : 'FAIL',
                    'info' => [
                        'OK'   => 'Redis is running',
                        'FAIL' => 'Redis is stopped, please run supervisor or command `redis-server`',
                    ]
                ],
                'websocket' => [
                    'status' => $echo ? 'OK' : 'FAIL',
                    'info' => [
                        'OK'   => 'Websocket is running',
                        'FAIL' => 'Websocket is stopped, please run supervisor or command `./node_modules/laravel-echo-server/bin/server.js start --force`',
                    ]
                ],
                'ffmpeg' => [
                    'status' => $ffmpeg ? 'OK' : 'FAIL',
                    'info' => [
                        'OK'   => 'FFMPEG tool is installed and works properly',
                        'FAIL' => 'FFMPEG tool is not installed',
                    ]
                ],
                'cam-servers' => [
                    'status' => $cam_serv ? 'OK' : 'FAIL',
                    'info' => [
                        'OK'   => '(Some) cam servers is running, if video not streaming please restart servers with command `./cam-servers.sh restart`',
                        'FAIL' => 'Cam servers not running, please start with command `./cam-servers.sh start`',
                    ]
                ],
                'writable' => [
                    'status' => $writable ? 'OK' : 'FAIL',
                    'info' => [
                        'OK'   => 'Paths are writable',
                        'FAIL' => 'Some paths are not writable, please check permissions',
                    ]
                ],
            ]
        ]);
    }

    private function getQueueStatus()
    {
        exec('ps aux | grep queue:work', $out);
        foreach($out as $line) {
            if(strpos($line, 'php artisan queue:work') !== false) {
                return true;
            }
        }

        return false;
    }

    private function getWebsocketServerStatus()
    {
        $scheme = 'http://';
        $host = '127.0.0.1';
        $port = 6001;
        $client = new Client(['base_uri' => "$scheme$host:$port"]);
        $response = $client->get('/socket.io/', ['query' => 'transport=polling']);
        $out = $response->getBody()->getContents();

        return $response->getStatusCode() === 200 && strpos($out, 'websocket') !== false;
    }

    private function getRedisStatus()
    {
        $redis = Redis::connection();
        $pingResponse = $redis->ping();

        if ($pingResponse) {
            return true;
        }
        
        logger()->error('HealthCheck:redis:' . $pingResponse);
        return false;
    }

    private function getFFMPEGStatus()
    {
        exec(env('FFMPEG_PATH_BIN') . ' -version', $out);
        foreach($out as $line) {
            if(strpos($line, 'libx264') !== false) {
                return true;
            }
        }

        return false;
    }

    private function getCamServStatus()
    {
        exec('ps aux | grep pm2', $out);
        foreach($out as $line) {
            if(strpos($line, 'PM2') !== false) {
                return true;
            }
        }

        return false;
    }

    private function getWritableStatus()
    {
        $paths = [
            base_path('bootstrap/cache'),
            base_path('storage'),
        ];

        foreach($paths as $path) {
            if(!is_writable($path)) {
                return false;
            }
        }

        try {
            logger()->info('Health Check - ping');
        }
        catch(\Exception $err) {
            return false;
        }

        return true;
    }
}
