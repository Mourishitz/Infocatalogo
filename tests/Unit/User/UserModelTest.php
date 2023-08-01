<?php

namespace Tests\Unit\User;

use App\Models\Post;
use App\Models\User;
use Tests\CreatesApplication;
use Tests\TestCase;


class UserModelTest extends TestCase
{
    use CreatesApplication;

    public function test_user_can_be_instantiated(): User
    {

        $user = User::factory()->create();

        $this->assertDatabaseHas('users', $user->getAttributes());
        $this->assertInstanceOf(User::class, $user);

        return $user;
    }

    /**
     * @depends test_user_can_be_instantiated
     */
    public function test_user_can_have_posts(User $user): void
    {
        Post::factory(5)->for($user, 'author')->create();

        $posts = $user->posts;
        $this->assertNotNull($posts);

        foreach ($posts as $post){
            $this->assertDatabaseHas('posts', $post->getAttributes());
        }
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @depends test_user_can_be_instantiated
     */
    public function test_users_can_be_updated(User $user)
    {
        $old_attributes = $user->getAttributes();
        $attributes = User::factory()->make()->getAttributes();

        $user->update($attributes);

        $this->assertDatabaseHas('users', $user->getAttributes());
        $this->assertDatabaseMissing('users', $old_attributes);
        return $user;
    }

    /**
     * @depends test_users_can_be_updated
     */
    public function test_users_can_be_deleted(User $user)
    {
        User::destroy($user['id']);
        $this->assertDatabaseMissing('users', $user->getAttributes());
    }
}
