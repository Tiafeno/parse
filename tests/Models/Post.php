<?php

namespace Cpp\Parse\Test\Models;

use Cpp\Parse\ObjectModel;

class Post extends ObjectModel
{
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
