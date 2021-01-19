<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Sensor;
use App\History;
use App\Action;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $qry = History::with('historyable')->where('created_at', '>', now()->subHours(env('DASHBOARD_HISTORY_FAIL_HOURS_TO_SHOW', 8)))
            ->orderBy('ocurrence_at', 'DESC');

        if($request->get('limit')) {
            $qry->limit($request->get('limit'));
        }

        if($request->get('status')) {
            $qry->whereStatus($request->get('status'));
        }

        $qry->where(function ($morph_query) use ($user) {
            $morph_query->whereHasMorph('historyable', [Sensor::class], function (Builder $query) use ($user) {
                $query->whereIn('group_id', $user->groups->pluck('id')->toArray());
            });
            $morph_query->orWhereHasMorph('historyable', [Action::class], function (Builder $query) use ($user) {
                $query->whereHas('sensor', function ($sens) use ($user) {
                    $sens->whereIn('group_id', $user->groups->pluck('id')->toArray());
                });
            });
        });

        return HistoryResource::collection($qry->get());
    }
}
