# Change Log
All notable changes to this project will be documented in this file.

## [0.8.9] - 2025-11-17

* 1c906e4 - Fix

## [0.8.8] - 2025-11-17

* edb7c46 - [scrivo/highlight.php] Package geshi/geshi replaced with scrivo/highlight.php (and tijsverkoyen/css-to-inline-styles)

## [0.8.7] - 2024-03-11

* dfeb91c - Fix - returned previous regular pattern

## [0.8.6] - 2024-03-11

* 808ae53 - Fixed transformation of very long source text (pcre.jit must be set to 0)

## [0.8.5] - 2023-12-06

* f836501 - A shortcode can be written in multiple lines
* 6f7c2eb - Fix for PHP 8.2

## [0.8.4] - 2022-06-10

* b2bf2fd - Missmatched block shortcodes are highlighted in the output

## [0.8.3] - 2022-05-24

* 83a17b3 - Added built-in inline block shortcode [span]

## [0.8.2] - 2022-03-19

* 35d9cc3 - Nested block shortcodes detection corrected

## [0.8.1] - 2022-03-09

* 504256b - Counting columns fixed in the shortcode row

## [0.8] - 2022-02-27

- Smarty shortcode autowiring

## [0.7.2] - 2021-05-28

- PHP5.5 issue fixed

## [0.7.1] - 2021-04-01

- Block shortcode div rewritten as a callback

## [0.7] - 2021-04-01

- Added block shortcode div

## [0.6.4] - 2021-02-10

- Building direct links to Iobjects fixed - a link can be on an image, which is also an Iobject

## [0.6.3] - 2020-07-18

- Dependency updated: ezyang/htmlpurifier: >=4.8|<=4.13

## [0.6.2] - 2020-06-16

- Added option keep_html_tables_unmodified to DrinkMarkdownPrefilter (true by default)

## [0.6.1] - 2020-05-27

- Markdown transformation can be disabled inside a block shortcode

## [0.6] - 2020-05-27

- Shortcodes can be registered with callbacks
- Added support for inline block shortcodes and function shortcodes
- Shortcodes can be registered as callbacks

## [0.5.4] - 2020-05-19

- Template for the shortcode *col* optimized for 4 and 6 column row

## [0.5.3] - 2020-05-13

- Processing of block shortcodes fixed

## [0.5.2] - 2020-05-13

- Counting columns in a [row], added class like col-md-6 or col-md-4 to a [col]

## [0.5.1] - 2020-05-13

- Tests fixed

## [0.5] - 2020-04-17

- Shortcodes implemented
- There are built-in shortcodes [row] & [col]

## [0.4.1] - 2020-02-12

- Some HTML syntax glitches removed in postfilter
- Rendering of direct links to Iobjects fixed

## [0.4] - 2020-01-20

- Package michelf/php-markdown can be installe in versions 1.7, 1.8 or 1.9

## [0.3] - 2019-10-04

- Iobject can be placed into a table cell

## [0.2] - 2018-04-09

### Added
- Direct links to Iobjects are converted to URLs of their details
- DrinkMarkdownFilter: base class for other filters

### Changed
- There may be more prefilters or postfilters

## [0.1] - 2018-03-02

First officially tagged version.
