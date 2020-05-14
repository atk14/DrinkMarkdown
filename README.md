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
