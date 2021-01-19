<?php

namespace App\Console\Commands;

use App\VideoCamera;
use Illuminate\Console\Command;

class RemoveRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'records:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        VideoCamera::where('keep_days', '>', 0)->get()->each(function($cam) {
            $cam->records()->each(function($rec) use ($cam) {
                if($rec['created_at_raw'] < now()->subDays($cam->keep_days)) {
                    unlink($rec['path']);
                    $this->line("Removed {$rec['path']}");
                }
            });
        });
    }
}
