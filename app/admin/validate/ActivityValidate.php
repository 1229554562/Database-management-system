<?php
namespace app\admin\validate;
use think\Validate;
class ActivityValidate extends Validate{
    //验证规则
    protected $rule=[
        'title|标题'=>'unique:Activity|require|max:20',
        'content'=>'require',
    ];
    // 验证消息
    protected $message=[
        'title:unique'=>'标题名称不能重复',
        'title:require'=>'标题名称不能为空',
        'title:max'=>'标题名称长度不能大于10位',
        'content:require'=>'内容不能为空',
    ];
    // 验证场景
    protected $scene=[
        'add'=>['title','content'],
        'edit'=>['content'],
    ];
}