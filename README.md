DrinkMarkdown
=============

Extended PHP Markdown parser tuned for usage in ATK14 projects. It's built on Michel Fortin's PHP Markdown Extra.

Originally it was developed for the project "Doctor Ink" (shortly "Drink").

Beware! It is in alpha state. It has some interconnections to the ATK14 Framework thus it can't be easily used in other projects.

Installation
------------

Just use the Composer:

    $ cd path/to/your/atk14/project/
    $ composer require atk14/drink-markdown dev-master

Usage
-----

    $dm = new DrinkMarkdown(array(
      "table_class" => "table", // the CSS class for table, default is "table table-bordered table-hover"
      "html_purification_enabled" => true, // default is true
      "temp_dir" => "/path/to/temp", // default is constant TEMP or sys_get_temp_dir()
    ));

    $html = $dm->transform($markdown);

License
-------

DrinkMarkdown is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)
