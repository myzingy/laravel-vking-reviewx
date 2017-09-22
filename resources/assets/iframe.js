/**
 * Created by vking on 2017/9/8.
 */
(function($){
    var base_url='https://review.bizseas.com/';
    if(!(process.env.NODE_ENV === 'production')){
        base_url="https://laravel.vking/";
    }
    var config={
        dom_id:"oneday_thread",
        appid:"",
        page_id:"",
        page_url:location.href,
        page_title:document.getElementsByTagName('title')[0].innerText,
        user_id:"",
        user_id_mask:"",
        user_name:"",
        user_email:"",
        target_id:"",
        target_sku:"",
        target_ids:"",//获取多条记录
        view:"", //def list + form ||  no
    };
    if(typeof oneConfig!='undefined'){
        for(var i in config){
            if(typeof oneConfig[i]!='undefined'){
                config[i]=oneConfig[i];
            }
        }
    }
    var urlencode=function(obj){
        if(typeof obj!="string"){
            obj=JSON.stringify(obj);
        }
        var str=btoa(obj);
        return str.replace('/','_').replace('+','-');
    }
    if(config.view!='no'){
        var $d=document.getElementById(config.dom_id);
        var src=base_url+'review/index/';
        src += urlencode(config);//encodeURI(JSON.stringify(config));
        $d.innerHTML='<iframe referrerpolicy="origin" id="dsq-app8967" name="dsq-app8967" allowtransparency="true"' +
            ' frameborder="0"' +
            ' scrolling="no" tabindex="0" title="oneday" width="100%" src="'+src+'" horizontalscrolling="no" verticalscrolling="no"></iframe>';
        var OnMessage=function(e){
            console.log("OnMessage",e);
            if(typeof e.data.oneday !='undefined'){
                if(e.data.oneday=='onedayReviewImg'){
                    onedayReviewImg(e.data.params);
                    return;
                }
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
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.href = "https://cdnjs.cloudflare.com/ajax/libs/viewerjs/0.7.2/viewer.min.css";
        js.rel='stylesheet';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'link', 'oneday-viewer-css'));
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "https://cdnjs.cloudflare.com/ajax/libs/viewerjs/0.7.2/viewer.min.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'oneday-viewer-jssdk'));
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
        var data=config;
        jQuery.ajax({
            url:url,
            data:data,
            jsonp:'callback',
            dataType:'jsonp',
            success:function(json){
                if(fun && json.code==200) fun(json);
            }
        });
        //bind event
        jQuery('.oneday-review .rating-links').find('a').click(function(){
            document.getElementById(config.dom_id).scrollIntoView();
            var frm = document.getElementById('dsq-app8967');
            if(jQuery(this).attr('ga-click-event')=='write_review'){
                frm.contentWindow.postMessage({oneday:{act:'write_review'}},"*");
            }else{
                frm.contentWindow.postMessage({oneday:{act:'review'}},"*");
            }
            return false;
        });
    };
    window.onedayReviewImg=function(data){
        var $onedayReviewImg='<div id="onedayReviewImg" style="display: none;" ></div>';
        if(jQuery('#onedayReviewImg').length<1){
             jQuery('body').append($onedayReviewImg);
        }else{
            window.onedayReviewImgSDK.destroy();
        }
        var $imgs='';
        for(var i in data){
            $imgs+='<img src="'+data[i]+'" alt="">';
        }
        jQuery('#onedayReviewImg').html($imgs);
        window.onedayReviewImgSDK = new Viewer(document.getElementById('onedayReviewImg'));
        window.onedayReviewImgSDK.show();
        jQuery('.viewer-container').css({'z-index':999999,'background-color':'rgba(0,0,0,0.8)'});
    }
})();
