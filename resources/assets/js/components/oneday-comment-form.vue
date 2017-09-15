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
</style>
<template>
    <div>
        <div class="row" style=" ">
            <div class="col-md-8 col-md-offset-2">

                <el-form ref="form" :model="form" label-width="20px" :rules="rules">
                    <el-tabs v-model="form.type" type="card" @tab-click="handleFormTabClick">
                        <el-tab-pane label="LEAVE A REVIEW" name="0"></el-tab-pane>
                        <el-tab-pane label="ASK A QUESTION" name="1"></el-tab-pane>
                    </el-tabs>
                    <el-form-item label=" " prop="rate" v-show="form.type==0">
                        Rating <el-rate v-model="form.rate" class="rating"></el-rate>
                    </el-form-item>
                    <el-form-item label=" " prop="nickname">
                        <el-input v-model="form.nickname" placeholder="Nickname"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="summary" v-show="form.type==0">
                        <el-input v-model="form.summary" placeholder="Summary of Your Review"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="review">
                        <el-input type="textarea" v-model="form.review" placeholder="Review"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="email">
                        <el-input v-model="form.email" placeholder="Email"></el-input>
                    </el-form-item>
                    <el-form-item label=" " v-show="form.type==0">
                        <el-upload
                                class="upload-demo"
                                action="/upload"
                                :headers="headers"
                                :before-upload="beforeUpload"
                                :on-remove="handleRemove"
                                :on-success="handleSuccess"
                                list-type="picture-card"
                                :disabled="uploadDisabled">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">Allow 5 images to be uploaded.</div>
                        </el-upload>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submitForm('form')">Post Review</el-button>
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
    export default {
        props:['param'],
        data() {
            return {
                form:{
                    rate:4,
                    fileList:[],
                    type:0,
                },
                rules: {
                    nickname: [
                        {required: true, message: 'Nickname is required.', trigger: 'blur'},
                    ],
                    summary: [
                        {required: true, message: 'Summary is required.', trigger: 'blur'},
                    ],
                    review: [
                        {required: true, message: 'Review is required.', trigger: 'blur'},
                    ],
                },
                headers:{},
                uploadDisabled:false,
            }
        },
        methods: {
            then:function(json,code) {
                switch (code) {
                    case uri.submitReview.code:
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
                if(d.length>4){
                    this.uploadDisabled=true;
                }else{
                    this.uploadDisabled=false;
                }
            },
            handleRemove(file, fileList) {
                this.setFileList(fileList);
                //this.form.fileList=fileList;
                console.log(this.form.fileList);
            },
            handleSuccess(response, file, fileList) {
                if(typeof response.id=='undefined'){
                    fileList.pop();
                    vk.alert('upload fail,Please Try Again');
                }
                this.setFileList(fileList);
                //this.form.fileList=fileList;
                console.log(this.form.fileList);
            },
            beforeUpload(){
                //this.headers['X-CSRF-TOKEN']="vviYXmILePpJdr1EEzDIrRVwjgFzYupRCYrr5Kpb";
                Object.assign(this.headers,window.axios.defaults.headers.common);
                console.log('beforeUpload',arguments);
            },
            submitForm(formName){
                console.log(this.fileList);
                this.$refs[formName].validate((valid,error) => {
                    if (valid) {
                        vk.http(uri.submitReview,this.form,this.then)
                    } else {
                        console.log(valid,error);
                        return false;
                    }
                });
            },
            handleFormTabClick(){
                console.log(arguments);
                if(this.form.type==0){
                    this.rules.summary[0].required=true;
                }else{
                    this.rules.summary[0].required=false;
                }
            },

        },
        mounted() {
            vk.ls(uri.LS_KEY.PAGE_PARAMS,this.param);
            console.log('Component mounted.',this.param);
        }
    }
</script>
