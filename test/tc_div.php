<?php
class TcDiv extends TcBase {

	function test(){
		$markdown = new DrinkMarkdown(array("shortcodes_enabled" => true));

		$src = '[div]Hello World![/div]';
		$expected = '
			<div>
				<p>Hello World!</p>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		$src = '[div class="teaser" id="id_treaser"]**Hello World!**[/div]';
		$expected = '
			<div class="teaser" id="id_treaser">
				<p><strong>Hello World!</strong></p>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));
	}
}
