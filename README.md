DrinkMarkdown
=============

Extended PHP Markdown parser tuned for usage in ATK14 projects. It's built on Michel Fortin's PHP Markdown Extra.

Originally it was developed for the project "Doctor Ink" (shortly "Drink").

Beware! It is in alpha state. It has some interconnections to the ATK14 Framework thus it can't be easily used in other projects.

Features
--------

DrinkMarkdown extends PHP Markdown Extra to:

- automatically converting URL and email text to clickable links
- providing optional HTML purification
- providing source code syntax highlighting
- table rendering improved
- iobjects (to be explained)

Basic Usage
-----------

    $dm = new DrinkMarkdown([
      "table_class" => "table", // the CSS class for tables, default is "table table-bordered table-hover"
      "html_purification_enabled" => true, // default is true
      "temp_dir" => "/path/to/temp", // default is constant TEMP or sys_get_temp_dir()
      "iobjects_processing_enabled" => true, // insertable objects processing, default is true
      "urlize_text" => true, // reconstruct missing links to urls or emails? default is true
      "shortcodes_enabled" => true, // whether to enable or disable processing of shortcodes, default is true
      "shortcode_autowiring_enabled" => true, // Smarty shortcodes are being registered automatically
    ]);

    $html = $dm->transform($markdown);

Usage in a ATK14 template
-------------------------

DrinkMarkdown package comes with two helpers usables in ATK14 templates.

If you have a trusted content:

    {$text|markdown nofilter} {* or *}
    {!$text|markdown}

If you have an insecure content, e.g. a comment from a user:

    {$comment|safe_markdown nofilter} {* or *}
    {!$comment|safe_markdown}

Shortcodes
----------

DrinkMarkdown can be extended with extensions: so called shortcodes.

There are three types of shortcodes:

- block shortcodes,
- inline block shortcodes and
- function shortcodes.

Rendering of shortcodes is either provided callback functions or by Smarty (template engine used in ATK14 framework) plugins.
Block shortcodes corresponds to Smarty block plugins, function shortcodes corresponds to Smarty function plugins.
The difference between block and inline block shortcodes is that block shortcodes affect whole paragraphs, 
but inline block shortcodes can operate inside paragraphs or sentences.

Built-in shortcodes
-------------------

DrinkMarkdown contains block shortcodes for organizing text into columns.

    [row]

    [col]
    ### Column 1
    This is text of the first column.
    [/col]

    [col]
    ### Column 2
    This is text of the second column.
    [/col]

    [col]
    ### Column 3
    This is text of the third column.
    [/col]

    [/row]

See a living example at http://markdown.plovarna.cz/czech/multiple-columns/

Shortcode _div_ renders <div> element with given attributes. Unlike direct usage of the HTML element div, markdown syntax is being interpreted inside the shortcode.

    [div class="teaser" id="id_teaser"]

    ## Hello World!

    Welcome to this very nice place.

    [/div]

Custom shortcodes
-----------------

    $dm = new DrinkMarkdown();

#### 1. Callbacks

    $dm->registerBlockShortcode("alert", function($columns,$params){
      $params += array(
        "type" => "primary"
      );
      return "<div class=\"alert alert-$params[type]\" role=\"alert\">$content</div>";
    });

    $dm->registerInlineBlockShortcode("upper", function($content,$params){ return strtoupper($content); });

    $dm->registerFunctionShortcode("name", function($params){
      $params += array(
        "gender" => "male"
      );
      return $params["gender"]=="female" ? "Samantha Doe" : "John Doe";
    });

#### 2. Smarty plugins

    $dm->registerBlockShortcode("alert");
    $dm->registerInlineBlockShortcode("upper");
    $dm->registerFunctionShortcode("name");

If no callback is specified during a shortcode registration, an appropriate Smarty plugin is required.
Note the naming conventions of plugins.

    <?php
    // file: app/helpers/block.drink_shortcode__alert.php
    function smarty_block_drink_shortcode__alert($params,$content,$template,&$repeat){
      if($repeat){ return; }

      $params += array(
        "type" => "primary"
      );

      return "<div class=\"alert alert-$params[type]\" role=\"alert\">$content</div>";
    }

    <?php
    // file: app/helpers/block.drink_shortcode__upper.php
    function smarty_block_drink_shortcode__upper($params,$content,$template,&$repeat){
      if($repeat){ return; }

      return strtoupper($content);
    }

    <?php
    // file: app/helpers/function.drink_shortcode__name.php
    function smarty_function_drink_shortcode__name($params,$template){
      $params += array(
        "gender" => "male"
      );

      return $params["gender"]=="female" ? "Samantha Doe" : "John Doe";
    }

Now, everything is set and ready. The following markdown text...

    ## This is welcome screen!

    [alert type="info"]
    Welcome [upper][name gender="female"][/upper]
    [/alert]

... will be rendered as:

    <h2>This is welcome screen!</h2>

    <div class="alert alert-info">
    <p>Welcome SAMANTHA DOE!</p>
    </div>

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/drink-markdown

Optionaly you can link (or copy & edit) helpers to your project.

    ln -s ../../vendor/atk14/drink-markdown/src/app/helpers/block.markdown.php app/helpers/
    ln -s ../../vendor/atk14/drink-markdown/src/app/helpers/block.safe_markdown.php app/helpers/
    ln -s ../../vendor/atk14/drink-markdown/src/app/helpers/modifier.markdown.php app/helpers/
    ln -s ../../vendor/atk14/drink-markdown/src/app/helpers/modifier.safe_markdown.php app/helpers/
    ln -s ../../vendor/atk14/drink-markdown/src/app/helpers/block.drink_shortcode__row.php app/helpers/
    ln -s ../../vendor/atk14/drink-markdown/src/app/helpers/block.drink_shortcode__col.php app/helpers/
    mkdir -p app/views/shared/helpers/drink_shortcodes
    ln -s ../../../../../vendor/atk14/drink-markdown/src/app/views/shared/helpers/drink_shortcodes/_row.tpl app/views/shared/helpers/drink_shortcodes/
    ln -s ../../../../../vendor/atk14/drink-markdown/src/app/views/shared/helpers/drink_shortcodes/_col.tpl app/views/shared/helpers/drink_shortcodes/

License
-------

DrinkMarkdown is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
