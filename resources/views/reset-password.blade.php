<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}css/main.css">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}fonts/font-awesome/css/font-awesome.min.css">
    <style>
        .help-block {
            color: red;
        }
    </style>
</head>
<body>


<div class="container-login100" style="background-image: url('{{asset('')}}bg-01.jpg');">
    <div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
        @if(session()->has('success'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('success') }}
                <a href="http://rainichi.com" class="alert-link">Đăng nhập ngay</a>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('error') }}
            </div>
        @endif
        @if(!session()->has('success'))
            <form class="login100-form validate-form" method="POST" action="{{url('password/reset')}}">
				<span class="login100-form-title p-b-37">
					Reset password
				</span>
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="token" value="{{$token}}">
                <div class="wrap-input100 validate-input m-b-20" data-validate="Không để trống password">
                    <input class="input100" type="password" name="password" placeholder="Password">
                    <span class="focus-input100"></span>

                </div>
                {!! $errors->first('password', '<p class="help-block">:message</p>') !!}

                <div class="wrap-input100 validate-input m-b-25" data-validate="Nhập lại password">
                    <input class="input100" type="password" name="password_confirmation"
                           placeholder="Nhập lại password">
                    <span class="focus-input100"></span>

                </div>
                {!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn">
                        Cập nhật
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script src="{{asset('')}}js/jquery-3.2.1.min.js"></script>
<script src="{{asset('')}}js/main.js"></script>
<script src="{{asset('')}}js/jquery.disableAutoFill.min.js"></script>
<script>
    $('.input100').disableAutoFill();
</script>
</body>
</html>
