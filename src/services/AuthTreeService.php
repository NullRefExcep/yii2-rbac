<?php

namespace nullref\rbac\services;

use nullref\rbac\ar\AuthItem;
use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\helpers\AuthNode;
use yii\rbac\Item;

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
                $children = $this->getArrayAuthTree($child);
                usort($children, function ($node1, $node2) {
                    $type1 = $node1['type'];
                    $type2 = $node2['type'];
                    if ($type1 === $type2) {
                        return strcmp($node1['name'], $node2['name']);
                    } else {
                        return ($node1['type'] === Item::TYPE_ROLE) ? false : true;
                    }
                });
                $arrayTree[$childItem->name] = [
                    'name'        => $childItem->name,
                    'description' => $childItem->description,
                    'type'        => $childItem->type,
                    'rule'        => $childItem->rule_name,
                    'children'    => $children,
                ];
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
                    'children' => $this->getArrayAuthTreeStructure($child, $selected),
                    'selected' => in_array($childItem->name, $selected),
                    'folder'   => true,
                    'expanded' => true,
                ];
                if ($arrayTree[$i]['children'] == []) {
                    unset($arrayTree[$i]['children']);
                    $arrayTree[$i]['folder'] = false;
                    $arrayTree[$i]['expanded'] = false;
                }
                $i++;
            }
        }

        return $arrayTree;
    }

    public function getArrayAuthList(AuthNode $node)
    {
        $arrayList = [];

        $children = $node->getChildren();
        $childrenAmount = count($children);
        if ($childrenAmount > 0) {
            foreach ($children as $child) {
                $childItem = $child->getItem();
                $childArray = $this->getArrayAuthList($child);
                $arrayList[$childItem->name] = array_keys($childArray);
                $arrayList = array_merge($arrayList, $childArray);
            }
        }

        return $arrayList;
    }

    public function getAuthTree()
    {
        /** @var AuthItem[] $items */
        $items = AuthItem::find()
            ->all();

        $children = AuthItemChild::find()
            ->all();

        $pool = [];
        foreach ($items as $item) {
            $authNode = new AuthNode();
            $authNode->setItem($item);
            $pool[$item->name] = $authNode;
        }

        foreach ($children as $child) {
            $childName = $child->child;
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
