<html>
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-size: 100%;
            font-family: 'Avenir Next', "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            line-height: 1.65;
        }

        img {
            max-width: 100%;
            margin: 0 auto;
            display: block;
        }

        body, .body-wrap {
            width: 100% !important;
            height: 100%;
            background: #f8f8f8;
        }

        a {
            color: #71bc37;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .button {
            display: inline-block;
            color: white;
            background: #f37021;
            border: solid #f37021;
            border-width: 10px 20px 8px;
            font-weight: bold;
            border-radius: 4px;
        }

        .button:hover {
            text-decoration: none;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 20px;
            line-height: 1.25;
        }

        h1 {
            font-size: 32px;
        }

        h2 {
            font-size: 28px;
        }

        h3 {
            font-size: 24px;
        }

        h4 {
            font-size: 20px;
        }

        h5 {
            font-size: 16px;
        }

        p, ul, ol {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .container {
            display: block !important;
            clear: both !important;
            margin: 0 auto !important;
            max-width: 580px !important;
        }

        .container table {
            width: 100% !important;
            border-collapse: collapse;
        }

        .container .masthead {
            padding: 80px 0;
            background: #f37021;
            color: white;
        }

        .container .masthead h1 {
            margin: 0 auto !important;
            max-width: 90%;
            text-transform: uppercase;
        }

        .container .content {
            background: white;
            padding: 30px 35px;
        }

        .container .content.footer {
            background: none;
        }

        .container .content.footer p {
            margin-bottom: 0;
            color: #888;
            text-align: center;
            font-size: 14px;
        }

        .container .content.footer a {
            color: #888;
            text-decoration: none;
            font-weight: bold;
        }

        .container .content.footer a:hover {
            text-decoration: underline;
        }

        .table-content {
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .table-content td {
            border: 1px solid #f37021;
            overflow: hidden;
        }
    </style>
</head>
<body>
<table class="body-wrap">
    <tr>
        <td class="container">
            <!-- Message start -->
            <table>
                <tr>
                    <td align="center" class="masthead">
                        <h1>RAI.NICHI</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <h2>Xin chào, </h2>
                        <h2><i>(Hello,) </i></h2>
                        <p>Yêu cầu mua khóa học <b>{{$course->name}}</b> của bạn cho lớp <b>{{$classroom->name}}</b> đã
                            được chấp nhận.</p>
                        <p><i>(Your request to purchase course <b>{{$course->name}}</b> for <b>{{$classroom->name}} class</b> has been accepted.)</i></p>
                        <p>Vui lòng đăng nhập vào <a href="{{env('CMS_LOGIN_LINK')}}">Trang quản trị</a> để kích hoạt
                            lớp học. Nếu cần thêm thông tin hoặc hỗ trợ, vui lòng liên hệ với chúng tôi, hoặc truy cập
                            vào: <a
                                href="https://rainichi.com">Rai.Nichi Website</a> để tìm hiểu thêm thông tin khác.</p>
                        <p><i>(Please login to <a href="{{env('CMS_LOGIN_LINK')}}">Admin page</a> to activate your class. If you need further information or assistance, please contact us, or visit: <a
                                    href="https://rainichi.com">Rai.Nichi Website</a>)</i></p>
                        <p><em>– Rai.Nichi.</em></p>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td class="container">
            <!-- Message start -->
            @include('mail.partials.footer')
        </td>
    </tr>
</table>
</body>
</html>
