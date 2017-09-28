<template>
    <div>
        <el-row>
            <el-col :xs="24" :sm="24">
                <el-tabs v-model="form.type" @tab-click="handleClickViewType" class="oneday-list-tabs">
                    <el-tab-pane name="0">
                        <span slot="label">Reviews
                            <span v-if=" count.review_num>0 ">
                                ({{count.review_num}})
                            </span>
                        </span>
                    </el-tab-pane>
                    <el-tab-pane name="1">
                        <span slot="label">Questions
                            <span v-if=" count.question_num>0 ">
                                ({{count.question_num}})
                            </span>
                        </span>
                    </el-tab-pane>
                </el-tabs>
            </el-col>
            <el-col :xs="24" :sm="0" class="oneday-list-tabs-select">
                <el-select v-model="form.order" placeholder="With Pictures" @change="handleClickViewOrder">
                    <el-option label="With Pictures" value="is_attr"></el-option>
                    <el-option label="Newest" value=" "></el-option>
                </el-select>
            </el-col>
        </el-row>


        <div class="review-list">
            <template v-if=" total > 0 ">
            <ol class="items review-items"  v-for="(item, key) in list">
                <onedayCommentListItem :item="item"></onedayCommentListItem>
            </ol>
            </template>
            <div v-else="" style="width: 100%; margin: 0 auto; text-align: center;">
                <el-button type="warning" @click="showOnedayCommentForm">{{tipMessage[form.type]}}</el-button>
            </div>
        </div>
        <div class="block">
            <el-pagination
                    layout="prev, pager, next"
                    :total="total" v-show="pager" @current-change="currentChange"
                    :current-page.sync="currentPage">
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
    import onedayCommentListItem from './oneday-comment-list-item.vue'
    import bus from './bus.js';
    export default {
        props:['param'],
        components:{
            onedayCommentListItem,
        },
        data() {
            return {
                form:{
                    order:"is_attr",
                    type:0,
                    offset:0,
                    limit:10,
                },
                currentPage:1,
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
                    case uri.getReviews.code:
                        console.log('uri.getReviews.code',json);
                        if(this.form.offset<this.form.limit){
                            this.total=json.total;
                            if(json.total>this.form.limit){
                                this.pager=true;
                            }else{
                                this.pager=false;
                            }
                        }
                        this.list=json.data;
                        var that=this;
                        that.setIframeHeight();
                        break;
                }
            },
            handleClickViewOrder(){
                this.form.offset=0;
                this.currentPage=1;
                this.getData();
            },
            handleClickViewType(){
                this.form.offset=0;
                this.currentPage=1;
                this.getData();
            },
            getData(){
                vk.http(uri.getReviews,this.form,this.then)
            },
            getImageUrl(id,size='full'){
                return vk.cgi()+"review/image/"+id+"-"
                    +window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    +"-"+size+'.png';
            },
            setIframeHeight(){
                setTimeout(function(){
                    var body=document.documentElement.getElementsByTagName('body');
                    //console.log('document.documentElement',body[0].offsetHeight,body[0].scrollHeight);
                    var h = body[0].offsetHeight;
                    console.log('OnMessage',body[0]);
                    window.parent.postMessage({"oneday":true,height:h+100,'from':'list'},"*");
                },500);
            },
            currentChange(currentPage){
                this.form.offset=(currentPage-1)*this.form.limit
                this.getData();
            },
            showOnedayCommentForm(){
                //document.getElementById('#oneday-comment-form');
                bus.$emit('showOnedayCommentForm',this.form.type);
            },
            showTabTotalNum(data){
                console.log('data....',data);
                this.count={
                    question_num:data.qcount,
                    review_num:data.count
                };
            }
        },
        mounted() {
            vk.ls(uri.LS_KEY.PAGE_PARAMS,this.param);
            this.getData();
            var that=this;
            bus.$on('showTabTotalNum',function(data){
                console.log('showTabTotalNum....',data);
                that.showTabTotalNum(data);
            });
            setInterval(function(){
                that.setIframeHeight();
            },3000);
        }
    }
</script>
