<?php

class Format {
	// Basado en wpautop
	public static function autop($pee = '') {
		$pre_tags = array();

		if ( trim($pee) === '' )
			return '';
	
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
	
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		// Space things out a little
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines

		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		// make paragraphs, including one at the end
		$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
		$pee = '';
		foreach ( $pees as $tinkle ) {
			$pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
		}

		$pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace

		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

		return $pee;
	}
}