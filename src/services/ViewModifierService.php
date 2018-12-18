<?php

namespace nullref\rbac\services;

use Yii;

class ViewModifierService
{
    const ELEMENT_CLASS     = 'ElementHtml';
    const ELEMENT_PATTERN   = 'nullref\\\rbac\\\helpers\\\element\\\ElementHtml';
    const ELEMENT_PATTERN_2 = '\\\nullref\\\rbac\\\helpers\\\element\\\ElementHtml';

    /** @var array */
    public $aliases;

    /** @var int */
    private $offset = 0;
    /** @var int */
    private $position = 0;
    /** @var int */
    private $currentPosition = 0;
    /** @var int */
    private $currentAmountOfParams = 1;
    /** @var string */
    private $currentFile = '';

    /**
     * ViewModifierService constructor.
     */
    function __construct()
    {
        $this->aliases = $this->prepareAliases();
    }

    /**
     * @return array
     */
    public function prepareAliases()
    {
        $aliases = [];

        $modules = Yii::$app->modules;
        foreach ($modules as $moduleName => $module) {
            $module = Yii::$app->getModule($moduleName, false);
            if ($module) {
                if (isset($module->viewPathAliases)) {
                    foreach ($module->viewPathAliases as $alias) {
                        $aliases[] =
                            [
                                'alias'  => $alias,
                                'module' => $moduleName,
                            ];
                    }
                }
            }
        }

        return $aliases;
    }

    /**
     * @param $alias
     *
     * @return array
     */
    public function writeIdentificator($alias)
    {
        $dirPath = Yii::getAlias($alias);
        $realPath = realpath($dirPath);
        $dirViews = $this->readDir($realPath);

        $files = [];
        foreach ($dirViews as $view) {
            $this->currentFile = file_get_contents($view['filePath']);
            $isRegularUse = false;
            $isRegularAliasUse = false;
            $aliasUse = '';
            //If regular use
            $matchR = [];
            $pattern = '/[\s]*use[\s]+' . self::ELEMENT_PATTERN . ';/';
            if (preg_match($pattern, $this->currentFile, $matchR)) {
                if (isset($matchR[0])) {
                    $isRegularUse = true;
                }
            }
            //If regular with alias
            $matchRA = [];
            $pattern = '/[\s]*use[\s]+' . self::ELEMENT_PATTERN . ' as[\s]*(.*?)[\s]*\;/';
            if (preg_match($pattern, $this->currentFile, $matchRA)) {
                if (isset($matchRA[1])) {
                    $isRegularAliasUse = true;
                    $aliasUse = $matchRA[1];
                }
            }
            //Regular match
            if ($isRegularUse) {
                $rMatch = [];
                $pattern = '/(?<!\\\)(?=[\s]*)' . self::ELEMENT_CLASS . '\:\:/';
                if (preg_match_all($pattern, $this->currentFile, $rMatch, PREG_OFFSET_CAPTURE)) {
                    if (isset($rMatch[0])) {
                        $pattern = self::ELEMENT_CLASS;
                        $this->processTag($rMatch[0], $pattern);
                    }
                }
            }
            //Regular alias match
            if ($isRegularAliasUse) {
                $rAMacth = [];
                $pattern = '/(?<!\\\)(?=[\s]*)' . $aliasUse . '\:\:/';
                if (preg_match_all($pattern, $this->currentFile, $rAMacth, PREG_OFFSET_CAPTURE)) {
                    if (isset($rAMacth[0])) {
                        $pattern = $aliasUse;
                        $this->processTag($rAMacth[0], $pattern);
                    }
                }
            }
            //Namespace match
            $nMatch = [];
            $pattern = '/' . self::ELEMENT_PATTERN_2 . '\:\:/';
            if (preg_match_all($pattern, $this->currentFile, $nMatch, PREG_OFFSET_CAPTURE)) {
                if (isset($nMatch[0])) {
                    $pattern = self::ELEMENT_PATTERN_2;
                    $this->processTag($nMatch[0], $pattern);
                }
            }
            $files[] = [
                'filePath' => $view['filePath'],
                'content'  => $this->currentFile,
            ];
        }

        return $files;
    }

    /**
     * @param $realPath
     *
     * @return array
     */
    private function readDir($realPath)
    {
        $files = [];
        if ($realPath && $handle = opendir($realPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == "." || $file == "..") {
                    continue;
                }
                $fullPath = $realPath . '/' . $file;
                if (is_dir($fullPath)) {
                    $files = array_merge($files, $this->readDir($fullPath));
                }
                $fileInfo = pathinfo($fullPath);
                if (is_file($fullPath) && $fileInfo['extension'] == 'php') {
                    $files[] = [
                        'filePath' => $fullPath,
                    ];
                }
            }
            closedir($handle);
        }

        return $files;
    }

    /**
     * @param $matches
     * @param $pattern
     */
    private function processTag($matches, $pattern)
    {
        $this->offset = 0;
        $this->currentPosition = 0;
        foreach ($matches as $match) {
            $this->currentPosition = $match[1] + $this->offset;
            $length = strlen($this->currentFile);
            $subSrting = substr($this->currentFile, $this->currentPosition, $length);
            //Find full tag
            $fullTag = [];
            $innerPattern = '/' . $pattern . '\:\:[A-Za-z]*\({1}.*\)/';
            if (preg_match($innerPattern, $subSrting, $fullTag)) {
                if (count($fullTag)) {
                    $fullTag = $fullTag[0];
                    $fullTagLength = strlen($fullTag);
                    //Find tag name
                    $tagName = [];
                    $tagPattern = '/' . $pattern . '\:\:([A-Za-z]*)\({1}/';
                    if (preg_match($tagPattern, $fullTag, $tagName)) {
                        if (isset($tagName[1])) {
                            //Find tag name
                            $tagName = $tagName[1];
                            $endOfOption = [];
                            $optionPattern = "/(?<=([\'\)\}\]]))(\))(?!(\,))/im";
                            $this->position = 0;
                            $fullTag .= ' ';
                            if (preg_match_all($optionPattern, $fullTag, $endOfOption, PREG_OFFSET_CAPTURE)) {
                                if (isset($endOfOption[2])) {
                                    $this->position = $endOfOption[2][count($endOfOption[2]) - 1][1];
                                    $this->countParams($fullTag);
                                    $this->addIdentificator($tagName);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $fullTag
     */
    private function countParams($fullTag)
    {
        $threeParam = [];
        $threeParamPattern = "/\,{1}[\s]*.*\,{1}/";
        if (preg_match($threeParamPattern, $fullTag, $threeParam)) {
            if (count($threeParam)) {
                $this->currentAmountOfParams = 3;
                $this->position -= 1;
                return;
            }
        }

        $twoParam = [];
        $twoParamPattern = "/\,{1}[\s]*[[:alpha:]\'\[\]\=\> ]*/";
        if (preg_match($twoParamPattern, $fullTag, $twoParam)) {
            if (count($twoParam)) {
                $this->currentAmountOfParams = 2;
                return;
            }
        }

        $oneParam = [];
        $oneParamPattern = "/(?<=\()[[:alpha:]\'\[\]\=\>, ]*(?<!\))/";
        if (preg_match($oneParamPattern, $fullTag, $oneParam)) {
            if (count($oneParam)) {
                $this->currentAmountOfParams = 1;
                return;
            }
        }

        $this->currentAmountOfParams = 1;
        return;
    }

    /**
     * @param $tagName
     * @param bool $isAppend
     */
    private function addIdentificator($tagName, $isAppend = false)
    {
        $identificator = $this->generateIdentificator($tagName);

        $paramsAmount = 0;
        switch ($tagName) {
            case 'a' :
                {
                    $paramsAmount = 3;
                    break;
                }
            case 'button' :
                {
                    $paramsAmount = 2;
                }
                $paramsAmount = 1;
        }
        $currentFilePosition = $this->currentPosition+$this->position;
        if ($this->currentAmountOfParams == $paramsAmount) {
            if ($this->currentFile[$currentFilePosition-1] !== '[') {
                $identificator = ', ' . $identificator;
            }
        } else {
            if ($paramsAmount == 3 && $this->currentAmountOfParams == 2) {
                $identificator = ', [' . $identificator . ']';
            } elseif ($paramsAmount == 3 && $this->currentAmountOfParams == 1) {
                $identificator = ', null, [' . $identificator . ']';
            }
        }

        $identificatorLength = strlen($identificator);
        $this->offset += $identificatorLength;
        $this->currentFile =
            substr($this->currentFile, 0, $currentFilePosition) .
            $identificator .
            substr($this->currentFile, $currentFilePosition);
    }

    /**
     * @param $tagName
     *
     * @return string
     */
    private function generateIdentificator($tagName)
    {
        $microtime = str_replace(',', '', microtime());

        return "'data-identificator' => '" . $tagName . '-' . $microtime . "'";
    }
}