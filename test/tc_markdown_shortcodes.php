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

		// Inline block shortcodes

		$transformer = new DrinkMarkdown(array("shortcodes_enabled" => true));
		$transformer->registerInlineBlockShortcode("upper");
		$transformer->registerFunctionShortcode("name");

		$src = 'Hello [upper]beautiful world and [name][/upper]!';
		$expected = 'Hello <!-- drink:upper -->beautiful world and <!-- drink:name --><!-- /drink:upper -->!';
		$this->assertEquals($expected,$prefilter->filter($src,$transformer));
	}

	function test(){
		$transformer = new DrinkMarkdown(array("shortcodes_enabled" => true));
		$transformer->registerBlockShortcode("alert");
		$transformer->registerInlineBlockShortcode("upper");
		$transformer->registerFunctionShortcode("name");

		$src = 'Hello [upper]beautiful world and [name][/upper]!';
		$expected = '<p>Hello BEAUTIFUL WORLD AND JOHN DOE!</p>';
		$this->assertEquals($expected,$transformer->transform($src));

		$src = 'Hello [upper]beautiful world and [name gender="female"][/upper]!';
		$expected = '<p>Hello BEAUTIFUL WORLD AND SAMANTHA DOE!</p>';
		$this->assertEquals($expected,$transformer->transform($src));

		$src = '[alert type="info"]
Welcome [upper][name gender="female"][/upper]!

[/alert]';
		$expected = trim('
<div class="alert alert-info">

<p>Welcome SAMANTHA DOE!</p>

</div>
		');
		$this->assertEquals($expected,$transformer->transform($src));
	}

	function test_enabled_disabled(){
		$markdown = new DrinkMarkdown(array(
			"shortcodes_enabled" => true,
		));

		$src = '[row][col]Hello World![/col][/row]';
		$expected = trim('
<div class="row row--shortcode">


<div class="col-12 col-xs-12 col-md-12 col--shortcode">


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

	function test_callbacks(){
		$markdown = new DrinkMarkdown();

		// blocks
		$markdown->registerInlineBlockShortcode("upper"); // Smarty
		$markdown->registerInlineBlockShortcode("lower",function($content){ return strtolower($content); });
		
		// functions
		$markdown->registerFunctionShortcode("name"); // Smarty
		$markdown->registerFunctionShortcode("veggie",function($params){
			$params += array(
				"color" => "green"
			);
			$veggies = array(
				"green" => "cucumber",
				"red" => "tomatoe",
				"yellow" => "potatoe"
			);
			$color = $params["color"];
			return isset($veggies[$color]) ? $veggies[$color] : "nothing";
		});

		$src = '[lower]Boys[/lower] & [upper]girls[/upper]';
		$expected = '<p>boys &amp; GIRLS</p>';
		$this->assertEquals($expected,$markdown->transform($src));

		$src = '[name] loves [veggie]!';
		$expected = '<p>John Doe loves cucumber!</p>';
		$this->assertEquals($expected,$markdown->transform($src));

		$src = '[name gender="female"] loves [veggie color="red"]!';
		$expected = '<p>Samantha Doe loves tomatoe!</p>';
		$this->assertEquals($expected,$markdown->transform($src));
	}

	function test_markdown_transformation_enabled(){
		// Block shortcode example is applied to the transformed text - this is default behaviour
		$markdown = new DrinkMarkdown();
		$markdown->registerBlockShortcode("example", array(
			"callback" => function($content,$params){
				return '<div class="example">'.$content.'</div><pre><code>'.h(trim($content)).'</code></pre></div>';
			},
			"markdown_transformation_enabled" => true,
		));
		$src = '[example]Hello <b>world</b>![/example]';
		$expected = trim('
<div class="example">

<p>Hello <b>world</b>!</p>

</div><pre><code>&lt;p&gt;Hello &lt;b&gt;world&lt;/b&gt;!&lt;/p&gt;</code></pre>');
		$this->assertEquals($expected,$markdown->transform($src));

		// Block shortcode example is applied to the raw (not been transformed) text
		$markdown = new DrinkMarkdown();
		$markdown->registerBlockShortcode("example", array(
			"callback" => function($content,$params){
				return '<div class="example">'.$content.'</div><pre><code>'.h(trim($content)).'</code></pre></div>';
			},
			"markdown_transformation_enabled" => false,
		));
		$src = '[example]Hello <b>world</b>![/example]';
		$expected = trim('
<div class="example">

Hello <b>world</b>!

</div><pre><code>Hello &lt;b&gt;world&lt;/b&gt;!</code></pre>');
		$this->assertEquals($expected,$markdown->transform($src));


		// Inline block shortcode with disabled markdown transformation
		$markdown = new DrinkMarkdown();
		$markdown->registerInlineBlockShortcode("literal", array(
			"callback" => function($content,$params){ return $content; },
			"markdown_transformation_enabled" => false,
		));
		$src = "If you want bold text in Markdown, use [literal]**asterisks**[/literal]";
		$expected = '<p>If you want bold text in Markdown, use **asterisks**</p>';
		$this->assertEquals($expected,$markdown->transform($src));
	}

	function test_shortocodes_in_source_code_samples(){
		$markdown = new DrinkMarkdown(array("shortcodes_enabled" => true));
		$src = '
```
line1
[row][col]line2[/col][/row]
line3
```
		';
		$expected = '<pre><code>line1
[row][col]line2[/col][/row]
line3</code></pre>';
		$this->assertEquals($expected,$markdown->transform($src));
	}

	function test_smarty_shortcode_autowiring(){
		$markdown = new DrinkMarkdown();

		$this->assertEquals(true,$markdown->isShortcodeRegistered("name"));
		$this->assertEquals(true,$markdown->isShortcodeRegistered("upper"));

		$this->assertEquals('<p>Hi John Doe!</p>',$markdown->transform("Hi [name]!"));

		// By default, block shortcode is being registered as a block (not inline) shortcode
		$this->assertEquals(trim('
<p>Hi</p>



<p>SAMANTHA</p>
		'),trim($markdown->transform("Hi [upper]Samantha[/upper]")));

		// Disabling autowiring

		$markdown = new DrinkMarkdown(array("shortcode_autowiring_enabled" => false));

		$this->assertEquals(false,$markdown->isShortcodeRegistered("name"));
		$this->assertEquals(false,$markdown->isShortcodeRegistered("upper"));

		$this->assertEquals('<p>Hi [name]!</p>',$markdown->transform("Hi [name]!"));
		$this->assertEquals('<p>Hi [upper]Samantha[/upper]</p>',$markdown->transform("Hi [upper]Samantha[/upper]"));

		$markdown->registerFunctionShortcode("name");

		$this->assertEquals('<p>Hi John Doe!</p>',$markdown->transform("Hi [name]!"));
		$this->assertEquals('<p>Hi [upper]Samantha[/upper]</p>',$markdown->transform("Hi [upper]Samantha[/upper]"));
	}
}
