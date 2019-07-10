<?php

namespace nullref\rbac\controllers;

use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\components\BaseController;
use nullref\rbac\repositories\AuthItemChildRepository;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\services\AuthTreeService;
use Yii;

class AuthItemController extends BaseController
{
    /** @var AuthTreeService */
    private $authTree;

    /** @var AuthItemRepository */
    private $aicRepository;

    public function __construct(
        $id,
        $module,
        $config = [],
        AuthTreeService $authTree,
        AuthItemChildRepository $aicRepository
    )
    {
        $this->authTree = $authTree;
        $this->aicRepository = $aicRepository;

        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $arrayTree = $this->authTree->getArrayAuthTree($this->authTree->getAuthTree());

        return $this->render('index', [
            'tree' => $arrayTree,
        ]);
    }

    public function actionUpdateHierarchy($name)
    {
        if (Yii::$app->request->isPost) {
            $tree = Yii::$app->request->post('tree', []);
            $parentName = isset($tree['parentName']) ? $tree['parentName'] : false;
            if ($parentName !== false) {
                if ($parentName == '') {
                    AuthItemChild::deleteAll(['child' => $name]);
                } else {
                    /** @var AuthItemChild $authChild */
                    $authChild = $this->aicRepository->getChildByName($name);
                    if ($authChild) {
                        $authChild->parent = $parentName;
                    } else {
                        $authChild = new AuthItemChild();
                        $authChild->child = $name;
                        $authChild->parent = $parentName;
                    }
                    $this->aicRepository->save($authChild);
                }
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
