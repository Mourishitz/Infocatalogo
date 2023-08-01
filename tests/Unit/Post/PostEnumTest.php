<?php

namespace Tests\Unit\Post;

use App\Enums\PostTypeEnum;
use Tests\TestCase;

class PostEnumTest extends TestCase
{
    public function test_getByIndex_function()
    {
        $length = count(PostTypeEnum::cases());
        $this->assertNull(PostTypeEnum::getByIndex($length));

        for ($i = 0; $i < $length; $i++) {
            $this->assertNotNull(PostTypeEnum::getByIndex($i));
        }
    }
}
