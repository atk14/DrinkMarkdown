{* generic column classes *}
{capture assign=col_classes}col-12 col-xs-12 col-md-{12/$number_of_columns|round}{/capture}
{if $number_of_columns==4}
	{* column classes for 4-column row *}
	{capture assign=col_classes}col-12 col-xs-12 col-md-6 col-lg-3{/capture}
{elseif $number_of_columns==6}
	{* column classes for 6-column row *}
	{capture assign=col_classes}col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2{/capture}
{/if}
<div class="{$col_classes} col--shortcode{if $class} {$class}{/if}" style="order: {$order};">
{!$content}
</div>
