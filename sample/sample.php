<?php

include('../src/Geekality/Transposer.php');

$text = file_get_contents('sample.txt');

$song = Geekality\Transposer::parse($text, 'D');
if(isset($_GET['key']))
    $song->transpose($_GET['key']);

?>
<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        .lyrics 
        {
            counter-reset: verse;
        }
        pre:before
        {
            display: block;
            font-weight: bold;
            font-style: italic;
            content: attr(class);
        }

        pre.verse:before
        {
            counter-increment: verse;
            content: counter(verse);
        }

        span.c
        {
            color: red;
        }

        .keys li
        {
            display: inline-block;
        }
        .keys a
        {
            padding: .5em;
            font-size: small;
            text-decoration: none;
            color: gray;
        }
        .keys a.original
        {
            color: blue;
        }
        .keys a.key
        {
            color: red;
        }
    </style>
</head>
<body>

<?php echo $song->get_key_selector('sample.php?key=') ?>

<?php echo $song ?>

</body>
</html>
