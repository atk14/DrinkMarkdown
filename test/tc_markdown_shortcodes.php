<?php
class TcMarkdownShortcodes extends TcBase {

	function test_parseParams(){
		$postfilter = new MarkdownShortcodesPostfilter();

		$this->assertEquals(array(),$postfilter->parseParams(""));
		$this->assertEquals(array("class" => "message"),$postfilter->parseParams("class=message"));
		$this->assertEquals(array("class" => "message message-alert"),$postfilter->parseParams('class="message message-alert"'));
		$this->assertEquals(array("class" => "message", "id" => "123"),$postfilter->parseParams("class=message id=123"));
		$this->assertEquals(array("class" => "message message-alert", "id" => "456"),$postfilter->parseParams('class="message message-alert" id=\'456\''));
		$this->assertEquals(array("order" => "0", "class" => "message message-alert"),$postfilter->parseParams(' order=0  class="message message-alert" '));
		$this->assertEquals(array("class" => "message", "format" => "300x300,enable_enlargement"),$postfilter->parseParams('class=message format="300x300,enable_enlargement"'));
	}

	function test_prefilter(){
		$prefilter = new MarkdownShortcodesPrefilter();
		$transformer = new DrinkMarkdown();

		$src = '[col]Hello World![/col]';
		$expected = "<!-- drink:col -->\n\nHello World!\n\n<!-- /drink:col -->";
		$this->assertEquals($expected,$prefilter->filter($src,$transformer));

		$src = '[row][col]Hello World![/col][/row]';
		$expected = "<!-- drink:row -->\n\n<!-- drink:col -->\n\nHello World!\n\n<!-- /drink:col -->\n\n<!-- /drink:row -->";
		$this->assertEquals($expected,$prefilter->filter($src,$transformer));


		$src = trim('
# Hello World

Hi there!

[row]
[col class="first"]This is the first column.[/col]
[col class="second"]This is the second column.[/col]
[/row]
		');

		$expected = trim('
# Hello World

Hi there!

<!-- drink:row -->

<!-- drink:col class="first" -->

This is the first column.

<!-- /drink:col -->

<!-- drink:col class="second" -->

This is the second column.

<!-- /drink:col -->

<!-- /drink:row -->
');
		$this->assertEquals($expected,$prefilter->filter($src,$transformer));
	}

	function test_enabled_disabled(){
		$markdown = new DrinkMarkdown(array(
			"shortcodes_enabled" => true,
		));

		$src = '[row][col]Hello World![/col][/row]';
		$expected = trim('
<div class="row row--shortcode">


<div class="col-12 col-md col--shortcode">


<p>Hello World!</p>


</div>



</div>
		');
		$this->assertEquals($expected,$markdown->transform($src));

		$markdown = new DrinkMarkdown(array(
			"shortcodes_enabled" => false,
		));
		$src = '[row][col]Hello World![/col][/row]';
		$expected = '<p>[row][col]Hello World![/col][/row]</p>';
		$this->assertEquals($expected,$markdown->transform($src));
	}
}
