<?php

namespace App\Http\Controllers\Api;

use App\Action;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActionRequest;
use App\Http\Resources\ActionResource;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Action::class, 'action');
    }

    public function index(Request $request)
    {
        $actions = Action::with(['sensor'])->whereHas('sensor', function($sensor) use ($request) {
            $sensor->whereIn('group_id', $request->user()->groups->pluck('id')->toArray());
        })->latest()->paginate();
        return ActionResource::collection($actions);
    }

    public function store(ActionRequest $request)
    {
        $action = Action::create($request->all());
        return new ActionResource($action); 
    }

    public function show(Action $action)
    {
        return new ActionResource($action); 
    }

    public function update(ActionRequest $request, Action $action)
    {
        $action->update($request->all());

        if ($request->get('favorite_name')) {
            $action->favorite->update(['name' => $request->get('favorite_name')]);
        }

        return new ActionResource($action);
    }

    public function destroy(Action $action)
    {
        $action->delete();
        return response(null, 204);
    }

    public function metadata()
    {
        return response([
            'val_types' => Action::$VAL_TYPES,
            'types'     => Action::$TYPES,
        ]);
    }

    public function favorite(Action $action)
    {
        $action->favorite
            ? $action->favorite()->delete()
            : $action->favorite()->create([
                'name' => $action->name
            ]);

        $action->load('favorite');
        return new ActionResource($action);
    }
}
