<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Tests\CreatesApplication;
use Tests\TestCase;

class PostTest extends TestCase
{
    use CreatesApplication;

    public function test_get_all_posts(): void
    {
        $response = $this->get('/api/post');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'type',
                        'author',
                    ]
                ]
            ]);
    }

    public function test_create_post(): array
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/api/post', [
            'title' => 'test post',
            'type' => 'Post',
        ]);


        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'type',
                    'author',
                ]
            ]);

        return $response['data'];
    }

    /**
     * @depends test_create_post
     */
    public function test_get_post_by_id(array $post)
    {
        $id = $post['id'];
        $response = $this->get("/api/post/$id");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
               'data' => [
                   'id',
                   'title',
                   'type',
                   'author'
               ]
            ]);
    }

    public function test_get_post_not_found_by_id()
    {
        $response = $this->get('/api/post/0');

        $response
            ->assertStatus(404)
            ->assertExactJson(['message' => 'Record not found.']);
    }

    /**
     * @depends test_create_post
     */
    public function test_update_post(array $post): void
    {
        $user = User::find($post['author']);
        $this->actingAs($user);

        $id = $post['id'];

        $response = $this->put("/api/post/$id", [
            'title' => 'test updated post',
        ]);


        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => $id,
                    'title' => 'test updated post',
                    'type' => $post['type'],
                    'author' => $user->id,
                ]
            ]);
    }

    /**
     * @depends test_create_post
     */
    public function test_delete_post_with_wrong_id(array $post): void
    {
        $randomUser = User::factory()->create();
        $this->actingAs($randomUser);

        $id = $post['id'];
        $response = $this->delete("/api/post/$id");

        $response
            ->assertStatus(401)
            ->assertJson(['message' => 'This is not your post, go away']);

        $randomUser->delete();
    }

    /**
     * @depends test_create_post
     */
    public function test_delete_post(array $post): void
    {
        $user = User::find($post['author']);
        $this->actingAs($user);

        $id = $post['id'];
        $response = $this->delete("/api/post/$id");

        $response
            ->assertNoContent();
    }
}
