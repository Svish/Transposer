<?php

/**
 * Builds a map for transposing chords between two different keys.
 *
 * @link https://github.com/Svish/PhpTransposer
 */
class Transposer
{
	public static function parse($song, $key)
	{
		if($song === NULL)
			throw new Exception('Song cannot be NULL.');

		if( ! array_key_exists($key, self::$SCALES))
			throw new Exception('Unknown key: '.$key);

		return new Transposer_Song($song, $key);
	}

	/**
	 * Scales belonging to each key.
	 */
	public static $SCALES = array
		(
			'A' => array('A','B','C♯','D','E','F♯','G♯'),
			'A♯' => array('A♯','B♯','D','D♯','E♯','G','A'), //*
			'B♭' => array('B♭','C','D','E♭','F','G','A'),
			'B' => array('B','C♯','D♯','E','F♯','G♯','A♯'),
			'C♭' => array('C♭','D♭','E♭','F♭','G♭','A♭','B♭'),
			'B♯' => array('B♯','D','E','E♯','G','A','B'), //*
			'C' => array('C','D','E','F','G','A','B'),
			'C♯' => array('C♯','D♯','E♯','F♯','G♯','A♯','B♯'),
			'D♭' => array('D♭','E♭','F','G♭','A♭','B♭','C'),
			'D' => array('D','E','F♯','G','A','B','C♯'),
			'D♯' => array('D♯','E♯','G','G♯','A♯','C','D'), //*
			'E♭' => array('E♭','F','G','A♭','B♭','C','D'),
			'E' => array('E','F♯','G♯','A','B','C♯','D♯'), //*
			'E♯' => array('E♯','G','A','A♯','B♯','D','E'), //*
			'F♭' => array('F♭','G♭','A♭','A','C♭','D♭','E♭'), //*
			'F' => array('F','G','A','B♭','C','D','E'),
			'F♯' => array('F♯','G♯','A♯','B','C♯','D♯','E♯'),
			'G♭' => array('G♭','A♭','B♭','C♭','D♭','E♭','F'),
			'G' => array('G','A','B','C','D','E','F♯'),
			'G♯' => array('G♯','A♯','B♯','C♯','D♯','E♯','G'), //*
			'A♭' => array('A♭','B♭','C','D♭','E♭','F','G'),
		);

	/**
	 * Chords in order, grouped by value.
	 */
	public static $CHORDS = array
		(
			array('A'),
			array('A♯', 'B♭'),
			array('B', 'C♭'),
			array('B♯', 'C'),
			array('C♯', 'D♭'),
			array('D'),
			array('D♯', 'E♭'),
			array('E', 'F♭'),
			array('E♯', 'F'),
			array('F♯', 'G♭'),
			array('G'),
			array('G♯', 'A♭'),
		);


	private $map;

	/**
	 * Creates a new Transposer.
	 *
	 * @param original Key to transpose from.
	 * @param target Key to transpose to.
	 * @throws Exception If any of the keys are not known.
	 */
	public function __construct($original, $target)
	{
		if( ! array_key_exists($original, self::$SCALES))
			throw new Exception('Unknown key: '.$original);

		if( ! array_key_exists($target, self::$SCALES))
			throw new Exception('Unknown key: '.$target);

		// Make a copy of the chords starting with the original
		$old = self::$CHORDS;
		while( ! in_array($original, $old[0]))
			array_push($old, array_shift($old));

		// Make a copy of the chords starting with the target
		$new = $old;
		while( ! in_array($target, $new[0]))
			array_push($new, array_shift($new));

		// For each chord group
		foreach(array_keys($old) as $chord)
		{
			$left = $old[$chord];
			$right = $new[$chord];

			// If single option on the right side
			if(count($right) == 1)
			{
				// Use that for all chords on left
				foreach($left as $x)
					$this->map[$x] = $right[0];

				continue;
			}

			// If single option after removing those not in target scale
			$right_c = array_intersect($right, self::$SCALES[$target]);
			if(count($right_c) == 1)
			{
				// Use that for all chords on left
				$right_c = array_pop($right_c);
				foreach($left as $x)
					$this->map[$x] = $right_c;

				continue;
			}

			// If two options on both sides
			if(count($left) == 2 AND count($right) == 2)
			{
				// Match first with first and second with second
				foreach(array_keys($left) as $x)
					$this->map[$left[$x]] = $right[$x];

				continue;
			}

			// If two options and only one on the left
			if(count($left) == 1 AND count($right) == 2)
			{
				// Calculate distance between original and target chord
				$diff = ord($target) - ord($original);
				if($diff < 0)
					$diff += 7;

				// Pick right chord with same distance to left chord
				$d0 = ord($right[0]) - ord($left[0]);
				if($d0 < 0)
					$d0 += 7;
				$d1 = ord($right[1]) - ord($left[0]);
				if($d1 < 0)
					$d1 += 7;
				$this->map[$left[0]] = $d0 == $diff ? $right[0] : $right[1];

				continue;
			}

			// If we get here, we have an unhandled case.
			// Which we really shouldn't have...
			throw new Exception("Unhandled case.");
		}
	}

	/**
	 * Transposes a chord from the old key to the new key.
	 *
	 * @param chord A chord in the old key.
	 * @return The chord in the new key.
	 */
	public function transpose($chord)
	{
		return $this->map[$chord];
	}

	public function __toString()
	{
		return print_r($this->map, true);
	}
}

class Transposer_Song
{
	private $verses = array();
	private $key;

	public function __construct($song, $key)
	{
		$this->key = $key;

		// Split song into verses
		foreach(preg_split('/(?:\r\n){2,}/', $song) as $verse)
			$this->verses[] = new Transposer_Verse($verse);
	}

	public function transpose($key)
	{
		if($key === NULL OR $key == $this->key)
			return;

		$t = new Transposer($this->key, $key);
		$this->key = $key;

		foreach($this->verses as $verse)
			foreach($verse->lines as $line)
				if( ! is_string($line))
					foreach($line->chords as $chord)
						$chord->chord = $t->transpose($chord->chord);
	}

	public function get_key_selector($url)
	{
		$keys = '';
		foreach(array_keys(Transposer::$SCALES) as $k)
			$keys .= sprintf('<a href="%s"%s>%s</a>',
				$url.urlencode($k),
				$k == $this->key ? ' class="key"' : '',
				$k
				);

		return '<div class="keys">'.$keys.'</div>'.PHP_EOL;
	}

	/**
	 * Returns HTML for a simple key selector.
	 *
	 * @param url URL prefix for key links. For example 'song/5/' or 'song.php?key='
	 */
	public function __toString()
	{
		return '<div class="lyrics">'.PHP_EOL.implode('', $this->verses).'</div>'.PHP_EOL;
	}
}

class Transposer_Verse
{
	public $lines = array();
	public function __construct($verse)
	{
		// Split verse into lines
		foreach(preg_split('%\r\n%', $verse) as $line)
			try
			{
				// Try create a key line
				$this->lines[] = new Transposer_ChordLine($line);
			}
			catch(Exception $e)
			{
				// Otherwise it's just a regular text line
				$this->lines[] = $line;
			}
	}
	public function __toString()
	{
		return '<pre class="verse">'.implode(PHP_EOL,$this->lines).'</pre>'.PHP_EOL;
	}
}

class Transposer_ChordLine
{
	public $chords;
	public function __construct($text)
	{
		// Find all chords
		preg_match_all(Transposer_Chord::$pattern, $text, $this->chords, PREG_SET_ORDER);

		// Create chords and count combined length of found chords
		$len = mb_strlen($text);
		foreach($this->chords as &$k)
		{
			$len -= mb_strlen($k[0]);
			$k = new Transposer_Chord($k);
		}

		// Assume this is not a chord line unless all text was eaten
		if($len > 0)
			throw new Exception('Not a chord line: '.$text);
	}
	public function __toString()
	{
		return implode('', $this->chords);
	}
}
class Transposer_Chord
{
	public static $pattern = '%(\s*+\/?)([A-H][♯♭b\#]?)((?:2|5|6|7|9|11|13|6\/9|7\-5|7\-9|7\#5|7\#9|7\+5|7\+9|7b5|7b9|7sus2|7sus4|add2|add4|add9|aug|dim|dim7|m\/maj7|m6|m7|m7b5|m9|m11|m13|maj7|maj9|maj11|maj13|mb5|m|sus4|sus2|sus)*)(\s*+)%u';

	private $text;
	private $pre;
	public $chord;
	private $fluff;
	public function __construct(array $parts)
	{
		list($this->text, 
			$this->pre, 
			$this->chord,
			$this->fluff) = $parts;

		$this->chord = preg_replace('%b%u', '♭', preg_replace('%#%u', '♯', $this->chord));
	}
	public function __toString()
	{
		$old = $this->text.'<span class="c">'.'</span>';
		$r = $this->pre.'<span class="c">'.$this->chord.$this->fluff.'</span>';
		return str_pad($r, mb_strlen($old, 'UTF-8') + (strlen($r)-mb_strlen($r, 'UTF-8')));
	}
}
