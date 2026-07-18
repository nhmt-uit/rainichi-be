<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rainichi - Verify Email</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}css/main.css">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}fonts/font-awesome/css/font-awesome.min.css">
    <style>
        .help-block {
            color: red;
        }

        .payment-status {
            display: flex;
            flex-direction: row;
            justify-content: center;
            margin-top: 20px;
        }

        .payment-status img {
            height: 80px;
            width: 80px;
            text-align: center;
        }

        .payment-content {
            margin-top: 15px;
            justify-content: center;
            text-align: center;
        }

        .payment-content span {
            font-size: 20px;
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
            margin-left: 10px;
        }

        .come-back {
            width: 100%;
            display: flex;
            flex-direction: row;
            margin-top: 20px;
            text-align: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body>


<div class="container-login100" style="background-image: url('{{asset('')}}bg-01.jpg');">
    <div class="wrap-login100 p-l-55 p-r-55 p-t-10 p-b-30">
        <div class="payment-status">
            @if($status)
                <img src="{{url('')}}/success.png"/>
            @else
                <img src="{{url('')}}/cancel.png"/>
            @endif
        </div>
        <div class="payment-content">
            <span>{{$message}}</span>
        </div>
        <div class="come-back">
            <span>
                <a href="{{$return_link}}">Quay lại về Login</a>
            </span>
        </div>
    </div>
</div>

<script src="{{asset('')}}js/jquery-3.2.1.min.js"></script>
<script src="{{asset('')}}js/main.js"></script>
</body>
</html>
