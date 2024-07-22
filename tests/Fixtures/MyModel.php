<?php

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

class MyModel extends Model
{
    protected $table = 'model';

    use MediaAlly;
}
