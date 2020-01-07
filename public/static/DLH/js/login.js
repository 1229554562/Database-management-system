// var ls = document.getElementsByTagName("label");
// var isTrue = true;

// var check = document.getElementById('check');
// check.onclick = function(){
//     isTrue = !isTrue;
//     if(isTrue){
//         ls[0].className = "none";
//         ls[1].className = " ";
//     }else{
//         ls[0].className = "checked";
//         ls[1].className = "none";
//     }
// }

var data = Number;
// 0 是登陆  1 是注册

data = window.location.href.split("?")[1].split("=")[1];
show(data);
function show(data){
    if(data == 0){
        document.title = "会员登录";
        $('#registbox').removeClass('show').addClass('hidd');
        $('#loginbox').removeClass('hidd').addClass('show');
    }else if(data == 1){
        document.title = "会员注册";
        $('#loginbox').removeClass('show').addClass('hidd');
        $('#registbox').removeClass('hidd').addClass('show');
    }
}

$("#gotoregist").on('click',function(){
    data = 1;
    show(data);
})
$("#gotologin").on('click',function(){
    data = 0;
    show(data);
})


$("#registbtn").on('click',function(){
    // 如果注册成功，显示登录
    data = 0;
    show(data);
})
$("#loginbtn").on('click',function(){
    // 如果登录成功，显示首页
    window.history.go(-1);
})