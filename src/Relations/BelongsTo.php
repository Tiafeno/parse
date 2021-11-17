<?php

namespace Cpp\Parse\Relations;

use Cpp\Parse\ObjectModel;

class BelongsTo extends Relation
{
    protected $embeddedClass;

    protected $keyName;

    protected $childObject;

    public function __construct($embeddedClass, $keyName, ObjectModel $childObject)
    {
        $this->embeddedClass = $embeddedClass;
        $this->childObject   = $childObject;
        $this->keyName       = $keyName;
    }

    public function getResults()
    {
        $class = $this->embeddedClass;

        $parent = $this->childObject->getParseObject()->get($this->keyName);

        if ($parent) {
            return (new $class($parent))->fetch();
        } else {
            return null;
        }
    }
}
