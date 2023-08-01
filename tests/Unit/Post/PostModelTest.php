<?php

namespace Tests\Unit\Post;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_post_can_be_instantiated()
    {
        $post = Post::factory()->for($this->user, 'author')->create();

        $this->assertDatabaseHas('posts', $post->getAttributes());
    }
}
