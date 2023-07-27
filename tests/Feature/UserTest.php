<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserTest extends TestCase
{

    public function test_get_all_users(): void
    {
        $response = $this->get('/api/user');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'email',
//                        'posts',
                    ]
                ]
            ]);
    }

    public function test_create_user(): int
    {
        $response = $this->post('/api/user', [
            'name' => 'test user',
            'email' => 'test15@mail.com',
            'password' => 'test password',
        ]);


        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'token',
                    'created_at',
                ]
            ]);

        return $response['data']['id'];
    }

    /**
     * @depends test_create_user
     */
    public function test_get_user_by_id(int $id): void
    {
        self::assertNotNull($id);

        $response = $this->get("/api/user/$id");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at'
                ]
            ]);
    }

    /**
     * @depends test_create_user
     */
    public function test_update_user(int $id): void
    {
        $response = $this->put("/api/user/$id", [
            'name' => 'test updated user',
        ]);


        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ]
            ]);
    }

    /**
     * @depends test_create_user
     */
    public function test_delete_user(int $id): void
    {
        $response = $this->delete("/api/user/$id");

        $response
            ->assertNoContent();
    }

}
