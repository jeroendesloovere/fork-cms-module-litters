{*
	variables that are available:
	- {$widgetLittersCategories}:
*}

{option:widgetLittersCategories}
	<section id="LittersCategoriesWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblCategories|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetLittersCategories}
						<li>
							<a href="{$widgetLittersCategories.url}">
								{$widgetLittersCategories.label}&nbsp;({$widgetLittersCategories.total})
							</a>
						</li>
					{/iteration:widgetLittersCategories}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetLittersCategories}
