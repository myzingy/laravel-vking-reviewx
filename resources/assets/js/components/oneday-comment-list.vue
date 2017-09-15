<style>
    .el-dialog__wrapper.review-images-dialog{
        height:500px;
    }
    .review-images-dialog .el-dialog{
        position: static;
    }
</style>

<template>
    <div>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
        <el-select v-model="form.order" placeholder="With Pictures" style="float: right;z-index:99;width:130px;" @change="handleClickViewOrder">
            <el-option label="With Pictures" value="is_attr"></el-option>
            <el-option label="Newest" value=" "></el-option>
        </el-select>
        <el-tabs v-model="form.type" @tab-click="handleClickViewType">
            <el-tab-pane label="Reviews" name="0"> </el-tab-pane>
            <el-tab-pane label="Questions" name="1"> </el-tab-pane>
        </el-tabs>
        <div class="review-list">
            <ol class="items review-items"  v-for="(item, key) in list">
                <onedayCommentFormItem :item="item" @openReviewImagesDialog="openReviewImagesDialog"></onedayCommentFormItem>
            </ol>
        </div>
        <div class="block">
            <el-pagination
                    layout="prev, pager, next"
                    :total="total">
            </el-pagination>
        </div>
        <el-dialog class="review-images-dialog" title="Review Images" :visible.sync="reviewImagesDialogVisible"
                   size="full" top="100%" modal-append-to-body="false">
            <!--
            <el-carousel :interval="5000" arrow="always">
                <el-carousel-item v-for="(row,key) in reviewImages">
                    <img :src="getImageUrl(row.attr_id)">
                </el-carousel-item>
            </el-carousel>
            -->
            ........
        </el-dialog>
    </div>

</template>
<script>
    import Vue from 'vue'
    import Element from 'element-ui'
    import vk from '../vk.js'
    import uri from '../uri.js'
    Vue.use(Element)
    import onedayCommentFormItem from './oneday-comment-form-item.vue'
    export default {
        props:['param'],
        components:{
            onedayCommentFormItem,
        },
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
                reviewImagesDialogVisible:false,
                reviewImages:[],
            }
        },
        methods: {
            then:function(json,code) {
                switch (code) {
                    case uri.getReviews.code:
                        console.log('uri.getReviews.code',json);
                        if(this.offset<this.limit){
                            this.total=json.total;
                        }
                        this.list=json.data;
                        break;
                }
            },
            handleClickViewOrder(){
                this.getData();
                //console.log(arguments);
            },
            handleClickViewType(){
                this.getData();
            },
            getData(){
                vk.http(uri.getReviews,this.form,this.then)
            },
            openReviewImagesDialog(item,index){
                console.log(arguments);
                this.reviewImagesDialogVisible=true;
                this.reviewImages=item;
            },
            getImageUrl(id,size='full'){
                return "/review/image/"+id+"-"
                    +window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    +"-"+size+'.png';
            },
        },
        mounted() {
            vk.ls(uri.LS_KEY.PAGE_PARAMS,this.param);
            this.getData();
            ///
            window.parent.postMessage({"oneday":true,height:1800},"*");
        }
    }
</script>
