<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $node array
 */

$hasChildren = isset($node['children']) && !empty($node['children']);

?>

<li
        class="auth-list-item"
        data-name="<?= $node['name'] ?>"
        data-update-url="<?= Url::to(['role/update-hierarchy', 'name' => $node['name']]) ?>">
    <div>
        <a class="btn btn-xs btn-primary drag-btn"><i class="fa fa-arrows"></i></a>
        <span>
            <i class="icon-folder-open"></i><?= $node['name'] ?>
        </span>
        <?= Html::a(FA::i(FA::_PENCIL),
            ['update', 'name' => $node['name']],
            ['class' => 'btn btn-xs btn-primary']) ?>
        <?= Html::a(FA::i(FA::_PLUS),
            ['create', 'parent_id' => $node['name']],
            ['class' => 'btn btn-xs btn-success']) ?>
        <?= Html::a(
            FA::i(FA::_TRASH),
            ['delete', 'name' => $node['name']],
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
