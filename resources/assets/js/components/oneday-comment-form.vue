<template>
    <div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <el-form ref="form" :model="form" label-width="20px" :rules="rules">
                    <el-form-item label=" " prop="rate">
                        Rating<el-rate v-model="form.rate"></el-rate>
                    </el-form-item>
                    <el-form-item label=" " prop="nickname">
                        <el-input v-model="form.nickname" placeholder="Nickname"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="summary">
                        <el-input v-model="form.summary" placeholder="Summary of Your Review"></el-input>
                    </el-form-item>
                    <el-form-item label=" " prop="review">
                        <el-input type="textarea" v-model="form.review" placeholder="Review"></el-input>
                    </el-form-item>
                    <el-form-item label="">
                        <el-upload
                                class="upload-demo"
                                action="/upload"
                                :headers="headers"
                                :on-preview="handlePreview"
                                :on-remove="handleRemove"
                                :before-upload="beforeUpload"
                                :file-list="fileList">
                            <el-button size="small" type="info">Upload Review Image <i class="el-icon-upload el-icon--right"></i></el-button>
                        </el-upload>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submitForm('numberValidateForm')">提交</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>

</template>

<script>
    import Vue from 'vue'
    import Element from 'element-ui'
    Vue.use(Element)
    export default {
        props:[],
        data() {
            return {
                form:{
                    rate:4
                },
                rules: {
                    nickname: [
                        {required: true, message: 'Nickname is required.', trigger: 'blur'},
                    ],
                    summary: [
                        {required: true, message: 'Summary of your review is required.', trigger: 'blur'},
                    ],
                    review: [
                        {required: true, message: 'Review is required.', trigger: 'blur'},
                    ],
                },
                fileList:[],
                headers:{},
            }
        },
        methods: {
            handlePreview(){
                console.log('onClickUsername onClickUsername.');
            },
            handleRemove(){},
            beforeUpload(){
                this.headers['X-CSRF-TOKEN']="vviYXmILePpJdr1EEzDIrRVwjgFzYupRCYrr5Kpb";
                console.log('beforeUpload',arguments);
            }

        },
        mounted() {
            console.log('Component mounted.');
            var that=this;
            setTimeout(function(){
                that.username='vking.wang';
            },5000);
        }
    }
</script>
