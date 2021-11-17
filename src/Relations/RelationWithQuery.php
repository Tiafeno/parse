<?php

namespace Cpp\Parse\Relations;

use Cpp\Parse\Query;
use Cpp\Parse\ObjectModel;

abstract class RelationWithQuery extends Relation
{
    protected $query;

    /**
     * @param ObjectModel
     */
    protected $parentObject;

    abstract protected function addConstraints();

    public function __construct(Query $query, ObjectModel $parentObject)
    {
        $this->query = $query;
        $this->parentObject = $parentObject;

        $this->addConstraints();
    }

    public function __call($method, $parameters)
    {
        $result = call_user_func_array([$this->query, $method], $parameters);

        if ($result === $this->query) {
            return $this;
        }

        return $result;
    }
}
