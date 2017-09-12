<template>
    <div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                list
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
            window.parent.postMessage({"oneday":true,height:500},"*");
        }
    }
</script>
