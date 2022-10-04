<?php

namespace CloudinaryLabs\CloudinaryLaravel\Tests;

use Illuminate\Support\Facades\Schema;

class MediaAllyTest extends TestCase
{
    public function test_has_public_id_field_in_db()
    {
        $this->assertTrue(Schema::hasColumn("media", "public_id"));
    }

    public function test_can_return_public_id()
    {
    }
}
