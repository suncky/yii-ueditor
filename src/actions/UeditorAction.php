<?php
/**
 * 邢帅教育
 * 本源代码由邢帅教育及其作者共同所有，未经版权持有者的事先书面授权，
 * 不得使用、复制、修改、合并、发布、分发和/或销售本源代码的副本。
 *
 * @copyright Copyright (c) 2016. xsteach.com all rights reserved.
 */

namespace suncky\yii\widgets\ueditor\actions;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use suncky\yii\widgets\webuploader\actions\AttachmentUploaderAction;
use suncky\yii\widgets\webuploader\actions\ImageUploaderAction;
use yii\base\Action;
use yii\base\InvalidRouteException;
use yii\web\UploadedFile;

/**
 * Class UeditorAction
 *
 * @author Choate Yao <choate.yao@gmail.com>
 * @package xst\web\workbench\components\actions
 */
class UeditorAction extends Action
{
    private $_configs;

    public function run($action) {
        if (method_exists($this, $action)) {
            return call_user_func_array([$this, $action], $_GET);
        } else {
            throw new InvalidRouteException();
        }
    }

    protected function config() {
        return Json::encode($this->loadConfig());
    }

    protected function attach() {
        /**
         * @var AttachmentUploaderAction $action
         */
        $action = $this->controller->createAction('attachment');
        $action->attachmentAllowFiles = $this->getConfig('fileAllowFiles');
        $action->attachmentMaxSize = $this->getConfig('fileMaxSize');
        $action->attachmentFieldName = $this->getConfig('fileFieldName');
        $uploadedFile = UploadedFile::getInstanceByName($action->attachmentFieldName);
        $result = Json::decode($action->run());
        $result['state'] = ArrayHelper::remove($result, 'status', '未知错误');
        $result['original'] = $uploadedFile->name;

        return Json::encode($result);
    }

    protected function image() {
        /**
         * @var ImageUploaderAction $action
         */
        $action = $this->controller->createAction('image');
        $action->imageAllowFiles = $this->getConfig('imageAllowFiles');
        $action->imageMaxSize = $this->getConfig('imageMaxSize');
        $action->imageFieldName = $this->getConfig('imageFieldName');
        $result = Json::decode($action->run());
        $result['state'] = ArrayHelper::remove($result, 'status', '未知错误');

        return Json::encode($result);
    }

    protected function loadConfig() {
        if ($this->_configs === null) {
            $this->_configs = include \Yii::getAlias('@app/configs/ueditor.php');
        }

        return $this->_configs;
    }

    protected function getConfig($name) {
        return ArrayHelper::getValue($this->loadConfig(), $name, null);
    }
}