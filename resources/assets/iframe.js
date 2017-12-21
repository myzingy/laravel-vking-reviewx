/**
 * Created by vking on 2017/9/8.
 */
(function(){
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
        view:"", //def list + form || me-list || no
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
                    onedayReviewImg(e.data.params,e.data.index);
                    return;
                }
                if(e.data.oneday=='scrollIntoView'){
                    document.getElementById(config.dom_id).scrollIntoView();
                    var frm = document.getElementById('dsq-app8967');
                    frm.contentWindow.postMessage({oneday:{act:'review'}},"*");
                    return;
                }
                if(e.data.oneday=='alert'){
                    alert(e.data.msg);
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
            //js.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js";
            js.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'oneday-review-jssdk'));
    }

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.href = base_url+"css/viewer.min.css?"+Math.random();
        js.rel='stylesheet';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'link', 'oneday-viewer-css'));
    if(typeof window.hasViewer=='undefined') {
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://cdnjs.cloudflare.com/ajax/libs/viewerjs/0.7.2/viewer.min.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'oneday-viewer-jssdk'));
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
        if(typeof oneConfig!='undefined'){
            for(var i in config){
                if(typeof oneConfig[i]!='undefined'){
                    config[i]=oneConfig[i];
                }
            }
        }
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
    window.onedayReviewImg=function(data,index){
        var $onedayReviewImg='<div id="onedayReviewImg" style="display: none;" ></div>';
        if(jQuery('#onedayReviewImg').length<1){
             jQuery('body').append($onedayReviewImg);
        }else{
            window.onedayReviewImgSDK.destroy();
        }
        var $imgs='';
        for(var i in data){
            if(/^http(s)?:\/\//.test(data[i])){
                $imgs+='<img src="'+data[i]+'" alt="">';
            }
        }
        jQuery('#onedayReviewImg').html($imgs);
        window.onedayReviewImgSDK = new Viewer(document.getElementById('onedayReviewImg'));
        //window.onedayReviewImgSDK.show();
        jQuery('img','#onedayReviewImg').eq(index).trigger('click');
        jQuery('.viewer-container').css({'z-index':999999,'background-color':'rgba(0,0,0,0.8)'});
    }
    //share_img
    window.shareImgIntervalRun=0;
    window.shareImgInterval=setInterval(function(){
        if(window.shareImgIntervalRun>10){
            clearInterval(window.shareImgInterval);
            return;
        }
        var frm = document.getElementById('dsq-app8967');
        var metas=document.getElementsByTagName('meta');
        for(var i in metas){
            var url=metas[i].content;
            if(/^(https:|http:)?\/\/.*\.(jpg|jpeg|png)$/i.test(url)){
                console.log('meta-img',url);
                frm.contentWindow.postMessage({oneday:{act:'share_img','url':url}},"*");
                break;
            }
        }
        window.shareImgIntervalRun++;
    },5000);
})();
