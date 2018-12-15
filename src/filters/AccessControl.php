<?php

namespace nullref\rbac\filters;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\AuthItem;
use nullref\rbac\repositories\ActionAccessRepository;
use Yii;
use yii\base\InlineAction;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl as BaseAccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class AccessControl extends BaseAccessControl
{
    /** @var Controller */
    public $controller;

    /** @var ActionAccessRepository */
    protected $actionAccessRepository;

    /** @var string */
    protected $userComponent;

    public function init()
    {
        $moduleUserComponent = Yii::$app->getModule('rbac')->userComponent;
        try {
            $this->userComponent = Yii::$app->{$moduleUserComponent};
        } catch (\Exception $e) {
            try {
                $this->userComponent = Yii::$app->getModule($moduleUserComponent);
            } catch (\Exception $e) {
                throw new InvalidConfigException('Bad userComponent provided');
            }
        }

        $this->actionAccessRepository = Yii::$container->get(ActionAccessRepository::class);

        /** @var Controller $controller */
        $controllerClass = $this->controller;
        $module = $controllerClass->module->id;
        $controller = $controllerClass->id;
        $action = $controllerClass->action->id;
        $this->rules = $this->getRules($module, $controller, $action);

        /**
         * @param $rule AccessRule|null
         * @param $action ErrorAction|InlineAction
         *
         * @return Response
         */
        $this->denyCallback = function ($rule, $action) {
            $controller = $action->controller;
            if ($this->userComponent->isGuest) {
                return $controller->redirect('/user/login');
            }
            Yii::$app->session->setFlash('warning', Yii::t('rbac', 'You don\'t have permission to')
                . ' ' . Yii::t('rbac', 'do this action'));

            return $controller->redirect(Yii::$app->request->referrer);
        };

        parent::init();
    }

    public function getRules($module, $controller, $action)
    {
        $rules = $this->rules;
        $isUserGuest = $this->userComponent->isGuest;

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
}
