<template>
    <li class="item review-item" itemscope="" itemprop="review" itemtype="http://schema.org/Review">
        <div class="review-information" itemprop="name">
            <div class="review-icon">
                <span>{{getNicknameChar()}}</span>
                <i class="el-icon-check"></i>
            </div>
            <div class="review-info" v-if=" item.type==0 ">
                <div>
                    <span style="font-weight: bold;font-size: 14px;">{{item.cont.nickname}}</span>
                    <span style="font-size: 12px;">Verified Buyer</span>
                    <span class="pull-right review-date">
                                <time class="review-details-value" itemprop="datePublished"
                                      datetime="8/15/17">{{getDate('created_at')}}</time>
                            </span>
                </div>
                <div>
                    <div class="review-ratings">
                        <el-rate v-model="item.score" disabled :colors="['#ffc600','#ffc600','#ffc600']"></el-rate>
                    </div>
                </div>
            </div>
            <div class="review-info" v-else="">
                <div>
                    <span style="font-weight: bold;font-size: 14px;">{{item.cont.nickname}}</span>
                    <span style="font-size: 12px;">Verified Reviewer</span>
                    <span class="pull-right review-date">
                                <time class="review-details-value" itemprop="datePublished"
                                      datetime="8/15/17">{{getDate('created_at')}}</time>
                            </span>
                </div>
                <div style="font-size: 14px;">
                    <span class="question-q" style="font-size: 16px;font-weight: bold;">Q:</span>
                    {{item.cont.review}}
                </div>
            </div>
        </div>
        <template v-if=" item.type==0 ">
            <div class="review-title" itemprop="name">{{item.cont.summary}}</div>
            <div class="review-content" itemprop="description">{{item.cont.review}}</div>
            <div class="review-images" style="padding-left: 50px;">
                <span v-for="(row,key) in item.attr">
                    <a class="review-image-a" @click="openReviewImagesDialog(key)">
                        <img :src="getImageUrl(row.attr_id,100)">
                    </a>
                </span>
            </div>
            <social-sharing :url="share.page_url"
                            v-cloak inline-template>
                    <dl class="share-box">
                        <dd>
                            <network network="facebook" id="facebook">
                                <i class="fa fa-fw fa-facebook"></i>
                            </network>
                        </dd>
                        <dd>
                            <network network="email" id="email">
                                <i class="fa fa-fw fa-envelope"></i>
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
                <span class="pull-right answer-date">
                         <time class="review-details-value" itemprop="datePublished" datetime="8/16/17">
                             {{getDate('updated_at')}}
                         </time>
                    </span>
                <div style="font-size: 14px;padding: 6px;" v-if=" item.cont.reply ">
                    <div style="font-weight: bold;">{{brand}}</div>
                    <div>
                        <span style="font-weight: bold;">A:</span>
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
    var SocialSharing = require('vue-social-sharing');
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
            }
        },
        methods: {
            getNicknameChar(){
                return this.item.cont.nickname[0];
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
        },
        mounted() {
            this.share.page_url=this.item.cont.page_url;
            console.log('this.item.cont.page_url',this.item.cont.page_url);
            this.getScore=parseInt(this.item.score);
            var param=vk.ls(uri.LS_KEY.PAGE_PARAMS);
            this.brand=param.brand;
            if(this.item.cont.review_images && this.item.cont.review_images.length>0){
                this.item.attr=this.item.cont.review_images;
            }
        }
    }
</script>
