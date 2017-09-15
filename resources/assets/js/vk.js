import Vue  from 'vue'
import VueResource  from 'vue-resource'
import { Message,MessageBox,Loading,Alert } from 'element-ui';
import URI from './uri.js';
Vue.use(VueResource);
let vk={
    isProduction:function(){
        return process.env.NODE_ENV === 'production';
    },
    cgi:function(uri){
        var base_url="";
        base_url+='/'+uri.act;
        console.log('isProduction',this.isProduction(),base_url);
        return base_url;
    },
    toast:function(msg,type='error'){
        if(type=='error')
            return Message.error(msg);
        Message(msg);
    },
    then:function(data,uri,callback){
        console.log('vk-then',data,uri.code);
        if(data.code==-1){
            this.toast(data.message);
            sessionStorage.clear();
            window.localStorage.clear();
            location.hash='#/login';
            return;
        }
        if(data.code!=200){
            this.toast(data.message);
            return;
        }
        this.setCache(uri,data);
        if(callback) callback(data,uri.code);
    },
    http:function(uri,data,callback){
        var cdata=this.getCache(uri);
        if(cdata){
            console.log('cacheData',cdata);
            return this.then(cdata,uri,callback);
        }
        var url=this.cgi(uri);
        var that=this;

        var page_params=this.ls(URI.LS_KEY.PAGE_PARAMS);
        if(page_params){
            Object.assign(data,page_params);
        }
        //var headers={};
        //Object.assign(headers,window.axios.defaults.headers.common);
        console.log('postdata',data);
        Vue.http.post(url,data,{emulateJSON: true,headers:window.axios.defaults.headers.common}).then(
            (response) => {
                that.loading(false);
                that.then(response.body,uri,callback);
            },
            (response) => {
                that.loading(false);
                that.then(response.body,uri,callback);
            }
        );
    },
    catchRule(uri){
        var rules={
            10003:{timeout:86400},
            12001:{timeout:86400},
        };
        var line=rules[uri.code];
        if(line){
            line.key=uri.act+'_'+uri.code;
        }
        return line;
    },
    setCache(uri,data){
        var rule=this.catchRule(uri);
        if(rule){
            this.ls(rule.key,data,rule.timeout);
        }
    },
    getCache(uri,callback){
        var rule=this.catchRule(uri);
        if(rule){
            return this.ls(rule.key);
        }
        return false;
    },
    ls:function(key,val=false,timeout=-1){
        var old=window.localStorage.getItem(key);
        var time=new Date().getTime();
        if(old){
            old=JSON.parse(old);
            if(val===false){
                if(old.time>time || old.time==-1){
                    return old.data;
                }
                return "";
            } 
        }
        if(val===false) return "";
        old={time:timeout==-1?-1:(time+timeout*1000),data:val};
        window.localStorage.setItem(key,JSON.stringify(old));
    },
    getArrObj2Arr:function(arr,key){
        var d=[];
        arr.map(function(r,i){
            d.push(r[key])
        })
        return d;
    },
    alert:function(title,confirm){
        MessageBox.alert(title, 'Message', {
            confirmButtonText: 'Ok',
            callback: action => {
                if(confirm) confirm();
            }
        });
    },
    confirm:function(title,confirm,cancel){
        MessageBox.confirm(title, 'Message', {
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel',
            type: 'warning'
        }).then(() => {
            if(confirm) confirm();
        }).catch(() => {
            if(cancel) cancel();
        });
    },
    date(tpl,timespace){
        tpl=tpl || "YYYY年MM月DD日 HH时II分SS秒";
        var d=new Date(timespace*1000);
        tpl=tpl.toUpperCase();
        tpl=tpl.replace('YYYY',d.getFullYear());
        var mm=d.getMonth()+1;
        tpl=tpl.replace('MM',mm>9?mm:'0'+mm);
        var dd=d.getDate();
        tpl=tpl.replace('DD',dd>9?dd:'0'+dd);
        var hh=d.getHours();
        tpl=tpl.replace('HH',hh>9?hh:'0'+hh);
        var ii=d.getMinutes();
        tpl=tpl.replace('II',ii>9?ii:'0'+ii);
        var ss=d.getSeconds();
        tpl=tpl.replace('SS',ss>9?ss:'0'+ss);
        return tpl;
    },
    loading(flag=true){
        var load=Loading.service({ fullscreen: true });
        if(!flag) setTimeout(function(){load.close();},0);
    }
};
export default vk;