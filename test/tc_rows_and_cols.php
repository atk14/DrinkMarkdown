<?php
class TcRowsAndCols extends TcBase {

	function test(){
		$markdown = new DrinkMarkdown(array("shortcodes_enabled" => true));


		$src = '[row][col]Hello World![/col][/row]';
		$expected = trim('
<div class="row row--shortcode">


<div class="col-12 col-md col-md-12 col--shortcode">


<p>Hello World!</p>


</div>



</div>
		');
		$this->assertEquals($expected,$markdown->transform($src));

		$src = '[row][col]Hello World![/col][col]Hello Another World![/col][/row]';
		$expected = trim('
<div class="row row--shortcode">


<div class="col-12 col-md col-md-6 col--shortcode">


<p>Hello World!</p>


</div>


<div class="col-12 col-md col-md-6 col--shortcode">


<p>Hello Another World!</p>


</div>



</div>
		');
		$this->assertEquals($expected,$markdown->transform($src));


		$src = '[row][col]Hello World![/col][col]Hello Another World![/col][col]Hello from outer space![/col][/row]';
		$expected = trim('
<div class="row row--shortcode">


<div class="col-12 col-md col-md-4 col--shortcode">


<p>Hello World!</p>


</div>


<div class="col-12 col-md col-md-4 col--shortcode">


<p>Hello Another World!</p>


</div>


<div class="col-12 col-md col-md-4 col--shortcode">


<p>Hello from outer space!</p>


</div>



</div>
		');
		$this->assertEquals($expected,$markdown->transform($src));


		$src = '[row][col]Hello World![/col][/row]
		[row][col]Hello Another World![/col][col]Hello from outer space![/col][/row]';
		$expected = trim('
<div class="row row--shortcode">


<div class="col-12 col-md col-md-12 col--shortcode">


<p>Hello World!</p>


</div>



</div>


<div class="row row--shortcode">


<div class="col-12 col-md col-md-6 col--shortcode">


<p>Hello Another World!</p>


</div>


<div class="col-12 col-md col-md-6 col--shortcode">


<p>Hello from outer space!</p>


</div>



</div>
		');
		$this->assertEquals($expected,$markdown->transform($src));
	}
}
