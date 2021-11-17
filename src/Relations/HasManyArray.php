<?php

namespace Cpp\Parse\Relations;

use Cpp\Parse\Query;
use Cpp\Parse\ObjectModel;

class HasManyArray extends HasMany
{
    public function addConstraints()
    {
        $this->query->containedIn($this->foreignKey, $this->parentObject);
    }

    /**
     * Relate other object to this object.
     *
     * @param  ObjectModel $model The child object
     * @return ObjectModel
     */
    public function save(ObjectModel $model)
    {
        $model->addUnique($this->foreignKey, [$this->parentObject->getParseObject()]);

        $model->save();

        return $model;
    }
}
