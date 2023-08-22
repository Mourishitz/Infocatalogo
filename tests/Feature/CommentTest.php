<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Tests\CreatesApplication;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use CreatesApplication;

    public function test_get_all_comments(): void
    {
        $response = $this->get('/api/comment');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'content',
                        'owner',
                        'post',
                    ]
                ]
            ]);
    }

    public function test_create_comment(): array
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->posts()->create(Post::factory()->make()->getAttributes());

        $response = $this->post('/api/comment', [
            'content' => 'test comment',
            'post' => $user->posts()->first()->getAttribute('id'),
        ]);


        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'content',
                    'owner',
                    'post',
                ]
            ]);

        return $response['data'];
    }

    /**
     * @depends test_create_comment
     */
    public function test_get_comment_by_id(array $comment)
    {
        $id = $comment['id'];
        $response = $this->get("/api/comment/$id");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
               'data' => [
                   'id',
                   'content',
                   'owner',
                   'post'
               ]
            ]);
    }

    public function test_get_comment_not_found_by_id()
    {
        $response = $this->get('/api/comment/0');

        $response
            ->assertStatus(404)
            ->assertExactJson(['message' => 'Record not found.']);
    }

    /**
     * @depends test_create_comment
     */
    public function test_update_comment(array $comment): void
    {
        $user = User::find($comment['owner']);
        $this->actingAs($user);

        $id = $comment['id'];

        $response = $this->put("/api/comment/$id", [
            'content' => 'test updated comment',
        ]);


        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => $id,
                    'content' => 'test updated comment',
                    'post' => $comment['post'],
                    'owner' => $user->id,
                ]
            ]);
    }

    /**
     * @depends test_create_comment
     */
    public function test_delete_comment_with_wrong_id(array $comment): void
    {
        $randomUser = User::factory()->create();
        $this->actingAs($randomUser);

        $id = $comment['id'];
        $response = $this->delete("/api/comment/$id");

        $response
            ->assertStatus(401)
            ->assertJson(['message' => 'This is not your comment, go away']);

        $randomUser->delete();
    }

    /**
     * @depends test_create_comment
     */
    public function test_delete_comment(array $comment): void
    {
        $user = User::find($comment['owner']);
        $this->actingAs($user);

        $id = $comment['id'];
        $response = $this->delete("/api/comment/$id");

        $response
            ->assertNoContent();
    }
}
