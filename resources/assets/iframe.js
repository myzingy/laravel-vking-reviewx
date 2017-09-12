/**
 * Created by vking on 2017/9/8.
 */
(function(){
    var config={
        dom_id:"oneday_thread",
        appid:"",
        page_id:"",
        page_url:location.href,
        user_id:"",
        user_name:"",
        view:"", //def list + form+
    };
    if(typeof oneConfig!='undefined'){
        for(var i in config){
            if(typeof oneConfig[i]!='undefined'){
                config[i]=oneConfig[i];
            }
        }
    }
    var $d=document.getElementById(config.dom_id);
    var src='http://laravel.vking/iframe?';
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
})();
