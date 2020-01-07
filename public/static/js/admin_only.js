/**
 * Created by gaojian on 18-6-13.
 */
var ajaxForm_list = $('form.js-ajax-form-only');

Wind.css('artDialog');
Wind.use('ajaxForm', 'artDialog', 'noty3', 'validate', function () {
    $('button.js-ajax-submit-only').on('click',function (e){
        var btn = $(this), form = btn.parents('form.js-ajax-form-only');
        console.log(btn);
        $btn = btn;
        var msg = btn.data('msg');
        if (msg) {
            art.dialog({
                id: 'warning',
                icon: 'warning',
                content: btn.data('msg'),
                cancelVal: '关闭',
                cancel: function () {
                    // console.log('123');
                    // alert('123');
                    //btn.data('subcheck', false);
                    //btn.click();
                },
                ok: function () {
                    // console.log('2');
                    btn.data('msg', false);
                    btn.click();
                }
            });
            return false;
        }
    });

    ajaxForm_list.each(function () {
        $(this).validate({
            //是否在获取焦点时验证
            //onfocusout : false,
            //是否在敲击键盘时验证
            //onkeyup : false,
            //当鼠标点击时验证
            //onclick : false,
            //给未通过验证的元素加效果,闪烁等
            highlight: function (element, errorClass, validClass) {
                if (element.type === "radio") {
                    this.findByName(element.name).addClass(errorClass).removeClass(validClass);
                } else {
                    var $element = $(element);
                    $element.addClass(errorClass).removeClass(validClass);
                    $element.parent().addClass("has-error");//bootstrap3表单
                    $element.parents('.control-group').addClass("error");//bootstrap2表单

                }
            },
            unhighlight: function (element, errorClass, validClass) {
                if (element.type === "radio") {
                    this.findByName(element.name).removeClass(errorClass).addClass(validClass);
                } else {
                    var $element = $(element);
                    $element.removeClass(errorClass).addClass(validClass);
                    $element.parent().removeClass("has-error");//bootstrap3表单
                    $element.parents('.control-group').removeClass("error");//bootstrap2表单
                }
            },
            showErrors: function (errorMap, errorArr) {
                var i, elements, error;
                for (i = 0; this.errorList[i]; i++) {
                    error = this.errorList[i];
                    if (this.settings.highlight) {
                        this.settings.highlight.call(this, error.element, this.settings.errorClass, this.settings.validClass);
                    }
                    //this.showLabel( error.element, error.message );
                }
                if (this.errorList.length) {
                    //this.toShow = this.toShow.add( this.containers );
                }
                if (this.settings.success) {
                    for (i = 0; this.successList[i]; i++) {
                        //this.showLabel( this.successList[ i ] );
                    }
                }
                if (this.settings.unhighlight) {
                    for (i = 0, elements = this.validElements(); elements[i]; i++) {
                        this.settings.unhighlight.call(this, elements[i], this.settings.errorClass, this.settings.validClass);
                    }
                }
                this.toHide = this.toHide.not(this.toShow);
                this.hideErrors();
                this.addWrapper(this.toShow).show();
            },
            submitHandler: function (form) {
                var $form = $(form);
                // alert('123');
                $form.ajaxSubmit({
                    url: $btn.data('action') ? $btn.data('action') : $form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                    dataType: 'json',
                    beforeSubmit: function (arr, $form, options) {

                        $btn.data("loading", true);
                        var text = $btn.text();

                        //按钮文案、状态修改
                        $btn.text(text + '中...').prop('disabled', true).addClass('disabled');
                    },
                    success: function (data, statusText, xhr, $form) {
                        // window.console.log(data);
                        function _refresh() {
                            if (data.url) {
                                if(data.data.parent == 1){
                                    parent.layer.closeAll();
                                    var framesrc = $(window.parent.document).find("#content iframe:visible");
                                    framesrc[0].contentWindow.location.reload();
                                    return false;
                                }else{
                                    window.location.href = data.url;
                                }
                                //parent.location.href = data.url;

                            } else {
                                if (data.code == 1) {
                                    //刷新当前页
                                    reloadPage(window);
                                }
                            }
                        }

                        var text = $btn.text();

                        //按钮文案、状态修改
                        $btn.removeClass('disabled').prop('disabled', false).text(text.replace('中...', '')).parent().find('span').remove();
                        if (data.code == 1) {
                            if ($btn.data('success')) {
                                var successCallback = $btn.data('success');
                                window[successCallback](data, statusText, xhr, $form);
                                return;
                            }
                            new Noty({
                                text: data.msg,
                                type: 'success',
                                layout: 'topCenter',
                                modal: true,
                                animation: {
                                    open: 'animated bounceInDown', // Animate.css class names
                                    close: 'animated bounceOutUp', // Animate.css class names
                                },
                                timeout: 1,
                                callbacks: {
                                    afterClose: function () {
                                        if ($btn.data('refresh') == undefined || $btn.data('refresh')) {
                                            _refresh();
                                        }
                                    }
                                }
                            }).show();
                            $(window).focus();
                        } else if (data.code == 0) {
                            var $verify_img = $form.find(".verify_img");
                            if ($verify_img.length) {
                                $verify_img.attr("src", $verify_img.attr("src") + "&refresh=" + Math.random());
                            }

                            var $verify_input = $form.find("[name='verify']");
                            $verify_input.val("");

                            //$('<span class="tips_error">' + data.msg + '</span>').appendTo($btn.parent()).fadeIn('fast');
                            $btn.removeProp('disabled').removeClass('disabled');

                            new Noty({
                                text: data.msg,
                                type: 'error',
                                layout: 'topCenter',
                                modal: true,
                                animation: {
                                    open: 'animated bounceInDown', // Animate.css class names
                                    close: 'animated bounceOutUp', // Animate.css class names
                                },
                                timeout: 1,
                                callbacks: {
                                    afterClose: function () {
                                        _refresh();
                                    }
                                }
                            }).show();
                            $(window).focus();
                        }else{
                            art.dialog({
                                title:'请输入验证码',
                                content: $("#codediv").html(),
                                cancelVal: '关闭',
                                cancel: function () {
                                    // reloadPage(window);
                                },
                                ok: function () {
                                    $.ajax({
                                        url:_URL+'/ajaxCheckCode',
                                        dataType: 'json',
                                        type:'post',
                                        data:{
                                            'no_order':data.data.no_order,
                                            // 'code'  :   $("#code").val(),
                                            'code'  :   data.data.confirm_code,
                                            'id':data.data.id
                                        },
                                        success:function (e){
                                            if (e.status == 1){
                                                new Noty({
                                                    text: data.msg,
                                                    type: 'success',
                                                    layout: 'topCenter',
                                                    modal: true,
                                                    animation: {
                                                        open: 'animated bounceInDown', // Animate.css class names
                                                        close: 'animated bounceOutUp', // Animate.css class names
                                                    },
                                                    timeout: 1,
                                                    callbacks: {
                                                        afterClose: function () {
                                                            if ($btn.data('refresh') == undefined || $btn.data('refresh')) {
                                                                _refresh();
                                                            }
                                                        }
                                                    }
                                                }).show();
                                            }else{
                                                art.dialog({
                                                    title:'错误',
                                                    icon: 'error',
                                                    content:e.msg,
                                                    cancelVal: '关闭',
                                                    cancel: function () {
                                                        reloadPage(window);
                                                    }
                                                })
                                            }
                                        }
                                    })
                                    // alert($("#code").val());
                                    // reloadPage(window);
                                }
                            });

                        }

                    },
                    error: function (xhr, e, statusText) {
                        // console.log('3');
                        art.dialog({
                            id: 'warning',
                            icon: 'warning',
                            content: statusText,
                            cancelVal: '关闭',
                            cancel: function () {
                                reloadPage(window);
                            },
                            ok: function () {
                                reloadPage(window);
                            }
                        });

                    },
                    complete: function () {
                        $btn.data("loading", false);
                    }
                });
            }
        });
    });

});
