<?php

namespace suncky\yii\widgets\ueditor;
use yii\web\AssetBundle;

/**
 * Class UEditorAsset
 * @package suncky\yii\widgets
 */
class UEditorAsset extends AssetBundle
{
    public $sourcePath = '@bower/ueditor-ext/dist';
    public $js = [
        'ueditor.config.js',
        'ueditor.all.min.js',
    ];
}