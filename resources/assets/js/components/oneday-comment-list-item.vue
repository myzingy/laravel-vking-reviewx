<template>
    <li class="item review-item" itemscope="" itemprop="review" itemtype="http://schema.org/Review">
        <div class="review-information" itemprop="name">
            <div class="review-icon">
                <span>{{getNicknameChar()}}</span>
                <i class="el-icon-check"></i>
            </div>
            <div class="review-info" v-if=" item.type==0 ">
                <div>
                    <span class="nickname">{{item.cont.nickname}}</span>
                    <span class="verified">Verified Buyer</span>
                    <span class="pull-right review-date">
                                <time class="review-details-value" itemprop="datePublished"
                                      datetime="8/15/17">{{getDate('created_at')}}</time>
                            </span>
                </div>
                <div style="clear: right;">
                    <div class="review-ratings">
                        <el-rate v-model="item.score" disabled :colors="['#ffc600','#ffc600','#ffc600']"></el-rate>
                    </div>
                </div>
            </div>
            <div class="review-info" v-else="">
                <div>
                    <span class="nickname">{{item.cont.nickname}}</span>
                    <span class="verified">Verified Reviewer</span>
                    <span class="pull-right review-date">
                                <time class="review-details-value" itemprop="datePublished"
                                      datetime="8/15/17">{{getDate('created_at')}}</time>
                            </span>
                </div>
                <div style="font-size: 14px;clear: right;" class="content-padding-right">
                    <span class="question-q nickname">Q:</span>
                    {{item.cont.review}}
                </div>
            </div>
        </div>
        <template v-if=" item.type==0 ">
            <div v-if=" item.cont.summary!='default' && item.cont.summary!='image' "
                 class="review-title content-padding-right"
                 itemprop="name">
                {{item.cont.summary}}
            </div>
            <div class="review-content content-padding-right" itemprop="description">{{item.cont.review}}</div>
            <div class="review-images" style="padding-left: 50px;">
                <span v-for="(row,key) in item.attr">
                    <a class="review-image-a" @click="openReviewImagesDialog(key)">
                        <img :src="getImageUrl(row.attr_id,100)">
                    </a>
                </span>
            </div>
            <social-sharing :url="getItemShareUrl()"
                            :title="getSummary(true)"
                            :description="item.cont.review"
                            :quote="getQuote()"
                            :twitter-user="item.cont.nickname"
                            :media="getMedia()"
                            v-cloak inline-template
                    ref="social_sharing">
                    <dl class="share-box">
                        <dd>
                            <network network="facebook" id="facebook" >
                                <i class="fa fa-fw fa-facebook" ></i>
                            </network>
                        </dd>
                        <dd>
                            <network network="linkedin" id="linkedin">
                                <i class="fa fa-fw fa-linkedin"></i>
                            </network>
                        </dd>
                        <dd>
                            <network network="pinterest" id="pinterest">
                                <i class="fa fa-fw fa-pinterest"></i>
                            </network>
                        </dd>
                        <dd>
                            <network network="twitter" id="twitter">
                                <i class="fa fa-fw fa-twitter"></i>
                            </network>
                        </dd>
                    </dl>
            </social-sharing>
        </template>
        <template v-else="">
            <div class="review-content" itemprop="description">
                <div :class="getOfficialIcon()" style="float: left;margin-right:4px;"><span></span></div>
                <div style="font-size: 14px;padding: 6px;" v-if=" item.cont.reply ">
                    <div class="pull-right review-date">
                         <time class="review-details-value" itemprop="datePublished" datetime="8/16/17">
                             {{getDate('updated_at')}}
                         </time>
                    </div>
                    <div class="nickname brand">{{brand}}</div>
                    <div class="content-padding-right">
                        <span class="nickname">A:</span>
                        {{item.cont.reply}}
                    </div>
                </div>
            </div>
        </template>
    </li>
</template>
<script>
    import Vue from 'vue'
    import vk from '../vk.js'
    import uri from '../uri.js'
    import SocialSharing from 'vue-social-sharing';
    Vue.use(SocialSharing);
    export default {
        props:['item'],
        data() {
            return {
                getScore:0,
                share:{
                    page_url:"https://",
                },
                brand:"",
                platform:"def",
                isFacebook:true,
            }
        },
        methods: {
            getNicknameChar(){
                return this.item.cont.nickname[0].toUpperCase();
            },
            getSummary(flag){
                var summary= (!this.item.cont.summary
                    || this.item.cont.summary=='default'
                    || this.item.cont.summary=='image'
                )?"":this.item.cont.summary;
                return flag?(summary?summary:this.item.cont.review):summary;
            },
            getQuote(){
                var summary=this.getSummary(false);
                return (summary?(summary+' - '):'')+this.item.cont.review;
            },
            openReviewImagesDialog(index){
                var data=[];
                for(var i in this.item.attr){
                    data.push(this.getImageUrl(this.item.attr[i].attr_id));
                }
                window.parent.postMessage({"oneday":'onedayReviewImg',params:data,index:index},"*");
            },
            getImageUrl(id,size='full'){
                
                if(this.item.cont.review_images && this.item.cont.review_images.length>0){
                    return this.item.cont.review_images[id][size=='full'?'src':'thumb'];
                }
                return vk.cgi("review/image/"+id+"-"
                        +window.axios.defaults.headers.common['X-CSRF-TOKEN']
                        +"-"+size+'.png');
            },
            getDate(datespace){
                return vk.date(this.item[datespace]);
            },
            getOfficialIcon(){
                var brand=this.brand.toLowerCase().replace(/[^-]+-/,'');
                return 'official-icon '+brand;
            },
            getMedia(){
                if(this.item.attr.length>0){
                    for(var i in this.item.attr){
                        return this.getImageUrl(this.item.attr[i].attr_id,'full');
                    }
                }
                return vk.ls('share_img');
            },
            getItemShareUrl(platform='def'){
                var url=this.item.cont.page_url;
                var page_params=vk.ls(uri.LS_KEY.PAGE_PARAMS);
                if(page_params.user_id && page_params.user_id_mask){
                    
                    var param=page_params.target_id+','+page_params.user_id
                        +','+page_params.user_id_mask+','+platform;
                    param=btoa(param).replace('/','_').replace('+','-');
                    url=url.replace(/([\?&])?shxxare=[^&#]+/,'');
                    if(url.indexOf('?')>-1){
                        return url+='&shxxare='+ param;
                    }
                    return url+='?shxxare='+ param;
                }
                return url;
            },
        },
        mounted() {
            this.share.page_url=this.item.cont.page_url;
            //console.log('this.item',this.item);
            this.getScore=parseInt(this.item.score);
            var param=vk.ls(uri.LS_KEY.PAGE_PARAMS);
            this.brand=param.brand;
            var that=this;
            this.$root.$on('social_shares_open', function (network, url) {
                //console.log('popup.window.location.href',that.$refs.social_sharing.popup.window.location);
                setTimeout(function(){
                    try{
                        that.$refs.social_sharing.url=that.getItemShareUrl(network);
                        var share_url=that.$refs.social_sharing.createSharingUrl(network);
                        that.$refs.social_sharing.popup.window.location.href= share_url;
                    }catch (e){}
                },100);
            });
//            this.$root.$on('social_shares_change', function (network, url) {
//                that.isFacebook=true;
//            });
        }
    }
</script>
