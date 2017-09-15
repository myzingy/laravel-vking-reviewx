<style>
    .items {
        margin: 0;
        padding: 0;
        list-style: none none;
        color: #777;
        font-size: 12px;
    }
    .review-field-rating .control {
        margin-bottom: 48px;
        margin-top: 10px
    }

    .review-list {
        margin-bottom: 30px
    }

    .review-list .block-title strong {
        font-weight: 300;
        line-height: 1.1;
        font-size: 2.6rem;
        margin-top: 2.5rem;
        margin-bottom: 2rem
    }

    .review-item {
        border-bottom: 1px solid #c9c9c9;
        margin: 0;
        padding: 20px 0
    }

    .review-item:after {
        clear: both;
        content: '';
        display: table
    }

    .review-item:last-child {
        border-width: 0
    }

    .review-ratings {
        display: table;
        margin-bottom: 10px;
        max-width: 100%
    }

    .review-author {
        display: inline
    }

    .review-title {
        font-weight: 300;
        line-height: 1.1;
        font-size: 1.8rem;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        margin: 0 0 20px
    }

    .review-content {
        margin-bottom: 20px
    }
    .review-summary-block .review-total-score {
        float: left;
        margin-right: 20px;
        height: 18px;
        line-height: 28px
    }

    .review-summary-block .product-reviews-summary {
        margin: 0
    }

    .review-nav-block .nav-tabs li.active>a {
        font-size: 14px;
        color: #f7799f;
        font-weight: bold;
        border: 0;
        border-bottom: 4px solid #f7799f
    }

    .review-nav-block .nav-tabs li>a {
        font-size: 14px;
        font-weight: bold
    }

    .review-item .review-information {
        height: 50px;
        clear: both
    }

    .review-item .review-icon {
        float: left;
        width: 50px;
        height: 50px;
        position: relative
    }

    .review-item .review-icon>span {
        display: inline-block;
        width: 45px;
        height: 45px;
        background: #f7799f;
        color: #fff;
        text-align: center;
        line-height: 45px;
        border-radius: 50%;
        font-size: 20px
    }

    .review-item .review-icon>i {
        position: absolute;
        top: 28px;
        right: 4px;
        display: inline;
        height: 16px;
        color: #FFF;
        border-radius: 50%;
        background: #26ce91;
        font-size: 10px;
        width: 16px;
        text-align: center;
        line-height: 16px
    }

    .review-item .review-title {
        font-weight: bold;
        font-size: 16px;
        padding-left: 50px;
        color: #716d6d;
        margin: 0
    }

    .review-item .review-content {
        padding-left: 50px
    }

    .review-item .review-date {
        font-size: 12px
    }
    .pull-right {
        float: right!important
    }
    .share-box{padding-left: 50px; cursor: pointer;}
    .share-box dd{display: inline-block;margin-left: 0px;}
    .share-box i{
        font-size: 24px;
        width: 34px;
        line-height:34px;
        display: block;
        background-color:#ff99bf;
        border-radius: 17px;
        color: #dddddd;
        margin: 3px;
    }
    .share-box i:hover,.share-box i.selected{
        background-color:#f7799f;
        color:#fff;
    }
    @media all and (min-width: 640px) {
        .review-item {
            padding: 30px 0
        }

        .review-title {
            margin: 0 0 30px
        }
    }
</style>
<template>
    <li class="item review-item" itemscope="" itemprop="review" itemtype="http://schema.org/Review">
        <div class="review-information" itemprop="name">
            <div class="review-icon">
                <span>{{getNicknameChar()}}</span>
                <i class="el-icon-check"></i>
            </div>
            <div class="review-info">
                <div>
                    <span style="font-weight: bold;font-size: 14px;">{{item.cont.nickname}}</span>
                    <span style="font-size: 12px;">Verified Buyer</span>
                    <span class="pull-right review-date">
                                <time class="review-details-value" itemprop="datePublished"
                                      datetime="8/15/17">{{item.created_at}}</time>
                            </span>
                </div>
                <div>
                    <div class="review-ratings">
                        <el-rate v-model="getScore" disabled></el-rate>
                    </div>
                </div>
            </div>
        </div>
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
                            <i class="fa fa-fw fa-facebook selected"></i>
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
                        <network network="reddit" id="reddit">
                            <i class="fa fa-fw fa-reddit"></i>
                        </network>
                    </dd>
                    <dd>
                        <network network="twitter" id="twitter">
                            <i class="fa fa-fw fa-twitter"></i>
                        </network>
                    </dd>
                    <dd>
                        <network network="vk" id="vk">
                            <i class="fa fa-vk"></i>
                        </network>
                    </dd>
                </dl>
        </social-sharing>
    </li>
</template>

<script>
    import Vue from 'vue'
    var SocialSharing = require('vue-social-sharing');
    Vue.use(SocialSharing);
    export default {
        props:['item'],
        data() {
            return {
                getScore:0,
                share:{
                    page_url:"https://",
                }
            }
        },
        methods: {
            getNicknameChar(){
                return this.item.cont.nickname[0];
            },
            openReviewImagesDialog(index){
                this.$emit('openReviewImagesDialog',this.item,index);
            },
            getImageUrl(id,size='full'){
                return "/review/image/"+id+"-"
                    +window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    +"-"+size+'.png';
            },
        },
        mounted() {
            this.share.page_url=this.item.cont.page_url;
            console.log('this.item.cont.page_url',this.item.cont.page_url);
            this.getScore=parseInt(this.item.score);
        }
    }
</script>
