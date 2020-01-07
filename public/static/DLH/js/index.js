// var btns = $('.clickbtn');
var sbtns = $('.searchbtn');
var sresults = $('.searchresult');
// let move = 111;

// 查询按钮部分的样式
// $.each(btns,function(i){
//     (function(j){
//         btns[j].onclick = function (){
//             $.each(btns,function(n){
//                 btns[n].classList.remove('clicked');
//             })
//             btns[j].classList.add('clicked');
//             var str = (j*move + 44.5)+'px';
//             $('#triangle').css('left',str);
//             if(j == 0){
//                 text = '请输入企业名称'
//             }else if(j == 1){
//                 text = '请输入老板名称'
//             }else if(j == 2){
//                 text = '请输入分类名称'
//             }else if(j ==3){
//                 text = '请输入活动名称'
//             }
//             $('#inputbox').attr('placeholder',text);
//         }
//     })(i)
// })
// 登录按钮点击
// $('#loginbtn').on('click',function(){
//     $('#shadowbox').removeClass('hidd').addClass('show');
//     $('#loginbox').removeClass('hidd').addClass('show');
//     $('#loginbox').css('animation-name','action_scale').css('animation-duration','.6s').css('animation-timing-function','linear');
// })
// 注册按钮点击
// $('#registbtn').on('click',function(){
//     $('#shadowbox').removeClass('hidd').addClass('show');
//     $('#loginbox').removeClass('hidd').addClass('show');
//     $('#loginbox').css('animation-name','action_scale').css('animation-duration','.6s').css('animation-timing-function','linear');
//     $('#conformpw').css('height','100%').addClass('loginboxlist');
//     $('#underline').css('left','366px');
//     $('#loginboxbtn').html('注册');
// })
// 登录盒子的注册切换
// $('#loginboxtitlelogin').on('click',function(){
//     $('#conformpw').css('height','0').removeClass('loginboxlist');
//     $('#underline').css('left','0');
//     $('#loginboxbtn').html('登录');
// })
// $('#loginboxtitleregist').on('click',function(){
//     $('#conformpw').css('height','100%').addClass('loginboxlist');
//     $('#underline').css('left','366px');
//     $('#loginboxbtn').html('注册');
// })
// 登录盒子关闭按钮

// 登录/注册表单点击
// $('#loginboxbtn').on('click',function(){
//     if($('#loginboxbtn').html() == '登录'){
//         $('#shadowbox').removeClass('show').addClass('hidd');
//         $('#loginbox').removeClass('show').addClass('hidd');
//         $('#loginbtn').removeClass('show').addClass('hidd');
//         $('#registbtn').removeClass('show').addClass('hidd');
//         $('#telbox').removeClass('hidd').addClass('show');
//     }
//     if($('#loginboxbtn').html() == '注册'){
//         $('#conformpw').css('height','0').removeClass('loginboxlist');
//         $('#underline').css('left','0');
//         $('#loginboxbtn').html('登录');
//     }
//     // 成功则清除input
//     $('#username').val('');
//     $('#password').val('');
//     $('#confpassword').val('');
//     $('#recode').val('');
// })
// 已登录状态点击
$('#telbox').on('click',function(){
    $('#logoutbox').toggleClass('hidd').toggleClass('show');
})
// 修改密码点击
$('#changebtn').on('click',function(){
    $('#shadowbox').removeClass('hidd').addClass('show');
    $('#changebox').removeClass('hidd').addClass('show');
    $('#changebox').css('animation-name','action_scale').css('animation-duration','.6s').css('animation-timing-function','linear');
})
// 确认修改密码
$('#confchange').on('click',function(){
    $('#shadowbox').removeClass('show').addClass('hidd');
    $('#changebox').removeClass('show').addClass('hidd');
    $('#logoutbox').removeClass('show').addClass('hidd');
})
// 退出登录成功
$('#logoutbtn').on('click',function(){
    $('#logoutbox').removeClass('show').addClass('hidd');
    $('#telbox').removeClass('show').addClass('hidd');
    $('#loginbtn').removeClass('hidd').addClass('show');
    $('#registbtn').removeClass('hidd').addClass('show');
})
// 高级查询按钮的样式
$.each(sbtns,function(i){
    (function(j){
        sbtns[j].onclick = function (){
            $.each(sresults,function(n){
                sresults[n].classList.remove('show');
                sresults[n].classList.add('hidd');
            })
            $.each(sbtns,function(n){
                sbtns[n].classList.remove('searched');
            })
            sbtns[j].classList.add('searched');
            if(j == 0){
                sresults[0].classList.remove('hidd');
                sresults[0].classList.add('show');
            }else if(j == 1){
                sresults[1].classList.remove('hidd');
                sresults[1].classList.add('show');
            }else if(j == 2){
                sresults[2].classList.remove('hidd');
                sresults[2].classList.add('show');
            }else if(j == 3){
                sresults[3].classList.remove('hidd');
                sresults[3].classList.add('show');
            }else if(j == 4){
                sresults[4].classList.remove('hidd');
                sresults[4].classList.add('show');
            }
        }
    })(i)
})
