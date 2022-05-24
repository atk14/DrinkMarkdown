<?php
class TcSpan extends TcBase {

	function test(){
		$markdown = new DrinkMarkdown(array("shortcodes_enabled" => true));

		$src = 'Hello [span class="text-danger" id="world"]World[/span]!';
		$expected = '<p>Hello <span class="text-danger" id="world">World</span>!</p>';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		$src = 'He[span]ll[/span]o [span class="text-danger" id="world"]*World*[/span]!';
		$expected = '<p>He<span>ll</span>o <span class="text-danger" id="world"><em>World</em></span>!</p>';
		$this->assertHtmlEquals($expected,$markdown->transform($src));
	}
}
