<!DOCTYPE html>
<html>
<head>
    <title>{{ $campaign->subject }}</title>
</head>
<body>
    {!! $campaign->template->html_content !!}
</body>
</html>