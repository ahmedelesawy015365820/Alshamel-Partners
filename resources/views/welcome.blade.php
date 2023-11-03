<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <link id="style_dashboard" href="{{ asset('css/custom.css') }}" rel="stylesheet">

    </head>
    <body class="">
        
        {{-- page vue (single page application) --}}
        <div id="app"></div>
        <!-- script -->
{{--        <script src="http://www.geoplugin.net/javascript.gp" type="text/javascript"></script>--}}
        <script src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
        <script src="{{ mix('js/app-'. env('MIX_VERSION') . '.js') }}"></script>

    </body>
</html>
