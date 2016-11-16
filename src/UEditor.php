<?php

namespace suncky\yii\widgets\ueditor;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class UEditor
 * @package suncky\yii\ueditor
 */
class UEditor extends InputWidget
{
    public $ueditorAction = 'site/ueditor';

    public $clientOptions = [];

    public function init() {
        parent::init();
        $this->clientOptions['serverUrl'] = Url::to($this->ueditorAction);
    }

    public function run() {
        $this->registerClientScript();
        if ($this->hasModel()) {
            $input = Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::textarea($this->name, $this->value, $this->options);
        }

        echo $input;
    }

    protected function registerClientScript() {
        $id = $this->options['id'];
        $clientOptions = Json::encode($this->clientOptions);
        $view = $this->getView();
        $bundle = UEditorAsset::register($view);
        $view->registerJs("window.UEDITOR_HOME_URL = '{$bundle->baseUrl}/'", View::POS_HEAD);
        $view->registerJs("UE.getEditor('{$id}', {$clientOptions})");
    }
}