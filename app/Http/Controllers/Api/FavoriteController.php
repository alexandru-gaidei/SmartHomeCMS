<?php

namespace App\Http\Controllers\Api;

use App\Action;
use App\Favorite;
use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Sensor;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $qry = Favorite::orderBy('order');

        if ($request->get('limit')) {
            $qry->limit($request->get('limit'));
        }

        if ($request->has('type') && $request->get('type') === Favorite::TYPE_SENSOR) {
            $qry->whereHasMorph('favoriteable', [Sensor::class], function (Builder $query) use ($user) {
                $query->whereIn('group_id', $user->groups->pluck('id')->toArray());
            });
        } elseif ($request->has('type') && $request->get('type') === Favorite::TYPE_ACTION) {
            $qry->whereHasMorph('favoriteable', [Action::class], function (Builder $query) use ($user) {
                $query->whereHas('sensor', function ($sens) use ($user) {
                    $sens->whereIn('group_id', $user->groups->pluck('id')->toArray());
                });
            });
        }

        return FavoriteResource::collection($qry->get());
    }

    public function doAction(Favorite $favorite)
    {
        $action = $favorite->favoriteable;
        abort_unless($action, 404);
        dispatch(function () use ($action) {
            $action->do();
        });
        return response(null, 200);
    }

    public function reorder(Request $request)
    {
        $items = collect($request->all())->pluck('id');
        $favorites = Favorite::whereIn('id', $items)->get();

        $items->each(function ($id, $index) use ($favorites) {
            $fav = $favorites->where('id', $id)->first();
            if ($fav) {
                $fav->order = $index;
                $fav->save();
            }
        });

        return response(null, 200);
    }
}
