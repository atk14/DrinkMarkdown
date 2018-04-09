<?php
require(__DIR__ . "/uppercase_postfilter.php");

class TcDrinkMarkdownFilter extends TcBase {

	function test_hideSomething(){
		$text = 'Hello There! <a href="http://homepage.com">My homepage is here</a> // <a href="http://favourite-site.com/">my favourite site</a>...';

		$filter = new DrinkMarkdownFilter();

		$filter->hideSomething('/<a\b[^>]*>.*?<\/a>/si',$text,$replaces_back);

		$this->assertFalse(!!preg_match('/<a/',$text));

		$this->assertEquals(array(
			'<a href="http://homepage.com">My homepage is here</a>',
			'<a href="http://favourite-site.com/">my favourite site</a>',
		),array_values($replaces_back));
	}

	function test_appendPostfilter(){
		$markdown = 'Hello There! [My homepage is here](http://homepage.com)';

		$dm = new DrinkMarkdown();
		$dm->appendPostfilter(new UppercasePostfilter());

		$html = $dm->transform($markdown);

		$this->assertEquals('<p>HELLO THERE! <a href="http://homepage.com">MY HOMEPAGE IS HERE</a></p>',$html);
	}
}
