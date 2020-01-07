var $taskContentInner = null;
var $mainIframe       = null;
var tabwidth          = 118;
var $loading          = null;
var $navWraper        = $("#nav-wrapper");
var $content;
$(function () {
    $mainIframe      = $("#mainiframe");
    $content         = $("#content");
    $loading         = $("#loading");
    var headerHeight = 48;
    $content.height($(window).height() - headerHeight);

    $navWraper.height($(window).height() - 48 - 40);
    $(window).resize(function () {
        $navWraper.height($(window).height() - 48 - 40);
        $content.height($(window).height() - headerHeight);
        calcTaskContentWidth();
    });

    $("#content iframe").load(function () {
        $loading.hide();
    });

    $taskContentInner = $("#task-content-inner");

    $taskContentInner.on("click", "li", function () {
        openapp($(this).attr("app-url"), $(this).attr("app-id"), $(this).attr("app-name"));
        return false;
    });

    $taskContentInner.on("dblclick", "li", function () {
        closeapp($(this));
        return false;

    });
    $taskContentInner.on("click", ".cmf-component-tabclose", function () {
        closeapp($(this).parent());
        return false;
    });

    $("#task-next").click(function () {
        var marginLeft   = $taskContentInner.css("margin-left");
        marginLeft       = marginLeft.replace("px", "");
        var contentInner = $("#task-content-inner").width();
        var contentWidth = $("#task-content").width();
        var lessWidth    = contentWidth - contentInner;
        marginLeft       = marginLeft - tabwidth <= lessWidth ? lessWidth : marginLeft - tabwidth;

        $taskContentInner.stop();
        $taskContentInner.animate({"margin-left": marginLeft + "px"}, 300, 'swing');
    });

    $("#task-pre").click(function () {
        var marginLeft = $taskContentInner.css("margin-left");
        marginLeft     = parseInt(marginLeft.replace("px", ""));
        marginLeft     = marginLeft + tabwidth > 0 ? 0 : marginLeft + tabwidth;
        // $taskContentInner.css("margin-left", marginLeft + "px");
        $taskContentInner.stop();
        $taskContentInner.animate({"margin-left": marginLeft + "px"}, 300, 'swing');
    });

    $("#refresh-wrapper").click(function () {
        var $currentIframe = $("#content iframe:visible");
        $loading.show();
        //$currentIframe.attr("src",$currentIframe.attr("src"));
        $currentIframe[0].contentWindow.location.reload();
        return false;
    });


    calcTaskContentWidth();
});
function refresh_page(){
    var $currentIframe = $("#content iframe:visible");
    $loading.show();
    //$currentIframe.attr("src",$currentIframe.attr("src"));
    $currentIframe[0].contentWindow.location.reload();
    return false;
}
function calcTaskContentWidth() {
    var width = $taskContentInner.width();
    if (($(document).width() - 268 - tabwidth - 30 * 2) < width) {
        $("#task-content").width($(document).width() - 268 - tabwidth - 30 * 2);
        $("#task-next,#task-pre").show();
    } else {
        $("#task-next,#task-pre").hide();
        $("#task-content").width(width);
    }
}

function close_current_app() {
    closeapp($("#task-content-inner .active"));
}

function closeapp($this) {
    if (!$this.is(".noclose")) {
        $this.prev().click();
        $this.remove();
        $("#appiframe-" + $this.attr("app-id")).remove();
        calcTaskContentWidth();
        $("#task-next").click();
    }

}


var task_item_tpl = '<li class="cmf-component-tabitem">' +
    '<a class="cmf-tabs-item-text"></a>' +
    '<span class="cmf-component-tabclose" href="javascript:void(0)" title="点击关闭标签"><span></span><b class="cmf-component-tabclose-icon">×</b></span>' +
    '</li>';

var appiframe_tpl = '<iframe style="width:100%;height: 100%;" frameborder="0" class="appiframe"></iframe>';

function parentopenapp(url, appId, appname, refresh){
    var $app = $("#task-content-inner li[app-id='" + appId + "']",window.parent.document);
    $("#task-content-inner .active",window.parent.document).removeClass("active");
    if ($app.length == 0) {
        var task = $(task_item_tpl).attr("app-id", appId).attr("app-url", url).attr("app-name", appname).addClass("active");
        task.find(".cmf-tabs-item-text").html(appname).attr("title", appname);
        $("#task-content-inner",window.parent.document).append(task);
        $(".appiframe",window.parent.document).hide();
        $("#loading",window.parent.document).show();
        var iframe = $(appiframe_tpl).attr("src", url).attr("id", "appiframe-" + appId);
        $("#content",window.parent.document).append(iframe);
        var $iframe = $("#appiframe-" + appId,window.parent.document);
        $iframe.load(function () {
            var srcLoaded = $iframe.get(0).contentWindow.location.href;
            if (srcLoaded.indexOf('admin/public/login') >= 0) {
                window.location.reload(true);
            }
            $("#loading",window.parent.document).hide();
        });
        var width = $("#task-content-inner",window.parent.document).width();
        if (($(document).width() - 268 - tabwidth - 30 * 2) < width) {
            $("#task-content",window.parent.document).width($(document).width() - 268 - tabwidth - 30 * 2);
            $("#task-next,#task-pre",window.parent.document).show();
        } else {
            $("#task-next,#task-pre",window.parent.document).hide();
            $("#task-content",window.parent.document).width(width);
        }
    } else {
        $app.addClass("active");
        $(".appiframe",window.parent.document).hide();
        var $iframe = $("#appiframe-" + appId,window.parent.document);
        var src     = $iframe.get(0).contentWindow.location.href;
        src         = src.substr(src.indexOf("://") + 3);
        if (refresh === true) {//刷新
            $("#loading",window.parent.document).show();
            $iframe.attr("src", url);
            $iframe.load(function () {
                var srcLoaded = $iframe.get(0).contentWindow.location.href;
                if (srcLoaded.indexOf('admin/public/login') >= 0) {
                    window.location.reload(true);
                }
                $("#loading",window.parent.document).hide();
            });
        }
        $iframe.show();
    }

    var taskContentInner = $("#task-content-inner",window.parent.document).width();
    var contentWidth     = $("#task-content",window.parent.document).width();
    if (taskContentInner <= contentWidth) { //如果没有开始滚动就不用进行下去了
        return;
    }

    var currentTabIndex = $("#task-content-inner li[app-id='" + appId + "']",window.parent.document).index();
    var itemOffset      = 0;
    var currentTabWidth = $("#task-content-inner li[app-id='" + appId + "']",window.parent.document).width();

    $("#task-content-inner li:lt(" + currentTabIndex + ')',window.parent.document).each(function () {
        itemOffset = itemOffset + $(this).width();
    });

    var cssMarginLeft = $taskContentInner.css("margin-left");

    cssMarginLeft = parseInt(cssMarginLeft.replace("px", ""));


    var marginLeft = currentTabWidth + itemOffset - contentWidth + cssMarginLeft;

    if (marginLeft > 0) {
        marginLeft = -(currentTabWidth + itemOffset - contentWidth);
        $taskContentInner.animate({"margin-left": marginLeft + "px"}, 300, 'swing');
        return;
    }

    if (itemOffset + cssMarginLeft < 0) {
        marginLeft = -itemOffset
        $taskContentInner.animate({"margin-left": marginLeft + "px"}, 300, 'swing');

        return;
    }

}


function openapp(url, appId, appname, refresh) {
    var $app = $("#task-content-inner li[app-id='" + appId + "']");
    $("#task-content-inner .active").removeClass("active");
    if ($app.length == 0) {
        var task = $(task_item_tpl).attr("app-id", appId).attr("app-url", url).attr("app-name", appname).addClass("active");
        task.find(".cmf-tabs-item-text").html(appname).attr("title", appname);
        $taskContentInner.append(task);
        $(".appiframe").hide();
        $loading.show();
        $appiframe = $(appiframe_tpl).attr("src", url).attr("id", "appiframe-" + appId);
        $appiframe.appendTo("#content");
        $appiframe.load(function () {
            var srcLoaded = $appiframe.get(0).contentWindow.location.href;
            if (srcLoaded.indexOf('admin/public/login') >= 0) {
                window.location.reload(true);
            }
            $loading.hide();
        });
        calcTaskContentWidth();
        if (screen.width <= 425){
           $(".navbar-toggle").addClass("collapsed").attr("aria-expanded",false);
           $("#sidebar").removeClass("in").attr("aria-expanded",false);
        }
    } else {
        $app.addClass("active");
        $(".appiframe").hide();
        var $iframe = $("#appiframe-" + appId);
        var src     = $iframe.get(0).contentWindow.location.href;
        src         = src.substr(src.indexOf("://") + 3);
        if (refresh === true) {//刷新
            $loading.show();
            $iframe.attr("src", url);
            $iframe.load(function () {
                var srcLoaded = $iframe.get(0).contentWindow.location.href;
                if (srcLoaded.indexOf('admin/public/login') >= 0) {
                    window.location.reload(true);
                }
                $loading.hide();
            });
        }
        $iframe.show();
    }

    var taskContentInner = $("#task-content-inner").width();
    var contentWidth     = $("#task-content").width();
    if (taskContentInner <= contentWidth) { //如果没有开始滚动就不用进行下去了
        return;
    }

    var currentTabIndex = $("#task-content-inner li[app-id='" + appId + "']").index();
    var itemOffset      = 0;
    var currentTabWidth = $("#task-content-inner li[app-id='" + appId + "']").width();

    $("#task-content-inner li:lt(" + currentTabIndex + ')').each(function () {
        itemOffset = itemOffset + $(this).width();
    });

    var cssMarginLeft = $taskContentInner.css("margin-left");

    cssMarginLeft = parseInt(cssMarginLeft.replace("px", ""));


    var marginLeft = currentTabWidth + itemOffset - contentWidth + cssMarginLeft;

    if (marginLeft > 0) {
        marginLeft = -(currentTabWidth + itemOffset - contentWidth);
        $taskContentInner.animate({"margin-left": marginLeft + "px"}, 300, 'swing');
        return;
    }

    if (itemOffset + cssMarginLeft < 0) {
        marginLeft = -itemOffset
        $taskContentInner.animate({"margin-left": marginLeft + "px"}, 300, 'swing');

        return;
    }


}

