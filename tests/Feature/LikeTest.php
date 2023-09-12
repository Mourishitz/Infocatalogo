<?php

use App\Models\Post;
use App\Models\User;

test('like post', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = $user->posts()->create(Post::factory()->make()->getAttributes());

    $response = $this->post('/api/like/'.$post->id);

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

test('get post likes', function ($post) {
    $response = $this->get('/api/like/'.$post->id);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'count',
            'data' => [
                '*' => [
                    'id',
                    'owner'
                ]
            ]
        ]);

})->depends('like post');

test('dislike post', function ($post) {
    $response = $this->delete('/api/like/'.$post->id);

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'message' => 'Post disliked'
        ]);

    return $post;
})->depends('like post');

test('dislike a post that is not liked', function ($post) {
    $response = $this->delete('/api/like/'.$post->id);

    $response
        ->assertStatus(400)
        ->assertExactJson([
            'message' => 'Post is not liked'
        ]);
})->depends('dislike post');
