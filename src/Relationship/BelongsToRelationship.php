<?php

namespace Isswp101\Persimmon\Relationship;

use Isswp101\Persimmon\ElasticsearchModel;
use Isswp101\Persimmon\Exceptions\ParentModelNotFoundException;
use Isswp101\Persimmon\Model\IElasticsearchModel;

class BelongsToRelationship
{
    /**
     * @var IElasticsearchModel
     */
    protected $child;

    /**
     * @var IElasticsearchModel
     */
    protected $parentClass;

    public function __construct(IElasticsearchModel $child, $parentClass)
    {
        $this->child = $child;
        $this->parentClass = $parentClass;
    }

    public function associate(IElasticsearchModel $parent)
    {
        $this->child->setParent($parent);
    }

    /**
     * Return parent model.
     *
     * @return ElasticsearchModel|null
     */
    public function get()
    {
        $parent = $this->child->getParent();

        if ($parent) {
            return $parent;
        }

        $parentClass = $this->parentClass;

        $parentId = $this->child->getParentId();

        $innerHits = $this->child->getInnerHits();

        if ($innerHits) {
            $attributes = $innerHits->getParent($parentClass::getType());
            $parent = new $parentClass($attributes);
        } elseif ($parentId) {
            $parent = $parentClass::find($parentId);
        }

        if ($parent) {
            $this->child->setParent($parent);
        }

        return $parent;
    }

    /**
     * Return parent model.
     *
     * @throws ParentModelNotFoundException
     * @return ElasticsearchModel
     */
    public function getOrFail()
    {
        $model = $this->get();

        if (is_null($model)) {
            throw new ParentModelNotFoundException($this->parentClass, $this->child->getParentId());
        }

        return $model;
    }
}
