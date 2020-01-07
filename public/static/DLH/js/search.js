var btns = $('.clickbtn');
var sbtns = $('.searchbtn');


// 高级查询按钮的样式
$.each(sbtns,function(i){
    (function(j){
        sbtns[j].onclick = function (){
            $.each(sbtns,function(n){
                sbtns[n].classList.remove('searched');
            })
            sbtns[j].classList.add('searched');
            if(j == 0){
                $('#searchresult').html('按行业查询')
            }else if(j == 1){
                $('#searchresult').html('按注册资本查询')
            }else if(j == 2){
                $('#searchresult').html('按行业参加园区活动情况查询')
            }else if(j == 3){
                $('#searchresult').html('按入园情况查询')
            }else if(j == 4){
                $('#searchresult').html('按获得园区支持情况查询')
            }
        }
    })(i)
})
// 企业详情样式