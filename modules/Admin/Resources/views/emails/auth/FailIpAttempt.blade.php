<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        {!! HTML::decode($emailContent['text1']) !!}
        <br />
        {!! HTML::decode($emailContent['text2']) !!}
    </body>
</html>
