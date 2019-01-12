<?php

namespace nullref\rbac\services;

use nullref\rbac\ar\AuthItem;
use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\helpers\AuthNode;

class AuthTreeService
{
    public function getArrayAuthTree(AuthNode $tree)
    {
        $arrayTree = [];

        $children = $tree->getChildren();
        $childrenAmount = count($children);
        if ($childrenAmount > 0) {
            foreach ($children as $child) {
                $childItem = $child->getItem();
                $arrayTree[$childItem->name] = [
                    'name'        => $childItem->name,
                    'description' => $childItem->description,
                    'type'        => $childItem->type,
                    'rule'        => $childItem->rule_name,
                    'children'    => [],
                ];
                $innerChildren = $child->getChildren();
                $innerChildrenAmount = count($innerChildren);
                if ($innerChildrenAmount > 0) {
                    $arrayTree[$childItem->name]['children'] = $this->getArrayAuthTree($child);
                }
            }
        }

        return $arrayTree;
    }

    public function getArrayAuthTreeStructure(AuthNode $tree, $selected = [])
    {
        $arrayTree = [];

        $children = $tree->getChildren();
        $childrenAmount = count($children);
        if ($childrenAmount > 0) {
            $i = 0;
            foreach ($children as $child) {
                $childItem = $child->getItem();
                $arrayTree[$i] = [
                    'title'    => ($childItem->description) ? $childItem->description : $childItem->name,
                    'key'      => $childItem->name,
                    'type'     => $childItem->type,
                    'rule'     => $childItem->rule_name,
                    'children' => [],
                    'selected' => in_array($childItem->name, $selected),
                ];
                $innerChildren = $child->getChildren();
                $innerChildrenAmount = count($innerChildren);
                if ($innerChildrenAmount > 0) {
                    $arrayTree[$i]['children'] = $this->getArrayAuthTreeStructure($child, $selected);
                    $arrayTree[$i]['folder'] = true;
                    $arrayTree[$i]['expanded'] = true;
                } else {
                    unset($arrayTree[$i]['children']);
                }
                $i++;
            }
        }

        return $arrayTree;
    }

    public function getAuthTree()
    {
        /** @var AuthItem[] $items */
        $items = AuthItem::find()
            ->indexBy('name')
            ->all();

        $children = AuthItemChild::find()
            ->indexBy('child')
            ->all();

        $pool = [];
        foreach ($items as $item) {
            $authNode = new AuthNode();
            $authNode->setItem($item);
            $pool[$item->name] = $authNode;
        }

        foreach ($children as $childName => $child) {
            $parentNode = isset($pool[$child->parent]) ? $pool[$child->parent] : null;
            $childNode = isset($pool[$childName]) ? $pool[$childName] : null;
            if ($parentNode && $childNode) {
                $parentNode->addChild($childNode);
                $childNode->setParent($childNode);
            }
        }

        foreach ($pool as $nodeName => $item) {
            /** @var AuthNode $item */
            if ($item->getParent() instanceof AuthNode) {
                unset($pool[$nodeName]);
            }
        }

        /** @var AuthNode $tree */
        $tree = new AuthNode();
        $tree->setChildren($pool);

        return $tree;
    }
}