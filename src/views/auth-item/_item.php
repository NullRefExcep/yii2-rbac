<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\rbac\Item;
use yii\web\View;

/**
 * @var $this View
 * @var $node array
 */

$hasChildren = isset($node['children']) && !empty($node['children']);
$action = ($node['type'] == Item::TYPE_ROLE) ? 'role' : 'permission';

?>

<li
        class="auth-list-item <?= $action ?>"
        data-name="<?= $node['name'] ?>"
        data-update-url="<?= Url::to(['auth-item/update-hierarchy', 'name' => $node['name']]) ?>">
    <div>
        <a class="btn btn-xs btn-primary drag-btn"><i class="fa fa-arrows"></i></a>
        <span>
            <i class="icon-folder-open"></i>
            <?= $node['name'] . ' - ' . $node['description'] ?>
        </span>
        <?= Html::a(FA::i(FA::_PENCIL),
            [$action . '/update', 'name' => $node['name']],
            ['class' => 'btn btn-xs btn-primary']) ?>
        <?= Html::a(FA::i(FA::_PLUS) . ' ' . Yii::t('rbac', 'Role'),
            ['role/create', 'parentName' => $node['name']],
            ['class' => 'btn btn-xs btn-success']) ?>
        <?= Html::a(FA::i(FA::_PLUS) . ' ' . Yii::t('rbac', 'Permission'),
            ['permission/create', 'parentName' => $node['name']],
            ['class' => 'btn btn-xs btn-warning']) ?>
        <?= Html::a(
            FA::i(FA::_TRASH),
            [$action . '/delete', 'name' => $node['name']],
            [
                'data'  => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method'  => 'post',
                ],
                'class' => 'btn btn-xs btn-danger',
            ]) ?>

        <a
                href="#authList<?= $node['name'] ?>"
                class="btn btn-xs btn-primary collapse-list-btn <?= $hasChildren ? '' : 'disabled' ?>"
                role="button"
                data-toggle="collapse"
                aria-expanded="false"
                aria-controls="authList<?= $node['name'] ?>">
            <i class="fa <?= $hasChildren ? 'fa-arrow-down' : 'fa-arrow-up' ?>"></i>
        </a>
    </div>
    <ol id="authList<?= $node['name'] ?>" class="collapse auth-list <?= $hasChildren ? '' : 'in' ?>">
        <?php if ($hasChildren): ?>
            <?php foreach ($node['children'] as $child): ?>
                <?= $this->render('_item', [
                    'node' => $child,
                ]) ?>
            <?php endforeach; ?>
        <?php endif ?>
    </ol>
</li>
