<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('like post', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = $user->posts()->create(Post::factory()->make()->getAttributes());

    $response = $this->post('/api/like/post/'.$post->id);

    $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'owner',
                'post',
            ],
        ]);

    return $post;
});

test('like comment', function ($post) {
    $user = User::factory()->create();
    $this->actingAs($user);

    $comment = new Comment(Comment::factory()->make()->getAttributes());
    $comment->owner()->associate($user);
    $comment->post()->associate($post);
    $comment->save();

    $response = $this->post('/api/like/comment/'.$comment->id);

    $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'owner',
                'post',
            ],
        ]);

})->depends('like post');

test('get post likes', function ($post) {
    $response = $this->get('/api/post/likes/'.$post->id);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'count',
            'data' => [
                '*' => [
                    'id',
                    'owner',
                ],
            ],
        ]);

})->depends('like post');

test('get comment likes', function ($comment) {
    $response = $this->get('/api/comment/likes/'.$comment->id);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'count',
            'data' => [
                '*' => [
                    'id',
                    'owner',
                ],
            ],
        ]);

})->depends('like comment');

test('dislike post', function ($post) {
    $response = $this->delete('/api/like/'.$post->id);

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'message' => 'Post disliked',
        ]);

    return $post;
})->depends('like post');

test('dislike a post that is not liked', function ($post) {
    $response = $this->delete('/api/like/'.$post->id);

    $response
        ->assertStatus(400)
        ->assertExactJson([
            'message' => 'Post is not liked',
        ]);
})->depends('dislike post');

test('dislike comment', function ($comment) {
    $response = $this->delete('/api/like/comment'.$comment->id);

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'message' => 'Comment disliked',
        ]);

    return $comment;
})->depends('like comment');

test('dislike a comment that is not liked', function ($comment) {
    $response = $this->delete('/api/like/'.$comment->id);

    $response
        ->assertStatus(400)
        ->assertExactJson([
            'message' => 'Post is not liked',
        ]);
})->depends('dislike post');
