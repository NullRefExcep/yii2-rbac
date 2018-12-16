<?php

namespace nullref\rbac\generators\element_identificator;

use nullref\core\traits\VariableExportTrait;
use nullref\rbac\services\ViewReaderService;
use nullref\rbac\services\ViewWriterService;
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
        return 'Element Identificator (RBAC)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates identificators for ElementHtml elements in view provided by path';
    }

    public $aliases;

    /** @var ViewReaderService */
    private $viewReaderService;

    /** @var ViewWriterService */
    private $viewWriterService;

    public function __construct(
        ViewReaderService $viewReaderService,
        ViewWriterService $viewWriterService
    )
    {
        $this->viewReaderService = $viewReaderService;
        $this->viewWriterService = $viewWriterService;
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
            'aliases' => 'Specify the aliases generate identificators for ElementHtml inside views.',
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
            $writtenFiles = $this->viewWriterService->writeIdentificator($alias);
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
        $aliases = $this->viewReaderService->prepareAliases();
        $paths = [];
        foreach ($aliases as $alias) {
            $paths[$alias['alias']] = $alias['alias'];
        }

        return $paths;
    }
}
