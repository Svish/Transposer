<?php

include('../src/Geekality/Transposer.php');

// song.php
$text = <<<SONG
D       G     D
This is first verse

(chorus)
G    Bm   A
This is a chorus

D       G      D
This is second verse

(chorus)
G    Bm   A
This is a chorus
SONG;

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