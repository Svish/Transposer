Introduction
===

For transposing simple songs written as plain text with chords above the lyrics.

Example usage
---

    // song.php
    $text = <<<SONG
    D                      G          D
    Be thou my vision, oh Lord of my heart
    A                          G              A
    Naught be all else to me, save that thou art
    G             D/F♯       Bm         G  A
    Thou my best thought, by day or by night
    Bm          F♯m            G     A    D
    Waking or sleeping, thy presence my light

    D                        G       D
    High King of heaven, my victory won
    A                          G              A
    May I reach heaven's joys, O bright heaven's sun
    G             D/F♯      Bm       G   A
    Heart of my own heart, whatever befall
    Bm          F♯m        G    A   D
    Still be my vision, O ruler of all
    SONG;

    $song = Transposer::parse($text, 'D');
    if(isset($_GET['key']))
    	$song->transpose($_GET['key']);
    
    echo $song->get_key_selector('song.php?key=');
    echo $song;


License
===

This work is licensed under the Creative Commons Attribution 3.0 Unported License. To view a copy of this license, visit [Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/).

![Creative Commons License](http://i.creativecommons.org/l/by/3.0/88x31.png)
