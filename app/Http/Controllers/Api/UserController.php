<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }
    
    public function index()
    {
        return UserResource::collection(User::latest()->paginate());
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'email'    => $request->get('email'),
            'name'     => $request->get('name'),
            'password' => bcrypt($request->get('password'))
        ]);
        $user->groups()->sync($request->get('groups'));
        return new UserResource($user); 
    }

    public function my(Request $request)
    {
        return new UserResource($request->user()); 
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response([
            'status' => true, 
            'message' => 'User has been successfully logout.'
        ], 200);
    }

    public function show(User $user)
    {
        return new UserResource($user); 
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->groups()->sync($request->get('groups'));
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response(null, 204);
    }
}
