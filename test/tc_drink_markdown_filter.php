<?php
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
}
