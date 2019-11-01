<?php
class TcDrinkMarkdown extends TcBase{

	function test(){
		$dm = new DrinkMarkdown(array("table_class" => "table table-bordered table-hover"));

		$this->assertEquals('<p>Hello World!</p>',$dm->transform('Hello World!'));

		// Links
		
		$this->assertEquals('<p>Welcome at <a href="http://www.earth.net">www.earth.net</a>!</p>',$dm->transform('Welcome at www.earth.net!'));

		$this->assertEquals('<p>Contact as on <a href="http://www.earth.net">www.earth.net</a><br />
or <a href="mailto:we@earth.net">we@earth.net</a></p>',$dm->transform("Contact as on www.earth.net  \nor we@earth.net"));

		// Text centering

		$this->assertEquals('<h1><center>Title</center></h1>',$dm->transform('# <center>Title</center>'));

		$this->assertEquals("<center>\n\n<p>Centered text block</p>\n\n</center>",$dm->transform("<center>\n\nCentered text block\n\n</center>"));

		// Empty documents

		$this->assertEquals('',$dm->transform(''));
		$this->assertEquals('',$dm->transform(' '));
		$this->assertEquals('',$dm->transform("\n\n\n\n\n"));
		$this->assertEquals('',$dm->transform("\n \n \n \n \n"));

		// Markdown tables

		$dm2 = new DrinkMarkdown(array("table_class" => ""));

		$src = '
Paragraph #1

| | |
|-|-|
|a|b|
|c|d|

Paragraph #2
';
		$result = trim('
<p>Paragraph #1</p>

<table class="table table-bordered table-hover"><thead></thead><tbody><tr><td>a</td>
  <td>b</td>
</tr><tr><td>c</td>
  <td>d</td>
</tr></tbody></table><p>Paragraph #2</p>');
		$result2 = trim('
<p>Paragraph #1</p>

<table><thead></thead><tbody><tr><td>a</td>
  <td>b</td>
</tr><tr><td>c</td>
  <td>d</td>
</tr></tbody></table><p>Paragraph #2</p>');
		$this->assertHtmlEquals($result,$dm->transform($src));
		$this->assertHtmlEquals($result2,$dm2->transform($src));

		// HTML tables

		// 1)
		$src = '
Paragraph #1

<table>
  <tr>
    <th>key</th>
    <td>val</td>
  </tr>
</table>

Paragraph #2';
		$result = trim('
<p>Paragraph #1</p>

<table class="table table-bordered table-hover">
  <tr>
    <th>key</th>
    <td>val</td>
  </tr>
</table>

<p>Paragraph #2</p>');
		$this->assertEquals($result,$dm->transform($src));

		// 2)
		$src = '
Paragraph #1

<table border="1" class="t">
  <tr>
    <th>key</th>
    <td>val</td>
  </tr>
</table>

Paragraph #2';
		$result = trim('
<p>Paragraph #1</p>

<table border="1" class="t table table-bordered table-hover">
  <tr>
    <th>key</th>
    <td>val</td>
  </tr>
</table>

<p>Paragraph #2</p>');
		$this->assertEquals($result,$dm->transform($src));

		// 3)
		$src = '
Paragraph #1

<table class="table table-bordered table-hover">
  <tr>
    <th>key</th>
    <td>val</td>
  </tr>
</table>

Paragraph #2';
		$result = trim('
<p>Paragraph #1</p>

<table class="table table-bordered table-hover">
  <tr>
    <th>key</th>
    <td>val</td>
  </tr>
</table>

<p>Paragraph #2</p>');
		$this->assertEquals($result,$dm->transform($src));

		// 4)
		$src = '
Paragraph #1

<div class="table-responsive">

<table>
  <tr>
    <th>key2</th>
    <td>val2</td>
  </tr>
</table>

</div>

Paragraph #2';
		$result = trim('
<p>Paragraph #1</p>

<div class="table-responsive">


<table class="table table-bordered table-hover">
  <tr>
    <th>key2</th>
    <td>val2</td>
  </tr>
</table>


</div>

<p>Paragraph #2</p>');
		$this->assertEquals($result,$dm->transform($src));

		// Code

		$src = '
Paragraph #1

```
function helloWorld(){
  alert("Hello World!");
}
```

Paragraph #2
';
		$result = trim('
<p>Paragraph #1</p>

<pre><code>function helloWorld(){
  alert(&quot;Hello World!&quot;);
}</code></pre>

<p>Paragraph #2</p>');
		$this->assertEquals($result,$dm->transform($src));


		// Code with highlighted syntax

		$src = '
Paragraph #1

```javascript
function helloWorld(){
  alert("Hello World!");
}
```

Paragraph #2
';

		$result = trim('
<p>Paragraph #1</p>

<pre><span style="color: #000066; font-weight: bold;">function</span> helloWorld<span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #009900;">&#123;</span>
  alert<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">&quot;Hello World!&quot;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
<span style="color: #009900;">&#125;</span></pre>

<p>Paragraph #2</p>');

		$this->assertEquals($result,$dm->transform($src));

		// HTML Purifier

		$dm2 = new DrinkMarkdown(array("html_purification_enabled" => false));

		$src = 'Please <a href="http://www.atk14.net/" class="link" onclick="alert(\'You have clicked!\');">click here</a>';
		$result_purified = '<p>Please <a href="http://www.atk14.net/" class="link">click here</a></p>'; // no onclick attribute!
		$result_not_purified = '<p>Please <a href="http://www.atk14.net/" class="link" onclick="alert(\'You have clicked!\');">click here</a></p>';
		$this->assertEquals($result_purified,$dm->transform($src));
		$this->assertEquals($result_not_purified,$dm2->transform($src));

		$src = 'Not <b><em>well</b></em> formatted!';
		$result_purified = '<p>Not <b><em>well</em></b> formatted!</p>'; // well formatted!
		$result_not_purified = '<p>Not <b><em>well</b></em> formatted!</p>';
		$this->assertEquals($result_purified,$dm->transform($src));
		$this->assertEquals($result_not_purified,$dm2->transform($src));

		$src = 'XSS? <script type="text/javascript">alert("xss");</script>';
		$result_purified = '<p>XSS? </p>'; // no <script> tag
		$result_not_purified = '<p>XSS? <script type="text/javascript">alert("xss");</script></p>'; //
		$this->assertEquals($result_purified,$dm->transform($src));
		$this->assertEquals($result_not_purified,$dm2->transform($src));

		$src = trim('
<html>

Hell Yeah!

</html>');
		$result_purified = '
<p></p>

<p>Hell Yeah!</p>

<p></p>';

		$result_not_purified = '
<p><html></p>

<p>Hell Yeah!</p>

<p></html></p>';
		$this->assertEquals(trim($result_purified),trim($dm->transform($src))); // no <html> element
		$this->assertEquals(trim($result_not_purified),trim($dm2->transform($src)));

		// Lists

		$src = '
Colors

- red
- blue
- yellow';

		$result = '
<p>Colors</p>

<ul><li>red</li>
<li>blue</li>
<li>yellow</li>
</ul>';

		$this->assertHtmlEquals(trim($result),trim($dm->transform($src)));

		$src = '
Colors
- red
- blue
- yellow';

		$result = '
<p>Colors</p>

<ul><li>red</li>
<li>blue</li>
<li>yellow</li>
</ul>';

		$this->assertHtmlEquals(trim($result),trim($dm->transform($src)));
	}

	function test_iobjects(){
		$dm = new DrinkMarkdown();

		// Iobject
		$src = '
# Iobject

[#1 Image: Testing Image]';

		$result = '
<h1>Iobject</h1>

<img src="rose_1.jpg" with="444" height="333">';

		$this->assertEquals(trim($result),trim($dm->transform($src)));

		// Iobjects in Table
		$src = '
# Iobjects in Table

|||
|-|-|
|[#1 Image: Testing Image]|[#2 Image: Testing Image]|';

		$result = '
<h1>Iobjects in Table</h1>

<table class="table"><thead></thead><tbody><tr><td><img src="rose_1.jpg" with="444" height="333"></td>
  <td><img src="rose_2.jpg" with="444" height="333"></td>
</tr></tbody></table>
		';

		$this->assertHtmlEquals(trim($result),trim($dm->transform($src)));

		// Link to Iobject
		$src = '
# Link to Iobject

[See the picture]([#3 Image: Testing Image])';

		$result = '
<h1>Link to Iobject</h1>

<p><a href="http://www.example.com/rose_3.jpg">See the picture</a></p>';

		$this->assertEquals(trim($result),trim($dm->transform($src)));

		$src = '## H2 baby';
		$result = '<h2>H2 baby</h2>';
		$this->assertEquals(trim($result),trim($dm->transform($src)));

		$src = '### H3 baby';
		$result = '<h3>H3 baby</h3>';
		$this->assertEquals(trim($result),trim($dm->transform($src)));
	}
}
