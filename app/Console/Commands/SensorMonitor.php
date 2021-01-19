<?php

namespace App\Console\Commands;

use App\Events\FavoriteDataChanged;
use App\Events\HistoryFailOcured;
use App\History;
use App\Jobs\SensorAction;
use App\Sensor;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Recurr\Transformer\ArrayTransformerConfig;

class SensorMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:monitor';

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
        $sensors = Sensor::with('actions')->where('source_type', Sensor::SRC_TYPE_FETCH)->get();
        $sensors->each(function($sensor)
        {
            $last_trigger = $sensor->history()->where('status', History::OK)->latest()->first();
            $start = $last_trigger ? $last_trigger->ocurrence_at : $sensor->created_at;

            $rule = Rule::createFromString($sensor->execute_at_rrule, $start, null, config('app.timezone'));
            
            $transformerConfig = new ArrayTransformerConfig();
            $transformerConfig->enableLastDayOfMonthFix();
            $transformer = new ArrayTransformer();
            $transformer->setConfig($transformerConfig);

            $constraint = new BeforeConstraint(now());
            $recurrences = $transformer->transform($rule, $constraint);

            $trigger_at = $last_trigger ? $recurrences->last() : $recurrences->first();
            $trigger_at = Carbon::parse($trigger_at->getStart());

            $need_to_fetch = !$last_trigger || $last_trigger->ocurrence_at < $trigger_at;

            if(!$need_to_fetch) {
                return;
            }

            $client = new Client();

            try {
                $response = $client->get($sensor->source_url_fetch);
                $data = $response->getBody()->getContents();
                $value = json_decode($data, true);

                if(!empty($sensor->parameter)) {
                    $parts = explode('.', $sensor->parameter);
                    foreach($parts as $part) {
                        $value = $value[trim($part)];
                    }
                }

                $sensor->value = $sensor->value_type == Sensor::VAL_TYPE_BOOL ? boolval($value) : $value;
                $sensor->save();
                $status = History::OK;

                if(!empty($sensor->favorite)) {
                    event(new FavoriteDataChanged([
                        'sensor_id' => $sensor->id,
                        'value'     => $sensor->value
                    ]));
                }

                $history_value = $sensor->value_type == Sensor::VAL_TYPE_NUM ? $value : null;
            }
            catch(\Exception $err) {
                $data = $err->getMessage();
                $status = History::FAIL;
                $history_value = null;
                event(new HistoryFailOcured());
            }

            $sensor->history()->create([
                'status' => $status,
                'data' => $data,
                'value' => $history_value,
                'ocurrence_at' => $trigger_at
            ]);

            if($status == History::FAIL) {
                return;
            }

            SensorAction::dispatch($sensor);
        });
    }
}
