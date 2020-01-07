<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Validate;

class VerificationCodeController extends HomeBaseController
{
    public function send()
    {

    
        //TODO: 限制 每个ip 的发送次数
        $data = $this->request->param();
     
        //$data['username']='18562457318';
        $accountType = 'mobile';
         $code = cmf_get_verification_code($data['username']);
        if (empty($code)) {
            $this->error("验证码发送过多,请明天再试!");
        }
       

        if ($accountType == 'mobile') {

            $param  = ['mobile' => $data['username'], 'code' => $code];
            $result = hook_one("send_mobile_verification_code", $param);

            if ($result !== false && !empty($result['error'])) {
                $this->error($result['message']);
            }

            //     

            $expireTime = empty($result['expire_time']) ? 0 : $result['expire_time'];

            cmf_verification_code_log($data['username'], $code, $expireTime);

            if (!empty($result['message'])) {
                $this->success($result['message']);
            } else {
                $this->success('验证码已经发送成功!'.$code);
            }

        }


    }
   
    public function sms(){
       
   
        $ip = get_client_ip();
        $logip = cmf_get_option('logip_setting');

        $data = $this->request->param();

        $captcha = $this->request->param('captcha');
        
        if (empty($captcha)) {
            $this->error(lang('CAPTCHA_REQUIRED'));
        }
        //验证码
//        if (!cmf_captcha_check_sms($captcha)) {
//            $this->error(lang('CAPTCHA_NOT_RIGHT'));
//        }
       // $mobile = Db::name('user')->where('user_login',$data['username'])->value('mobile');
       $mobile =input('post.username'); 
     
        $code = cmf_get_verification_code($mobile);   
        if (empty($code)) {
            $this->error("验证码发送过多,请明天再试!");
        }
        $res = cmf_sendSms($mobile,['code'=>$code]);
        
        if($res['status'] == 88 || $res['status'] == 1){
            //$time=time();
            // $data=[
            //     'mobile'=>$mobile,
            //     'content'=>'【团尚科技】您的验证码是'.$code,
            //     'status'=>'ok',
            //     'sendtime'=>$time,
            // ];
            // db('smslog')->insert($data);
            cmf_verification_code_log($mobile, $code);
           
            $this->success('验证码已经发送成功!'.$code);
//            $this->success('您的验证码是'.$code);
        }else{
            $this->error($res['msg']);
        }

    }


    public function sms_send(){
        $data = $this->request->param();
      
        $captcha = $this->request->param('captcha');
      
        if (empty($captcha)) {
            $this->error(lang('CAPTCHA_REQUIRED'));
        }
        //验证码
        // if (!cmf_captcha_check($captcha)) {
        //     $this->error(lang('CAPTCHA_NOT_RIGHT'));
        // }
        $mobile = db('user')->where('mobile',$data['username'])->value('mobile');
        
       
        $code = cmf_get_verification_code($mobile);
        
        
        if (empty($code)) {
            $this->error("验证码发送过多,请明天再试!");
        }
        $res = cmf_sendSms($mobile,['code'=>$code]);
       
        if($res['status'] == 88 || $res['status'] == 1){
            cmf_verification_code_log($mobile, $code);
       
        
           
            $this->success('验证码已经发送成功!');
//            $this->success('您的验证码是'.$code);
        }else{
            cmf_verification_code_log($mobile, $code);
            $this->error($res['msg']);
        }

    }



}
