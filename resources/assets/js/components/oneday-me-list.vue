<style>
    .review-card{margin-bottom: 8px;}
    .review-card .el-card__body{padding: 5px 10px;}
    .review-card .h3{ font-weight: bold;}
</style>
<template>
    <div>
        <div class="review-list">
            <template v-if=" total > 0 ">
                <el-card class="review-card" v-for="row in list" key="key">
                    <el-row>
                        <el-col :xs="24" :sm="10"  >
                            <div>
                                <span class="h3">Prodcut</span>
                                <div class="bottom clearfix">
                                    <a :href="row.cont.page_url" target="_top">{{row.cont.page_title}}</a>
                                </div>
                            </div>
                        </el-col>
                        <el-col :xs="24" :sm="10"  >
                            <div>
                                <span class="h3">Content</span>
                                <div v-if=" row.type==1 ">
                                    Q:{{row.cont.review}}
                                    <div class="reply" v-if="row.cont.reply">
                                        A:{{row.cont.reply}}
                                    </div>
                                </div>
                                <div v-else="">
                                    <el-rate
                                            v-model="row.score"
                                            disabled
                                            show-text
                                            text-color="#ff9900"
                                            text-template="{value}">
                                    </el-rate>
                                    Summary:{{row.cont.summary}}<br>
                                    Review:{{row.cont.review}}
                                </div>
                            </div>
                        </el-col>
                        <el-col :xs="24" :sm="4"  >
                            <div>
                                <span class="h3">Date</span>
                                <div class="bottom clearfix">
                                    <time class="time">{{ getTdDate(row) }}</time>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </el-card>
            </template>
            <div v-else="" style="width: 100%; margin: 0 auto; text-align: center;">
                You have submitted no reviews.
            </div>
        </div>
        <div class="block">
            <el-pagination
                    layout="prev, pager, next"
                    :total="total" v-show="pager" @current-change="currentChange">
            </el-pagination>
        </div>
    </div>

</template>
<script>
    import Vue from 'vue'
    import Element from 'element-ui'
    import vk from '../vk.js'
    import uri from '../uri.js'
    Vue.use(Element)
    export default {
        props:['param'],
        data() {
            return {
                form:{
                    order:"is_attr",
                    type:0,
                    offset:0,
                    limit:10,
                },
                total:0,
                list:[],
                pager:false,
                tipMessage:[
                    'BE THE FIRST TO WRITE A REVIEW',
                    'BE THE FIRST TO ASK A QUETION'
                ],
                count:{
                    question_num:0,
                    review_num:0
                }
            }
        },
        methods: {
            then:function(json,code) {
                switch (code) {
                    case uri.getMyReviews.code:
                        console.log('uri.getMyReviews.code',json);
                        if(this.form.offset<this.form.limit){
                            this.total=json.total;
                            if(json.total>this.form.limit){
                                this.pager=true;
                            }else{
                                this.pager=false;
                            }
                        }
                        this.list=json.data;
                        this.setIframeHeight();
                        break;
                }
            },
            getTdDate(row){
                return vk.date(row.created_at);
            },
            getData(){
                this.form.appid=this.param.appid;
                this.form.user_id=this.param.user_id;
                this.form.user_id_mask=this.param.user_id_mask;
                vk.http(uri.getMyReviews,this.form,this.then)
            },
            setIframeHeight(){
                setTimeout(function(){
                    var body=document.documentElement.getElementsByTagName('body');
                    //console.log('document.documentElement',body[0].offsetHeight,body[0].scrollHeight);
                    var h = body[0].offsetHeight;
                    window.parent.postMessage({"oneday":true,height:h+100},"*");
                },500);
            },
            currentChange(currentPage){
                this.form.offset=(currentPage-1)*this.form.limit
                this.getData();
            },
            
        },
        mounted() {
            this.getData();
        }
    }
</script>
