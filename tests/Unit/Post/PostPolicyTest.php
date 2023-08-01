<?php

namespace Tests\Unit\Post;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        [$user, $randomUser] = User::factory(2)->create();

        $this->user = $user;
        $this->randomUser = $randomUser;

        $this->post = Post::factory()->for($this->user, 'author')->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->post->delete();
        $this->user->delete();
        $this->randomUser->delete();
    }

    public function test_force_delete()
    {
        $this->assertTrue($this->user->can('forceDelete', $this->post));
        $this->assertFalse($this->randomUser->can('forceDelete', $this->post));
    }

    public function test_restore()
    {
        $this->assertTrue($this->user->can('restore', $this->post));
        $this->assertFalse($this->randomUser->can('restore', $this->post));
    }
}
