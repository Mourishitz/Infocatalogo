<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CommentController extends Controller
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        protected Comment $repository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CommentResource::collection($this->repository->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();
        $post = Post::find($request->post);

        $comment = new Comment($data);

        $comment->owner()->associate($user);
        $comment->post()->associate($post);

        $comment->save();

        return new Response(['data' => new CommentResource($comment)], ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $comment = $this->repository->findOrFail($id);
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, int $id)
    {
        $data = $request->validated();
        $comment = Comment::find($id);
        $comment->update($data);
        return ['data' => new CommentResource($comment)];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $comment = $this->repository->findOrFail($id);
        if($request->user()->cannot('delete', $comment)){
            return new Response(['message' => 'This is not your comment, go away'], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        $comment->delete();
        return response()->json(null, 204);
    }
}
