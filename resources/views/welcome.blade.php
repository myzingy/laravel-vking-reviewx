<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                
                margin: 0;
            }

            .full-height {

            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
                width:90%;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}" onclick="onedayReview">Register</a>
                    @endif
                </div>
            @endif

            <div class="content">
                <div id="oneday_thread"></div>
            </div>
        </div>

    </body>
    @verbatim
    <script>
        var oneConfig={
            dom_id:"oneday_thread",
            appid:"appid",
            page_id:"",
            page_url:location.href+'/jjj?a=b&b=c#masd',
            user_id:"",
            user_name:"",
            view:"",
        };
        (function() { // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            s.src = 'http://laravel.vking/js/iframe.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
            ////
            window.useOnedayReview=function(){
                console.log("useOnedayReview...");
                if(typeof onedayReview=='undefined'){
                    console.log("useOnedayReview...");
                    setTimeout("useOnedayReview()",500);
                    return;
                }
                onedayReview(function(){
                    console.log("..........");
                });
            };
            useOnedayReview();
        })();
    </script>
    @endverbatim
</html>
