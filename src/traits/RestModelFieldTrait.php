<?php

namespace nullref\rbac\traits;

trait RestModelFieldTrait
{
    /** @var array */
    public $filteredFields = [];

    /** @var array */
    private $currentFields = [];

    /**
     * Filter model fields in pair with RestFieldCheckerViewBehavior
     *
     * @return array
     */
    private function getCurrentFields()
    {
        if (empty($this->currentFields)) {
            $fields = $this->currentFields();

            foreach ($this->filteredFields as $key) {
                if (array_key_exists($key, $fields)) {
                    unset($fields[$key]);
                }
                $foundKey = array_search($key, $fields);
                if ($foundKey !== false) {
                    unset($fields[$foundKey]);
                }
            }

            $this->currentFields = $fields;
        }

        return $this->currentFields;
    }
}