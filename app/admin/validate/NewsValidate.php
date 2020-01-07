<?php
namespace app\admin\validate;
use think\Validate;
class NewsValidate extends Validate{
    //验证规则
    protected $rule=[
        'title|标题'=>'unique:News|require|max:10',
        'content'=>'require',
        'author'=>'require'
    ];
    // 验证消息
    protected $message=[
        'title:unique'=>'新闻名称不能重复',
        'title:require'=>'新闻名称不能为空',
        'content:require'=>'内容不能为空',
        'author:require'=>'作者名称不能为空',
    ];
    // 验证场景
    protected $scene=[
        'add'=>['title','content','author'],
        'edit'=>['content'],
    ];
}