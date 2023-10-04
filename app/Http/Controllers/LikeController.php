<?php

namespace App\Http\Controllers;

use App\Http\Requests\Like\LikeRequest;
use App\Http\Resources\LikeResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LikeController extends Controller
{
    public function likePost(LikeRequest $request): Response
    {
        /** @var User $user * */
        $user = $request->user();

        /** @var Post $post * */
        $post = Post::find($request->id);

        try {
            return new Response(
                ['data' => new LikeResource($this->like($user, $post))],
                status: ResponseAlias::HTTP_CREATED
            );

        } catch (InvalidArgumentException $e) {
            return new Response(['message' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function getPostLikes(int $id): Response
    {
        $post = Post::find($id);

        return new Response(['data' => LikeResource::collection($post->likes)], ResponseAlias::HTTP_OK);
    }

    public function dislikePost(Request $request, int $id): JsonResponse|Response
    {
        /** @var Post $post * */
        $post = Post::find($id);

        try {
            $this->dislike($request->user(), $post);

            return response()->json(null, 204);
        } catch (InvalidArgumentException $e) {
            return new Response(['message' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function likeComment(LikeRequest $request): Response
    {
        /** @var User $user * */
        $user = $request->user();

        /** @var Comment $comment * */
        $comment = Comment::find($request->id);

        try {
            return new Response(
                ['data' => new LikeResource($this->like($user, $comment))],
                status: ResponseAlias::HTTP_CREATED
            );

        } catch (InvalidArgumentException $e) {
            return new Response(['message' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function getCommentLikes(int $id): Response
    {
        $comment = Comment::find($id);

        return new Response(['data' => LikeResource::collection($comment->likes)], ResponseAlias::HTTP_OK);
    }

    public function dislikeComment(Request $request, int $id): JsonResponse|Response
    {

        /** @var Comment $comment * */
        $comment = Comment::find($id);

        try {
            $this->dislike($request->user(), $comment);

            return response()->json(null, 204);
        } catch (InvalidArgumentException $e) {
            return new Response(['message' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a Like associated to a user on a likeable model.
     *
     * @throws InvalidArgumentException
     */
    private function like(User $user, Model $model): Like
    {
        if ($model->likes()->where('owner_id', $user->id)->exists()) {
            $class = strtolower(class_basename($model));
            throw new InvalidArgumentException(
                message: "This user already likes this $class"
            );
        }

        /** @var Like $like* */
        $like = $model->likes()->make();

        $like->owner()->associate($user);
        $like->likeable()->associate($model);
        $like->save();

        return $like;
    }

    /**
     * Deletes a Like associated to a user on a likeable model.
     *
     * @throws InvalidArgumentException
     */
    private function dislike(User $user, Model $model): void
    {
        /** @var Like $like * */
        $like = $model->likes()->where('owner_id', '=', $user->id)->get()->first();

        if ($user->cannot('delete', $like)) {
            $class = strtolower(class_basename($model));
            throw new InvalidArgumentException(
                message: "This user don't likes this $class"
            );
        }

        $like->delete();
    }
}
