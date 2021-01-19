<?php

namespace App\Http\Controllers\Api;

use App\Events\FavoriteDataChanged;
use App\History;
use App\Http\Controllers\Controller;
use App\Http\Resources\SensorResource;
use App\Jobs\SensorAction;
use App\Sensor;
use Illuminate\Http\Request;

class IncomingActionController extends Controller
{
    public function process(Request $request, $identifier)
    {
        $sensor = Sensor::whereIdentifier($identifier)->first();
        abort_unless($sensor, 404);

        $value = $request->all();

        if(!empty($sensor->parameter)) {
            $parts = explode('.', $sensor->parameter);
            foreach($parts as $part) {
                $value = $value[trim($part)];
            }
        }

        $sensor->value = $sensor->value_type === Sensor::VAL_TYPE_BOOL ? boolval($value) : $value;
        $sensor->save();

        if(!empty($sensor->favorite)) {
            event(new FavoriteDataChanged([
                'sensor_id' => $sensor->id,
                'value'     => $sensor->value
            ]));
        }

        $sensor->history()->create([
            'status'       => History::OK,
            'data'         => json_encode($request->all()),
            'ocurrence_at' => now()
        ]);

        SensorAction::dispatch($sensor);

        return new SensorResource($sensor);
    }
}
