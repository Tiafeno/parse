<?php

namespace Cpp\Parse\Test\Models;

use Cpp\Parse\ObjectModel;

class User extends ObjectModel
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
