<template>
    <div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component</div>

                    <div class="panel-body">
                        I'm an example component!
                        <br>{{data.name}}
                        <br>{{data.sex}}
                        <br>{{data.age}}
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        props:['data'],
        data() {
            return {
                username: 'vking',
            }
        },
        methods: {
            onClickUsername(){
                console.log('onClickUsername onClickUsername.');
            },
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
