<?php

namespace Cpp\Parse\Test\Models;

use Cpp\Parse\ObjectModel;

class Category extends ObjectModel
{
    public function posts()
    {
        return $this->hasManyArray(Post::class);
    }
}
