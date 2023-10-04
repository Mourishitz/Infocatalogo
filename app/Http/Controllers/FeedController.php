<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function mostLikedPosts(Request $request)
    {
        $posts = Post::withCount('likes')
            ->orderByDesc('likes_count')
            ->take(5)
            ->get();

        return new Response(PostResource::collection($posts));
    }

    public function mostCommentedPosts(Request $request)
    {
        $posts = Post::withCount('comments')
            ->orderByDesc('comments_count')
            ->take(5)
            ->get();

        return new Response(PostResource::collection($posts));
    }

    public function mostLikedAuthors(Request $request)
    {
        $users = User::select('users.*')
            ->join('posts', 'users.id', '=', 'posts.author_id')
            ->join('likes', function ($join) {
                $join->on('posts.id', '=', 'likes.likeable_id')
                    ->where('likes.likeable_type', '=', 'App\Models\Post');
            })
            ->groupBy('users.id')
            ->orderByRaw('COUNT(likes.id) DESC')
            ->take(5)
            ->get();

        return new Response(UserResource::collection($users));
    }

    public function mostCommentedAuthors(Request $request)
    {
        $users =  User::select('users.*')
            ->join('posts', 'users.id', '=', 'posts.author_id')
            ->join('comments', function ($join) {
                $join->on('posts.id', '=', 'comments.commentable_id')
                    ->where('comments.commentable_type', '=', 'App\Models\Post');
            })
            ->groupBy('users.id')
            ->orderByRaw('COUNT(comments.id) DESC')
            ->take(5)
            ->get();

        return new Response(UserResource::collection($users));
    }
}
