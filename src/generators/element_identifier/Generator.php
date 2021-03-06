<?php

namespace nullref\rbac\generators\element_identifier;

use nullref\core\traits\VariableExportTrait;
use nullref\rbac\services\ViewModifierService;
use yii\gii\CodeFile;
use yii\gii\Generator as BaseGenerator;

class Generator extends BaseGenerator
{
    use VariableExportTrait;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Element Identifier (RBAC)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates identifiers for ElementHtml elements in view provided by path';
    }

    public $aliases;

    /** @var ViewModifierService */
    private $viewModifierService;

    public function __construct(
        ViewModifierService $viewModifierService
    )
    {
        $this->viewModifierService = $viewModifierService;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['aliases'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'aliases' => 'Specify the aliases generate identifiers for ElementHtml inside views.',
        ]);
    }

    public function getTemplatePath()
    {
        return 'Empty';
    }

    public function generate()
    {
        $files = [];
        $this->aliases = (!is_array($this->aliases)) ? [] : $this->aliases;
        foreach ($this->aliases as $alias) {
            $writtenFiles = $this->viewModifierService->writeIdentifier($alias);
            foreach ($writtenFiles as $file) {
                $filePath = $file['filePath'];
                $code = $file['content'];
                $file = new CodeFile(
                    $filePath,
                    $code
                );
                $file->id = $filePath;
                $files[] = $file;
            }
        }

        return $files;
    }

    public function getViewPaths()
    {
        $aliases = $this->viewModifierService->prepareAliases();
        $paths = [];
        foreach ($aliases as $alias) {
            $paths[$alias['alias']] = $alias['alias'];
        }

        return $paths;
    }
}
