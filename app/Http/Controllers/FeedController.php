<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function mostLikedPosts(Request $request)
    {
        $posts = Post::select('posts.*')
            ->leftJoin('likes', function ($join) {
                $join->on('likes.likeable_id', '=', 'posts.id')
                    ->where('likes.likeable_type', '=', 'App\\Post');
            })
            ->leftJoin('comments', 'comments.post_id', '=', 'posts.id')
            ->groupBy('posts.id')
            ->orderByRaw('COUNT(likes.id) + COUNT(comments.id) DESC')
            ->take(5)
            ->get();

        return new Response(PostResource::collection($posts));
    }

    public function mostCommentedPosts(Request $request)
    {

        // TODO: Implement query params
    }

    public function mostLikedAuthors(Request $request)
    {
        // TODO: Implement query params
    }

    public function mostCommentedAuthors(Request $request)
    {
        // TODO: Implement query params
    }
}
