/**
 * Created by vking on 2017/9/8.
 */
(function($){
    var base_url='http://laravel.vking/';
    var config={
        dom_id:"oneday_thread",
        appid:"",
        page_id:"",
        page_url:location.href,
        user_id:"",
        user_name:"",
        target_id:"",
        target_sku:"",
        view:"", //def list + form ||  no
    };
    if(typeof oneConfig!='undefined'){
        for(var i in config){
            if(typeof oneConfig[i]!='undefined'){
                config[i]=oneConfig[i];
            }
        }
    }
    if(config.view!='no'){
        var $d=document.getElementById(config.dom_id);
        var src=base_url+'review?';
        src += encodeURIComponent(JSON.stringify(config));
        $d.innerHTML='<iframe id="dsq-app8967" name="dsq-app8967" allowtransparency="true" frameborder="0"' +
            ' scrolling="no" tabindex="0" title="oneday" width="100%" src="'+src+'" horizontalscrolling="no" verticalscrolling="no"></iframe>';
        var OnMessage=function(e){
            console.log("OnMessage",e);
            if(typeof e.data.oneday !='undefined'){
                document.getElementById('dsq-app8967').height=e.data.height;
            }
        };
        if (window.addEventListener) {  // all browsers except IE before version 9
            window.addEventListener("message", OnMessage, false);
        } else {
            if (window.attachEvent) {   // IE before version 9
                window.attachEvent("onmessage", OnMessage);
            }
        }
    }
    if(typeof jQuery=='undefined'){
        //https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js
        console.log('jquery is undefined!!!');
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'oneday-review-jssdk'));

    }
    window.onedayReview=function(fun){
        if(typeof jQuery=='undefined') {
            console.log('jquery on loading...');
            setTimeout(function(){
                onedayReview(fun);
            },500);
            return;
        }
        console.log('jquery on loaded!');
        var url=base_url+"api/getReviewTotal";
        var data={};
        jQuery.ajax({
            url:url,
            data:data,
            jsonp:'callback',
            dataType:'jsonp',
            success:function(json){
                if(fun) fun(json);
            }
        });
    };
})();
