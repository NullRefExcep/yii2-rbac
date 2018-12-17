<?php

namespace nullref\rbac\services;

use Yii;

class ViewWriterService
{
    const ELEMENT_CLASS     = 'ElementHtml';
    const ELEMENT_PATTERN   = 'nullref\\\rbac\\\helpers\\\element\\\ElementHtml';
    const ELEMENT_PATTERN_2 = '\\\nullref\\\rbac\\\helpers\\\element\\\ElementHtml';

    public function writeIdentificator($alias)
    {
        $dirPath = Yii::getAlias($alias);
        $realPath = realpath($dirPath);
        $dirViews = $this->readDir($realPath);

        $files = [];
        foreach ($dirViews as $view) {
            $currentFile = file_get_contents($view['filePath']);
            $isRegularUse = false;
            $isRegularAliasUse = false;
            $aliasUse = '';
            //If regular use
            $matchR = [];
            $pattern = '/[\s]*use[\s]+' . self::ELEMENT_PATTERN . ';/';
            if (preg_match($pattern, $currentFile, $matchR)) {
                if (isset($matchR[0])) {
                    $isRegularUse = true;
                }
            }
            //If regular with alias
            $matchRA = [];
            $pattern = '/[\s]*use[\s]+' . self::ELEMENT_PATTERN . ' as[\s]*(.*?)[\s]*\;/';
            if (preg_match($pattern, $currentFile, $matchRA)) {
                if (isset($matchRA[1])) {
                    $isRegularAliasUse = true;
                    $aliasUse = $matchRA[1];
                }
            }
            //Regular match
            if ($isRegularUse) {
                $rMatch = [];
                $pattern = '/(?<!\\\)(?=[\s]*)' . self::ELEMENT_CLASS . '\:\:/';
                if (preg_match_all($pattern, $currentFile, $rMatch, PREG_OFFSET_CAPTURE)) {
                    if (isset($rMatch[0])) {
                        $pattern = self::ELEMENT_CLASS;
                        $currentFile = $this->processTag($currentFile, $rMatch[0], $pattern);
                    }
                }
            }
            //Regular alias match
            if ($isRegularAliasUse) {
                $rAMacth = [];
                $pattern = '/(?<!\\\)(?=[\s]*)' . $aliasUse . '\:\:/';
                if (preg_match_all($pattern, $currentFile, $rAMacth, PREG_OFFSET_CAPTURE)) {
                    if (isset($rAMacth[0])) {
                        $pattern = $aliasUse;
                        $currentFile = $this->processTag($currentFile, $rAMacth[0], $pattern);
                    }
                }
            }
            //Namespace match
            $nMatch = [];
            $pattern = '/' . self::ELEMENT_PATTERN_2 . '\:\:/';
            if (preg_match_all($pattern, $currentFile, $nMatch, PREG_OFFSET_CAPTURE)) {
                if (isset($nMatch[0])) {
                    $pattern = self::ELEMENT_PATTERN_2;
                    $currentFile = $this->processTag($currentFile, $nMatch[0], $pattern);
                }
            }
            $files[] = [
                'filePath' => $view['filePath'],
                'content'  => $currentFile,
            ];
        }

        return $files;
    }

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

    private function processTag($currentFile, $matches, $pattern)
    {
        $offset = 0;
        foreach ($matches as $match) {
            $currentPosition = $match[1] + $offset;
            $length = strlen($currentFile);
            $subSrting = substr($currentFile, $currentPosition, $length);
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
                            //Find ends on ')
                            $optionPattern = "/(?<=(\'))(\))/";
                            $position = false;
                            if (preg_match($optionPattern, $fullTag, $endOfOption, PREG_OFFSET_CAPTURE)) {
                                if (isset($endOfOption[2])) {
                                    $position = $endOfOption[2][1];
                                }
                            }
                            //Find arrays
                            $optionPattern = "/(?<=\[)([A-Za-z,=> \[\]'\"]*)(?=\])?";
                            //Find ends on ])
                            $optionPattern = '/\,[\s]*\[(.*?)(\][\s]*\))/';
                            $position = false;
                            if (preg_match($optionPattern, $fullTag, $endOfOption, PREG_OFFSET_CAPTURE)) {
                                if (isset($endOfOption[2])) {
                                    $position = $endOfOption[2][1];
                                }
                            }

                            if ($position !== false) {
                                $identificator = $this->generateIdentificator($tagName);
                                $identificatorLength = strlen($identificator);
                                $offset += $identificatorLength;
                                $currentFile =
                                    substr($currentFile, 0, $currentPosition + $position) .
                                    $identificator .
                                    substr($currentFile, $currentPosition + $position);
                            }
                        }
                    }
                }
            }
        }

        return $currentFile;
    }

    private function addIdentificator() {
        $endOfOption = [];
        //Find ends on ')
        $optionPattern = "/(?<=(\'))\)/";
        $position = false;
        if (preg_match($optionPattern, $fullTag, $endOfOption, PREG_OFFSET_CAPTURE)) {
            if (isset($endOfOption[2])) {
                $position = $endOfOption[2][1];
            }
        }
        //Find ends on ])
        $optionPattern = '/\,[\s]*\[(.*?)(\][\s]*\))/';
        $position = false;
        if (preg_match($optionPattern, $fullTag, $endOfOption, PREG_OFFSET_CAPTURE)) {
            if (isset($endOfOption[2])) {
                $position = $endOfOption[2][1];
            }
        }

        if ($position !== false) {
            $identificator = $this->generateIdentificator($tagName);
            $identificatorLength = strlen($identificator);
            $offset += $identificatorLength;
            $currentFile =
                substr($currentFile, 0, $currentPosition + $position) .
                $identificator .
                substr($currentFile, $currentPosition + $position);
        }

        return 1;
    }

    private function generateIdentificator($tagName)
    {
        $microtime = str_replace(',', '', microtime());

        return ", 'data-identificator' => '" . $tagName . '-' . $microtime . "'";
    }
}