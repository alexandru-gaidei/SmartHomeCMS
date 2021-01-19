<?php

namespace App\Jobs;

use App\Action;
use App\Sensor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SensorAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sensor;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sensor $sensor)
    {
        $this->sensor = $sensor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sensor->actions->each(function ($action) {
            if ($this->sensor->value <= $this->sensor->min_value && $action->value_type === Action::VAL_TYPE_MIN) {
                $action->do();
            } elseif ($this->sensor->value >= $this->sensor->max_value && $action->value_type === Action::VAL_TYPE_MAX) {
                $action->do();
            }
        });
    }
}
