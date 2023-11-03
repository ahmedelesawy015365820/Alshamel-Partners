<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Pdf</title>
</head>

<body style=" font-family: Helvetica, Arial, sans-serif;">
    <div style=" width: 100%; height: 100%;">
        <div style=" width: 100%;">
            @foreach($data_type_value as $field )
            <div style="width: 50%;
            float: left;
            margin-bottom: 10px;
            display: inline-block;">
                <label style=" margin-bottom: 0.2rem !important;
                color: #343a40 !important;
                font-weight: 600;
                font-size: 18px;
                font-family: Helvetica, Arial, sans-serif;
                display: inline-block;">
                    {{$field->name_e}}
                </label>
                <input readonly value="{{is_object($field->value)?$field->value->name_e:$field->value}}" type="text" data-create="9" step="0.1" style="
                    margin: 0;
                    font-family: inherit;
                    background-color: #f1f5f7;
                    opacity: 1;
                    display: block;
                    width: 90%;
                    height: 500px;
                    padding: 25px;
                    font-size: 16px;
                    font-weight: 400;
                    line-height: 1.5;
                    color: #6c757d;
                    background-clip: padding-box;
                    border: 1px solid #ced4da;
                    border-radius: 10px;
                " />
            </div>
            @endforeach

        </div>
        <div style=" width: 100%;">
            @if(isset($media)&&count($media) >0)
            @foreach($media as $photo )
            @if($photo->mime_type!="application/pdf")
            <div style="margin: 20px 0 0 0;">
                <img src="{{$photo->original_url}}" width="100%" height="100%" />
            </div>
            @endif
            @endforeach
            @endif
        </div>
    </div>

</body>

</html>