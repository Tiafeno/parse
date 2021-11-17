<?php

namespace Cpp\Parse\Relations;

class HasMany extends HasOneOrMany
{
    public function getResults()
    {
        return $this->query->get();
    }
}
