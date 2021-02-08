<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SensorRequest;
use App\Http\Resources\SensorResource;
use App\Http\Resources\SensorShortResource;
use Illuminate\Http\Request;
use App\Sensor;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sensor::class, 'sensor');
    }

    public function index(Request $request)
    {
        $sensors = Sensor::with(['actions'])->whereIn('group_id', $request->user()->groups->pluck('id')->toArray())->latest();
        return SensorShortResource::collection($request->has('page') ? $sensors->paginate() : $sensors->get());
    }

    public function store(SensorRequest $request)
    {
        $sensor = Sensor::create($request->all());
        return new SensorResource($sensor); 
    }

    public function show(Sensor $sensor)
    {
        return new SensorResource($sensor);
    }

    public function update(SensorRequest $request, Sensor $sensor)
    {
        $sensor->update($request->all());

        if ($request->get('favorite_name')) {
            $sensor->favorite->update(['name' => $request->get('favorite_name')]);
        }

        return new SensorResource($sensor);
    }

    public function destroy(Sensor $sensor)
    {
        $sensor->delete();
        return response(null, 204);
    }

    public function metadata()
    {
        return response([
            'src_types'      => Sensor::$SRC_TYPES,
            'val_types'      => Sensor::$VAL_TYPES,
            'rrule_freq'     => Sensor::$RRULE_FREQ,
            'rrule_interval' => Sensor::$RRULE_INTERVAL,
        ]);
    }

    public function favorite(Sensor $sensor)
    {
        $sensor->favorite
            ? $sensor->favorite()->delete()
            : $sensor->favorite()->create([
                'name' => $sensor->name
            ]);

        $sensor->load('favorite');
        return new SensorResource($sensor);
    }
}
