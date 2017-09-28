<style>
    .el-upload-list--picture-card .el-upload-list__item,.el-upload--picture-card{
        width:80px;
        height: 80px;
        line-height:80px;
    }
    .el-upload-list--picture-card .el-upload-list__item .el-icon-check{
        vertical-align:top;
    }
    .rating{
        display:inline-block;
        position: relative;
        top:-2px;
        left:5px;
    }
    #oneday-comment-form .el-tabs__header{
        display: none;
    }
</style>
<template>
    <div id="oneday-comment-form">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 total-info" style="margin-bottom: 10px;">
                <el-row class="row-bg" justify="space-between">
                    <el-col :xs="24" :sm="12">
                        <el-row class="row-bg" justify="start" v-if=" total.count>0 ">
                            <div class="total-score-text">TOTAL SCORE</div>
                            <el-rate
                                    v-model="total.score"
                                    disabled
                                    show-text
                                    :colors="['#ffc600','#ffc600','#ffc600']"
                                    text-color="#777"
                                    text-template="{value}">
                            </el-rate>
                        </el-row>
                    </el-col>
                    <el-col :xs="24" :sm="12" style="text-align:right;">
                        <el-row>
                            <el-button @click="handleDisplayForm(0)">
                                <i class="el-icon-edit"></i>
                                &nbsp;&nbsp;LEAVE A REVIEW
                            </el-button>
                            <el-button @click="handleDisplayForm(1)">
                                <i class="el-icon-information"></i>
                                &nbsp;&nbsp;ASK A QUESTION
                            </el-button>
                        </el-row>

                    </el-col>
                </el-row>
            </div>
            <div class="col-md-8 col-md-offset-2" v-show="showOnedayCommentForm">

                <el-form ref="form" :model="form" label-width="20px" :rules="rules" class="oneday-review-form">
                    <el-tabs v-model="form.type" type="card" @tab-click="handleFormTabClick" >
                        <el-tab-pane label="LEAVE A REVIEW" name="0"></el-tab-pane>
                        <el-tab-pane label="ASK A QUESTION" name="1"></el-tab-pane>
                    </el-tabs>
                    <el-form-item label=" " prop="rate" v-show="form.type==0">
                        Rating
                        <el-rate v-model="form.rate" class="rating"
                                 :show-text="true"  text-color="#ffc600"
                                 :texts="['Poor','Fair','Average','Good','Excellent']"
                                 :colors="['#ffc600','#ffc600','#ffc600']"></el-rate>
                    </el-form-item>
                    <el-form-item label=" " prop="nickname">
                        <el-input v-model="form.nickname" placeholder="Nickname" auto-complete="off"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="summary" v-show="form.type==0">
                        <el-input v-model="form.summary" placeholder="Summary of Your Review" auto-complete="off"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="review">
                        <el-input type="textarea" v-model="form.review"
                                  :placeholder=" form.type==0?'Review':'Question'" auto-complete="off"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="email">
                        <el-input v-model="form.email" placeholder="Email"></el-input>
                    </el-form-item>
                    <el-form-item label=" " v-show="form.type==0">
                        <el-upload
                                class="upload-demo"
                                :action="getUploadAction()"
                                :headers="headers"
                                :before-upload="beforeUpload"
                                :on-remove="handleRemove"
                                :on-success="handleSuccess"
                                list-type="picture-card"
                                :file-list="form.fileListTmp"
                                accept="image/*">
                            <i class="el-icon-plus"></i>
                            <div class="el-upload__text">Upload images</div>
                            <div slot="tip" class="el-upload__tip">
                                Allow 5 images to be uploaded; Image size limit 10M.
                            </div>
                        </el-upload>
                    </el-form-item>
                    <el-form-item class="submit">
                        <el-button type="primary" @click="submitForm('form')">Post {{form.type==0?'Review':'Question'}}</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import Element from 'element-ui'
    import vk from '../vk.js'
    import uri from '../uri.js'
    Vue.use(Element)
    import bus from './bus.js';
    export default {
        props:['param'],
        data() {
            var checkNickname = function (rule, value, callback) {
                if (value.length>50) {
                    return callback(new Error('Nickname up to 50 characters.'));
                }
                callback();
            };
            var checkSummary = function(rule, value, callback) {
                if (value.length>100) {
                    return callback(new Error('Summary up to 100 characters.'));
                }
                callback();
            };
            var checkReview = function(rule, value, callback) {
                if (value.length>250) {
                    return callback(new Error('Review up to 250 characters.'));
                }
                callback();
            };
            return {
                fileList:[],
                form:{
                    rate:4,
                    fileList:[],
                    fileListTmp:[],
                    type:"0",
                    nickname:"",
                    summary:"",
                    review:"",
                    email:"",
                },
                rules: {
//                    rate: [
//                        {required: true, message: 'Rating is required.', trigger: 'blur',type: 'number', min:1},
//                    ],
                    nickname: [
                        {required: true, message: 'Nickname is required.', trigger: 'blur'},
                        {validator: checkNickname, trigger: 'blur'}
                    ],
                    summary: [
                        {required: true, message: 'Summary is required.', trigger: 'blur'},
                        {validator: checkSummary, trigger: 'blur'}
                    ],
                    review: [
                        {required: true, message: 'Review is required.', trigger: 'blur'},
                        {validator: checkReview, trigger: 'blur'}
                    ],
                    email:[
                        {type:'email',message: 'Email error.', trigger: 'blur'}
                    ],
                },
                headers:{},
                uploadDisabled:false,
                showOnedayCommentForm:false,
                total:{count:0,score:0.0},
                imgLimit:5,
            }
        },
        methods: {
            then:function(json,code) {
                switch (code) {
                    case uri.submitReview.code:
                        this.form.fileList=[];
                        this.form.fileListTmp=[];
                        this.$refs.form.resetFields();
                        var
                            msg=this.form.type==0?'Thanks For Your Review!\n\nPlease note that your review may take a few days to appear as we collect content.'
                            :'Thanks for you submitting the question, we will reply you as soon as possible!';
                        alert(msg,'success');
                        break;
                    case uri.getTotal.code:
                        if(json.data[0]){
                            var score=0.0;
                            if(json.data[0].score>0){
                                score=Math.fround(json.data[0].score).toFixed(1);
                                console.log(typeof score,score);
                            }
                            this.total={count:json.data[0].count,score:parseFloat(score),qcount:json.data[0].qcount};
                            bus.$emit('showTabTotalNum',this.total);
                        }
                        break;
                }
            },
            setFileList(fileList){
                var d=[];
                fileList.map((v)=>{
                    console.log(v);
                    d.push(v.response.id)
                });
                this.form.fileList=d;
                //console.log('el-upload el-upload--picture-card',document.getElementsByClassName('el-upload el-upload--picture-card')[0])
                if(d.length>=this.imgLimit){
                    this.uploadDisabled=true;
                }else{
                    this.uploadDisabled=false;
                }
                this.uploadPictureCard(!this.uploadDisabled);
            },
            handleRemove(file, fileList) {
                this.setFileList(fileList);
                //this.form.fileList=fileList;
                console.log(this.form.fileList);
            },
            handleSuccess(response, file, fileList) {
                if(typeof response.id=='undefined'){
                    fileList.pop();
                    vk.toast('upload fail,Please Try Again');
                }
                this.setFileList(fileList);
                //this.form.fileList=fileList;
                console.log(this.form.fileList);
            },
            beforeUpload(){
                if(this.uploadDisabled){
                    return false;
                }
                var imgs=document.getElementsByClassName('el-upload-list el-upload-list--picture-card')[0].childNodes;
                if(imgs.length>=this.imgLimit){
                    return false;
                }
                //this.headers['X-CSRF-TOKEN']="vviYXmILePpJdr1EEzDIrRVwjgFzYupRCYrr5Kpb";
                Object.assign(this.headers,window.axios.defaults.headers.common);
                console.log('beforeUpload',arguments);
            },
            uploadPictureCard(show){
                if(show){
                    document.getElementsByClassName('el-upload el-upload--picture-card')[0].style.display='inline-block';
                }else{
                    document.getElementsByClassName('el-upload el-upload--picture-card')[0].style.display='none';
                }
            },
            submitForm(formName){
                console.log(formName,this.fileList);
                this.$refs[formName].validate((valid,error) => {
                    if (valid) {
                        vk.http(uri.submitReview,this.form,this.then)
                    } else {
                        console.log('valid....',valid,error);
                        return false;
                    }
                });
            },
            handleFormTabClick(){
                console.log(arguments);
                if(this.form.type==0){
                    this.rules.summary[0].required=true;
                    this.rules.review[0].message='Review is required.';
                    //this.rules.rate[0].required=true;
                }else{
                    this.rules.summary[0].required=false;
                    this.rules.review[0].message='Question is required.';
                    //this.rules.rate[0].required=false;
                }
                this.setIframeHeight();
            },
            handleDisplayForm(type){
                console.log(arguments);
                this.form.type=type.toString();
                this.showOnedayCommentForm=true;
                this.handleFormTabClick();
            },
            setIframeHeight(){
                setTimeout(function(){
                    var body=document.documentElement.getElementsByTagName('body');
                    //console.log('document.documentElement',body[0].offsetHeight,body[0].scrollHeight);
                    var h = body[0].offsetHeight;
                    window.parent.postMessage({"oneday":true,height:h+100,'from':'form'},"*");
                },500);
            },
            getUploadAction(){
                return vk.cgi('upload');
            }

        },
        mounted() {
            vk.ls(uri.LS_KEY.PAGE_PARAMS,this.param);
            console.log('Component mounted.',this.param);
            if(this.param.user_id){
                this.form.nickname=this.param.user_name;
                this.form.email=this.param.user_email;
            }
            var that=this;
            bus.$on('showOnedayCommentForm',function(type){
                that.handleDisplayForm(type);
            });
            var OnMessage=function(e){
                console.log("OnMessage",e);
                if(typeof e.data.oneday !='undefined'){
                    if(e.data.oneday.act=='write_review'){
                        that.handleDisplayForm(1);
                        return;
                    }
                    if(e.data.oneday.act=='review'){
                        that.handleDisplayForm(0);
                        return;
                    }
                }
            };
            if (window.addEventListener) {  // all browsers except IE before version 9
                window.addEventListener("message", OnMessage, false);
            } else {
                if (window.attachEvent) {   // IE before version 9
                    window.attachEvent("onmessage", OnMessage);
                }
            }
            vk.http(uri.getTotal,this.param,this.then);
        }
    }
</script>
