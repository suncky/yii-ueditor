# 介绍
该扩展基于Yii2.0, Choate\ueditor-ext编写而成

# 安装
## 配置config文件路径
首先配置UEditor的配置文件
```php
return [
    'components' => [
        'assetManager' => [
            'assetMap' => [
                'ueditor.config.js' => '@web/js/ueditor.config.js', // 具体的存放路径参照项目定义
            ],
        ],
    ],
];
```

# 使用
```php
$form = ActiveForm::begin();
echo $form->field($model, 'content')->widget(['class' => UEditor::className(), 'ueditorAction' => '后端请求,包括上传等']);
$form->end();
```


