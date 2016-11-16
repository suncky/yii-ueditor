<?php

namespace suncky\yii\widgets\ueditor;
use yii\web\AssetBundle;

/**
 * Class UEditorParseAsset
 * @package suncky\yii\widgets\ueditor
 */
class UEditorParseAsset extends AssetBundle
{
    public $sourcePath = '@bower/ueditor-ext/dist';
    public $js = [
        'ueditor.parse.min.js',
    ];
}