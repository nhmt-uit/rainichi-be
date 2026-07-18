<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rai.Nichi</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
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
                        <h2>Xin chào</h2>
                        <h2><i>(Hello) </i></h2>
                        <p>Bạn vừa được thêm vào lớp {{$className}}.</p>
                        <p><i>(You have just been added to the {{$className}} class.) </i></p>
                        <p>Nếu cần thêm thông tin hoặc hỗ trợ, vui lòng liên hệ với chúng tôi, hoặc truy cập
                            vào: <a href="https://rainichi.com">Rai.Nichi Website</a> để tìm hiểu thêm thông tin khác.
                        </p>
                        <p><i>(Please do not hesitate to contact us if you need a help or you can take a look our website: <a
                                    href="https://rainichi.com">Rai.Nichi Website</a> for more information.)</i></p>
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
