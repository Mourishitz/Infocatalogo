<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PostController extends Controller
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        protected Post $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection($this->repository->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        return new Response(['data' => new PostResource($request->user()->posts()->create($data))], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $post = $this->repository->findOrFail($id);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, int $id)
    {
        $data = $request->validated();
        $post = $this->repository->findOrFail($id);

        if ($request->user()->cannot('update', $post)) {
            return new Response(['message' => 'This is not your post, go away'], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        $post->update($data);
        return ['data' => new PostResource($post)];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $post = $this->repository->findOrFail($id);

        if ($request->user()->cannot('delete', $post)) {
            return new Response(['message' => 'This is not your post, go away'], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        $post->delete();

        return response()->json(null, 204);
    }
}
