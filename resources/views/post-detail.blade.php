<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-language" content="vi" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$post->seo_name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="description" content="{{$post->seo_content}}" />
    <meta name="keywords" content="{{$post->seo_keywords}}rainichi, Rainichi, japan, online course, japanese online course" />
    <meta name="robots" content="index,follow" />
    <meta name="revisit-after" content="1 days" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{url('')}}/bai-viet/{{$post->slug}}/{{$type}}" />
    <meta property="og:title" content="{{$post->seo_name}}" />
    <meta property="og:description" content="{{$post->seo_content}}" />
    <meta property="fb:app_id" content="2458511104207144" />
    <link rel="shortcut icon" href="https://s3user10122.storebox.vn/files/config/images/favicon.ico" />
    <meta name="generator" content="Rainichi.com" />
    <meta name="copyright" content="Rainichi.com" />
    <meta name="author" content="VietLabo" />
    <meta http-equiv="audience" content="General" />
    <meta name="resource-type" content="Document" />
    <meta name="distribution" content="Global" />
    <meta property="og:image" content="{{$post->image ? media_url($post->image) : ''}}" />
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="315">
</head>
<body>
<script src="{{url('')}}/js/jquery-3.2.1.min.js"></script>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            window.location.href = "https://rainichi.vn/{{$type === "1" ? 'su-kien' : 'viec-lam'}}/detail/{{$post->slug}}"
        }, 100)
    })
</script>
</body>
</html>
