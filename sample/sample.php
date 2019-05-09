<?php

// Include the class (shouldn't be needed if you use Composer)
include('../src/Geekality/Transposer.php');

// Get the lyrics with chords
$text = file_get_contents('sample.txt');

// Parse the text in its original key (in this case, D)
$song = Geekality\Transposer::parse($text, 'D');

// If we have a key to transpose to
if(isset($_GET['key']))
    // Transpose it to that key
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

// Optionally output HTML for a key selector
<?php echo $song->get_key_selector('sample.php?key=') ?>

// Output the transposed song
<?php echo $song ?>

</body>
</html>
