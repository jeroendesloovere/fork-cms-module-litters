<div class="table">
	<div class="two-eighths dark">
		<div class="parent mother">
			<h2 class="delta">{$lblMother|ucfirst}</h2>
			<div>
				{option:item.mother_photo_url}<img src="{$item.mother_photo_url|createimage:250:250}" alt="{option:item.mother_affix_prepend}{$item.mother_affix} {/option:item.mother_affix_prepend}{$item.mother_name}{option:item.mother_affix_append} {$item.mother_affix}{/option:item.mother_affix_append}">{/option:item.mother_photo_url}
				{option:!item.mother_photo_url}<img src="/frontend/modules/litters/layout/images/cat_shape.png" alt="Pas de photo pour {$item.mother_name}" />{/option:!item.mother_photo_url}
			</div>
			{option:item.mother_url}<a href="{$item.mother_url}">{/option:item.mother_url}{option:item.mother_affix_prepend}<span class="affixe">{$item.mother_affix}</span> {/option:item.mother_affix_prepend}{$item.mother_name}{option:item.mother_affix_append} <span class="affixe">{$item.mother_affix}</span>{/option:item.mother_affix_append}{option:item.mother_url}</a>{/option:item.mother_url}
		</div>

		<div class="parent father">
			<h2 class="delta">{$lblFather|ucfirst}</h2>
			<div>
				{option:item.father_photo_url}<img src="{$item.father_photo_url|createimage:250:250}" alt="{option:item.father_affix_prepend}{$item.father_affix} {/option:item.father_affix_prepend}{$item.father_name}{option:item.father_affix_append} {$item.father_affix}{/option:item.father_affix_append}">{/option:item.father_photo_url}
				{option:!item.father_photo_url}<img src="/frontend/modules/litters/layout/images/cat_shape.png" alt="Pas de photo pour {$item.father_name}" />{/option:!item.father_photo_url}
			</div>
			{option:item.father_url}<a href="{$item.father_url}">{/option:item.father_url}{option:item.father_affix_prepend}<span class="affixe">{$item.father_affix}</span> {/option:item.father_affix_prepend}{$item.father_name}{option:item.father_affix_append} <span class="affixe">{$item.father_affix}</span>{/option:item.father_affix_append}{option:item.father_url}</a>{/option:item.father_url}
		</div>
	</div>

	<div class="six-eighths">
		<h1>{$title|ucfirst} ({$item.birth_date})</h1>

		{option:item.description_before}
		<section>
			{$item.description_before}
		</section>
		{/option:item.description_before}

		<table class="litter">
			<thead>
				<tr>
					<th>{$lblName|ucfirst}</th>
					<th>{$lblSex|ucfirst}</th>
					<th>{$lblEms|ucfirst}</th>
					<th>{$lblColor|ucfirst}</th>
					<th>{$lblQuality|ucfirst}</th>
					<th>{$lblAvailability|ucfirst}</th>
					<th>{$lblGallery|ucfirst}</th>
				</tr>
			</thead>
			<tbody>
				{iteration:youngs}
					<tr>
						<th>{$youngs.name} ({$youngs.code_name})</th>
						<td>{$youngs.sex}</td>
						<td>{$youngs.ems_code}</td>
						<td>{$youngs.color}</td>
						<td>{$youngs.quality}</td>
						<td>{$youngs.availability}</td>
						<td>
							{option:youngs.url}<a href="{$youngs.url}" title="{$lblGoToItsGallery|ucfirst}">{/option:youngs.url}
								{option:youngs.photo_url}<img src="{$youngs.photo_url|createimage:100:100}" alt="{$youngs.name} ({$youngs.code_name})" />{/option:youngs.photo_url}
								{option:!youngs.photo_url}<img src="/frontend/modules/litters/layout/images/cat_shape.png" alt="Pas de photo" />{/option:!youngs.photo_url}
							{option:youngs.url}</a>{/option:youngs.url}
						</td>
					</tr>
				{/iteration:youngs}
			</tbody>
		</table>

		{option:item.description_after}
			<section>
				{$item.description_after}
			</section>
		{/option:item.description_after}
	</div>
</div>