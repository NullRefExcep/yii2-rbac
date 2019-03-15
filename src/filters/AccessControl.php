<?php

namespace nullref\rbac\filters;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\AuthItem;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\ActionAccessRepositoryInterface;
use Yii;
use yii\base\InlineAction;
use yii\filters\AccessControl as BaseAccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use yii\web\User;

class AccessControl extends BaseAccessControl
{
    /** @var Controller */
    public $controller;

    /** @var ActionAccessRepositoryInterface */
    protected $actionAccessRepository;

    /** @var object */
    protected $userComponent;

    /** @var User|null */
    protected $userIdentity;

    /** @var string  */
    protected $loginUrl = '';

    public function beforeFilter($event)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->userComponent = $module->userComponent;
        $this->userIdentity = $module->getUserIdentity();

        $this->loginUrl = $module->loginUrl;

        $this->actionAccessRepository = Yii::$container->get(ActionAccessRepositoryInterface::class);

        /** @var Controller $controller */
        $controllerClass = $this->controller;
        $controllerModule = $controllerClass->module->id;
        $controller = $controllerClass->id;
        $action = $controllerClass->action->id;
        $this->rules = $this->getRules($controllerModule, $controller, $action);
        foreach ($this->rules as $i => $rule) {
            if (is_array($rule)) {
                $this->rules[$i] = Yii::createObject(array_merge($this->ruleConfig, $rule));
            }
        }

        $this->setDenyCallBack();

        return parent::beforeFilter($event);
    }

    protected function getRules($module, $controller, $action)
    {
        $rules = $this->rules;
        $identity = $this->userIdentity;
        $isUserGuest = true;
        if ($identity) {
            $isUserGuest = $this->userComponent->isGuest;
        }

        if ($isUserGuest) {
            $rules[] = [
                'allow'   => false,
                'actions' => [
                    $action,
                ],
            ];
        } else {
            /** @var ActionAccess $actionAccess */
            $actionAccess = $this->actionAccessRepository
                ->findOneByMCA(
                    $module,
                    $controller,
                    $action
                );
            if ($actionAccess) {
                $newRule = [];
                $roles = [];
                /** @var AuthItem[] $items */
                $items = $actionAccess->authItems;
                if ($items) {
                    foreach ($items as $item) {
                        $roles[] = $item->name;
                    }
                    $newRule = [
                        'allow'   => true,
                        'actions' => [
                            $action,
                        ],
                        'roles'   => $roles,
                    ];
                }

                $isEdited = false;
                if (!empty($newRule)) {
                    foreach ($rules as $key => $rule) {
                        if (in_array($action, $rule['actions']) && $rule['allow']) {
                            if (count($rule['actions']) == 1) {
                                $isEdited = true;
                            }
                            $rules[$key]['roles'] = ArrayHelper::merge($rules[$key]['roles'], $roles);
                        }
                    }
                }
                if (!$isEdited) {
                    if (!$items) {
                        $rules[] = [
                            'allow'   => true,
                            'actions' => [
                                $action,
                            ],
                        ];
                    } else {
                        $rules[] = $newRule;
                    }
                }
            } else {
                $rules[] = [
                    'allow'   => true,
                    'actions' => [
                        $action,
                    ],
                ];
            }
            if (!count($rules) && !$isUserGuest) {
                $rules[] = [
                    'allow'   => true,
                    'actions' => [
                        $action,
                    ],
                ];
            }
        }

        return $rules;
    }

    protected function setDenyCallBack()
    {
        /**
         * @param $rule AccessRule|null
         * @param $action ErrorAction|InlineAction
         *
         * @return Response
         */
        $this->denyCallback = function ($rule, $action) {
            $controller = $action->controller;
            if ($this->userComponent->isGuest) {
                return $controller->redirect($this->loginUrl);
            }
            Yii::$app->session->setFlash('warning', Yii::t('rbac', 'You don\'t have permission to')
                . ' ' . Yii::t('rbac', 'do this action'));

            return $controller->redirect(Yii::$app->request->referrer ?? Yii::$app->getHomeUrl());
        };
    }
}
