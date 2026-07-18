<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="background: #caceca; font-family: 'Nunito', sans-serif">
<div style="max-width: 500px; margin: 0 auto; background-color: #fff; padding: 20px; text-align: center;">
    <h4 style="color: #17a2b8">XIN CHÀO</h4>
    <h4 style="color: #17a2b8"><i>(HELLO)</i></h4>
    <p style="text-align: justify">Chúng tôi nhận được yêu cầu thay đổi mật khẩu từ
        bạn. Vui lòng bấm Xác nhận bên dưới:</p>
    <p style="text-align: justify"><i>(We received a request to change your password. Please click Confirm below:)</i>
    </p>
    <a style="background-color: #17a2b8; color: #fff; padding: 10px; border-radius: 2px; display: inline-block; text-decoration: none"
       href="{{url('/password/find/'.$token)}}">XÁC
        NHẬN <br/> <i>(Confirm)</i></a>

    <br>
    <hr/>
    <h4><i>Rai.Nichi</i></h4>
    <i style="font-size: 12px; color: #00011159">Email tự động, được gửi từ hệ thống, vui lòng không trả lời.</i>
    <i style="font-size: 12px; color: #00011159">(This is automation email. Please do not reply on it.)</i>
</div>
</body>
</html>
