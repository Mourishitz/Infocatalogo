<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Tests\CreatesApplication;
use Tests\TestCase;

class UserTest extends TestCase
{
    use CreatesApplication;

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
                    ],
                ],
            ]);
    }

    public function test_create_user(): array
    {
        $response = $this->post('/api/user', [
            'name' => 'test user',
            'email' => 'test@mail.com',
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
                ],
            ]);

        return $response['data'];
    }

    public function test_create_user_with_existing_email()
    {
        $response = $this->post('/api/user', [
            'name' => 'test user',
            'email' => 'test@mail.com',
            'password' => 'test password',
        ]);

        $response
            ->assertInvalid();
    }

    /**
     * @depends test_create_user
     */
    public function test_get_user_by_id(array $user): void
    {
        self::assertNotNull($user);
        $id = $user['id'];

        $response = $this->get("/api/user/$id");

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => $id,
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'created_at' => Carbon::make($user['created_at'])->format('Y-m-d'),
                ],
            ]);
    }

    public function test_user_not_found_by_id()
    {
        $response = $this->get('/api/user/0');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'message' => 'Record not found.',
            ]);
    }

    /**
     * @depends test_create_user
     */
    public function test_update_user(array $user): void
    {
        self::assertNotNull($user);

        $id = $user['id'];

        $response = $this->put("/api/user/$id", [
            'name' => 'test updated user',
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => $id,
                    'name' => 'test updated user',
                    'email' => $user['email'],
                    'created_at' => Carbon::make($user['created_at'])->format('Y-m-d'),
                ],
            ]);
    }

    /**
     * @depends test_create_user
     */
    public function test_delete_user_with_wrong_id(array $user): void
    {
        $user = User::findOrFail($user['id']);
        $this->actingAs($user);
        $response = $this->delete('/api/user/0');

        $response
            ->assertStatus(401)
            ->assertJson(['message' => 'This is not your user, go away']);
    }

    /**
     * @depends test_create_user
     */
    public function test_delete_user(array $user): void
    {
        $user = User::findOrFail($user['id']);

        self::assertNotNull($user);
        $id = $user['id'];

        $this->actingAs($user);
        $response = $this->delete("/api/user/$id");

        $response
            ->assertNoContent();
    }
}
