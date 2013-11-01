<ul>
	{iteration:items}
		<li><a href="{$items.full_url}">{$items.name}</a></li>
	{/iteration:items}
</ul>
{include:core/layout/templates/pagination.tpl}
