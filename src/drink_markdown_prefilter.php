<?php
class DrinkMarkdownPrefilter {

	function filter($raw,$transformer){
		$transformer->replaces = array();

		// We only accept LF (\n) as line endings
		$raw = EasyReplace($raw,array(
			"\r\n" => "\n",
			"\n\r" => "\n",
		));

		$raw = "\n$raw\n";
		
		$replaces = array();
		$uniqid = uniqid();

		// Source codes wrapped in ```...```
		preg_match_all('/[\n\r]```([ a-z0-9]*)\n(.*?)\n```\s*\n/s',$raw,$matches);
		for($i=0;$i<sizeof($matches[0]);$i++){
			$snippet = $matches[0][$i];
			$source = $transformer->formatSourceCode($matches[2][$i],array("lang" => $matches[1][$i]));
			$placeholder = "source.$i.$uniqid";
			$replaces[$snippet] = "\n\n$placeholder\n\n";

			$transformer->replaces["<p>$placeholder</p>"] = $source;
		}

		// HTML tables
		preg_match_all('/\n<table\b[^>]*>.*?<\/table>\s*?\n/si',$raw,$matches);
		for($i=0;$i<sizeof($matches[0]);$i++){
			$snippet = $matches[0][$i];
			$table = trim($snippet);
			$placeholder = "table.$i.$uniqid";
			$replaces[$snippet] = "\n\n$placeholder\n\n";
			
			$transformer->replaces["<p>$placeholder</p>"] = $table; // <p>table.0.591c34cd0689f</p>
			$transformer->replaces["$placeholder"] = $table; // <div class="table-responsive">table.0.591c34cd0689f</div>
		}

		// Adding empty line before a list when needed
		//
		//	Colors:   ->   Colors:
		//	- red     ->
		//	- blue    ->   - red
		//	- green   ->   - blue
		//	          ->   - green
		//
		// The line before the list (Colors:) should not end with space.
		//
		// TODO: Is this really a wished feature?
		$raw = preg_replace('/(\n[^\n\*-][^\n]*[^ ])\n([\*-] {1,2}[^\s])/s',"\\1\n\n\\2",$raw);

		$raw = EasyReplace($raw,$replaces);

		return $raw;
	}
}
