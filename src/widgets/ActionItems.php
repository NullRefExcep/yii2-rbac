<?php

namespace nullref\rbac\widgets;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\ActionAccessItem;
use nullref\rbac\repositories\AuthItemRepository;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class ActionItems extends Widget
{
    /** @var integer */
    public $actionId;

    /** @var AuthItemRepository */
    public $repository;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        if ($this->actionId === null) {
            throw new InvalidConfigException('You should set ' . __CLASS__ . '::$actionId');
        }
    }

    /** @inheritdoc */
    public function run()
    {
        $isUpdated = false;

        /** @var ActionAccess $model */
        $model = ActionAccess::findOne($this->actionId);
        $model->items = ActionAccessItem::getActionItems($this->actionId);

        if ($model->load(Yii::$app->request->post())) {
            $model->assignItems();
            $isUpdated = true;
        }

        $items = $this->repository->getMap('name', 'description');

        return $this->render('form', [
            'model'     => $model,
            'isUpdated' => $isUpdated,
            'items' => $items
        ]);
    }
}