<?php

namespace nullref\rbac\helpers\element;

use nullref\rbac\services\ElementCheckerService;
use yii\helpers\BaseHtml;

class ElementHtml extends BaseHtml
{
    /** @var ElementCheckerService */
    public static $elementCheckerService;

    public static function tag($name, $content = '', $options = [])
    {
        $result = parent::tag($name, $content = '', $options = []);
        if (isset($options['data-identificator'])) {
            if (static::$elementCheckerService instanceof ElementCheckerService) {
                if (!static::$elementCheckerService->isAllowed($options['data-identificator'])) {
                    $result = '';
                }
            }
        }

        return $result;
    }
}