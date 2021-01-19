<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use Illuminate\Http\Request;
use App\Group;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Group::class, 'group');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = Group::latest();

        if(!$user->is_admin) {
            $q->whereHas('users', function ($u) use ($user) {
                $u->where('id', $user->id);
            });
        }

        return GroupResource::collection($request->has('page') ? $q->paginate() : $q->get());
    }

    public function store(GroupRequest $request)
    {
        $group = Group::create($request->all());
        return new GroupResource($group); 
    }

    public function show(Group $group)
    {
        return new GroupResource($group); 
    }

    public function update(GroupRequest $request, Group $group)
    {
        $group->update($request->all());
        return new GroupResource($group);
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return response(null, 204);
    }
}
