<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        protected User $repository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection($this->repository->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);

        $user = $this->repository->create($data);
        $user['token'] = $user->createToken('token')->plainTextToken;
        return new Response(['data' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = $this->repository->findOrFail($id);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $data = $request->validated();

        $user = $this->repository->findOrFail($id);
        $user->update(['name' => $data['name']]);

        return ['data' => $user];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = $this->repository->findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
