<?php

namespace nullref\rbac\models\helpers;

use nullref\rbac\models\AuthItem;

class AuthNode
{
    /** @var AuthItem */
    private $item;

    /** @var AuthNode */
    private $parent;

    /** @var AuthNode[] */
    private $children = [];

    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param AuthItem $item
     */
    public function setItem(AuthItem $item)
    {
        $this->item = $item;
    }

    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param AuthNode $item
     */
    public function setParent(AuthNode $item)
    {
        $this->parent = $item;
    }

    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param AuthNode $item
     */
    public function addChild(AuthNode $item)
    {
        $this->children[$item->getItem()->name] = $item;
    }

    /**
     * @param AuthNode $item
     */
    public function removeChildren(AuthNode $item)
    {
        unset($this->children[$item->getItem()->name]);
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }
}