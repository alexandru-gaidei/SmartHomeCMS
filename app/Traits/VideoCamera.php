<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait VideoCamera
{
    public static $size_heights = [
        240 => '240p',
        480 => '480p',
        600 => '600p',
        720 => '720p',
        960 => '960p',
        1080 => '1080p',
    ];

    public function startRecording()
    {
        $this->stopRecording();

        if (! $this->store || empty($this->stream_url) || empty($this->length) || empty($this->size_height)) {
            return;
        }

        $path = public_path($this->dirPath());
        if (! is_dir($path)) {
            mkdir($path);
        }

        $command = sprintf(
            '%s -re -i %s -flags +global_header -f segment -strftime 1 -segment_time %s -segment_format_options \
            movflags=+faststart -reset_timestamps 1 \
            -c:v libx264 -preset veryfast -filter:v scale="trunc(oh*a/2)*2:%s" -crf 26 -c:a aac \
            -threads 2 -r 10 %s/%%Y-%%m-%%d_%%H-%%M-%%S__%s.mp4 > /dev/null 2>&1 & echo $!',

            config('services.ffmpeg.bin_path'),
            $this->stream_url,
            $this->length,
            $this->size_height,
            $path,
            sprintf('%s-%s', Str::slug($this->name), Str::uuid())
        );

        try {
            exec($command, $output);
            $pid = $output[0];
            $this->pid = $pid;
            $this->save();
        } catch (Exception $err) {
            logger()->error($command);
            logger()->error($err->getMessage());
            throw $err;
        }

        usleep(500000); // 0.5s

        $pids = $this->processes();
        if (! in_array($pid, $pids)) {
            throw new Exception('Cannot play, please verify availability of the stream.', 520);
        }
    }

    public function stopRecording()
    {
        if ($this->pid) {
            exec("kill {$this->pid}");
        }
    }

    public function records()
    {
        try {
            $path = public_path($this->dirPath());
            $url = url($this->dirPath());
            $files = File::files($path);
        } catch (Exception $err) {
            return collect([]);
        }

        return collect($files)->map(function ($file) use ($url) {
            $datetime = Carbon::parse($file->getCTime())->setTimezone(config('app.timezone'));
            return [
                'id' => uniqid(),
                'filename' => $file->getFilename(),
                'url' => $url.'/'.$file->getFilename(),
                'path' => $file->getPathname(),
                'created_at_raw' => $datetime,
                'created_at' => $datetime->format('d/m/Y H:i:s'),
            ];
        });
    }

    public static function processes()
    {
        exec('pgrep ffmpeg', $output);
        return $output;
    }

    private function dirPath()
    {
        return "_cam/{$this->id}-{$this->uuid}";
    }
}
