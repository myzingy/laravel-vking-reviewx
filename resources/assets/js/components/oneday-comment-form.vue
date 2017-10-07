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
    @media all and (max-width: 400px) {
        .el-upload-list--picture-card .el-upload-list__item-actions:hover span {
            width: 0px;
            height: 0px;
            line-height: 1.0;
            overflow: hidden;
        }
    }
</style>
<template>
    <div id="oneday-comment-form">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 total-info" style="margin-bottom: 10px;">
                <el-row class="row-bg" justify="space-between">
                    <el-col :xs="24" :sm="12">
                        <el-row class="row-bg" justify="start">
                            <div class="total-score-text">TOTAL SCORE</div>
                            <el-rate v-if=" total.count>0 "
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
                                <i class="icon-pen"></i> LEAVE A REVIEW
                            </el-button>
                            <el-button @click="handleDisplayForm(1)">
                                <i class="icon-talk"></i> ASK A QUESTION
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
                                accept="image/*"
                                onClick="handleClickUpload(event)">
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
            <div class="col-md-8 col-md-offset-2" v-show="showOnedayQuestionForm">

                <el-form ref="questionForm" :model="questionForm" label-width="20px" :rules="rules" class="oneday-review-form">
                    <el-form-item label=" " prop="nickname">
                        <el-input v-model="questionForm.nickname" placeholder="Nickname" auto-complete="off"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="question">
                        <el-input type="textarea" v-model="questionForm.question"
                                  placeholder="Question" auto-complete="off"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="email">
                        <el-input v-model="questionForm.email" placeholder="Email"></el-input>
                    </el-form-item>
                    <el-form-item class="submit">
                        <el-button type="primary" @click="submitForm('questionForm')">Post Question</el-button>
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
                value=value.trim();
                if (value.length<1) {
                    return callback(new Error('Nickname is required.'));
                }
                if (value.length>50) {
                    return callback(new Error('Nickname up to 50 characters.'));
                }
                callback();
            };
            var checkSummary = function(rule, value, callback) {
                value=value.trim();
                if (value.length<1) {
                    return callback(new Error('Summary is required.'));
                }
                if (value.length>100) {
                    return callback(new Error('Summary up to 100 characters.'));
                }
                callback();
            };
            var checkReview = function(rule, value, callback,label='Review') {
                value=value.trim();
                if(label!='Question') label='Review';
                console.log('checkReview',label);
                if (value.length<1) {
                    return callback(new Error(label.toString()+' is required.'));
                }
                if (value.length>250) {
                    return callback(new Error(label.toString()+' up to 250 characters.'));
                }
                callback();
            };
            var checkQuestion = function(rule, value, callback) {
                return checkReview(rule, value, callback,'Question');
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
                questionForm:{
                    type:"1",
                    nickname:"",
                    review:"",
                    email:"",
                    question:"",
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
                    question: [
                        {required: true, message: 'Question is required.', trigger: 'blur'},
                        {validator: checkQuestion, trigger: 'blur'}
                    ],
                    email:[
                        {type:'email',message: 'Email error.', trigger: 'blur'}
                    ],
                },
                headers:{},
                uploadDisabled:false,
                showOnedayCommentForm:false,
                showOnedayQuestionForm:false,
                total:{count:0,score:0.0},
                imgLimit:5,
            }
        },
        methods: {
            then:function(json,code) {
                switch (code) {
                    case uri.submitReview.code:
                        if(!this.showOnedayQuestionForm){
                            var msg='Thanks For Your Review!\n\nPlease note that your review may take a few days to appear as we collect content.';
                            this.form.fileList=[];
                            this.form.fileListTmp=[];
                            this.$refs.form.resetFields();
                            this.form.email=this.getUserEmail();
                        }else{
                            var msg='Thanks for you submitting the question, we will reply you as soon as possible!';
                            this.$refs.questionForm.resetFields();
                            this.questionForm.email=this.getUserEmail();
                        }
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
                        if('questionForm'==formName){
                            this.questionForm.review=this.questionForm.question;
                            vk.http(uri.submitReview,this.questionForm,this.then)
                        }else{
                            vk.http(uri.submitReview,this.form,this.then)
                        }
                    } else {
                        console.log('valid....',valid,error);
                        return false;
                    }
                });
            },
            handleFormTabClick(){
                console.log(arguments);
                if(!this.showOnedayQuestionForm){
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
//                this.form.type=type.toString();
//                this.form.nickname="";
//                this.form.summary="";
//                this.form.review="";
//                this.form.email=this.getUserEmail();
                if(type==1){
                    this.showOnedayCommentForm=false;
                    this.showOnedayQuestionForm=true;
                }else{
                    this.showOnedayQuestionForm=false;
                    this.showOnedayCommentForm=true;
                }
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
            },
            getUserEmail(){
                if(this.param.user_id){
                    return this.param.user_email;
                }
                return "";
            },
        },
        mounted() {
            vk.ls(uri.LS_KEY.PAGE_PARAMS,this.param);
            console.log('Component mounted.',this.param);

            //this.form.nickname=this.param.user_name;
            this.form.email=this.getUserEmail();
            this.questionForm.email=this.getUserEmail();
            
            var that=this;
            bus.$on('showOnedayCommentForm',function(type){
                that.handleDisplayForm(type);
            });
            var OnMessage=function(e){
                console.log("OnMessage",e);
                if(typeof e.data.oneday !='undefined'){
                    if(e.data.oneday.act=='write_review'){
                        that.handleDisplayForm(0);
                        return;
                    }
                    if(e.data.oneday.act=='review'){
                        //that.handleDisplayForm(0);
                        that.showOnedayCommentForm=false;
                        that.showOnedayQuestionForm=false;
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
            window.handleClickUpload=function(event){
                if(!/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                    return;
                }
                var dom=event.target;
                var del=document.getElementsByClassName('el-upload-list__item-delete');
                if(del.length>0){
                    //console.log('window.handleClickUpload',del);
                    for(var i in del){
                        if(typeof del[i].style=='undefined') continue;
                        del[i].style.width=0;
                        del[i].style.height=0;
                    }
                }
                if(dom.className=='el-upload-list__item-actions'){
                    var del=dom.getElementsByClassName('el-upload-list__item-delete');
                    del[0].style.width='20px';
                    del[0].style.height='20px';
                    //dom.getElementsByClassName('el-icon-delete2');
                }
            };
        }
    }
</script>
