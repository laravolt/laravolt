<!DOCTYPE html>
<html>
<head>
    <title></title>

    <meta charset="UTF-8"/>
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    @stack('meta')
    @stack('style')
    @stack('head')

</head>

<body>

@yield('content')

@stack('script')
@stack('body')

</body>
</html>
