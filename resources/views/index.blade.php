<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config("app.name", "Laravel") }}</title>
    <link rel="icon" href="{{ config('app.url') }}/favicon.ico">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
    @font-face {
        font-family: 'Noto Sans KR';
        font-style: normal;
        font-weight: 300;
        src: url('{{ config('app.url') }}/fonts/NotoSansKR-Light.woff2') format('woff2'),
            url('{{ config('app.url') }}/fonts/NotoSansKR-Light.woff') format('woff'),
            url('{{ config('app.url') }}/fonts/NotoSansKR-Light.otf') format('opentype');
    }
    /*
    @font-face {
    font-family: 'D2 coding';
    font-style: normal;
    font-weight: 400;
    src: url('D2Coding.eot');
    src: local('※'), local('D2Coding'),
        url('{{ config('app.url') }}/fonts/D2Coding.eot?#iefix') format('embedded-opentype'),
        url('{{ config('app.url') }}/fonts/D2Coding.woff2') format('x-woff2'),
        url('{{ config('app.url') }}/fonts/D2Coding.woff') format('woff'),
        url('{{ config('app.url') }}/fonts/D2Coding.ttf') format('truetype'),
        url('{{ config('app.url') }}/fonts/D2Coding.svg') format('svg');
    }
    */
    </style>
</head>
<body>
    <div id="app"></div>
    <script>
    const SUB_URL = "{{ config('app.url') }}";
    </script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>