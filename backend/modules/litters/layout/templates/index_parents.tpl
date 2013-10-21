{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>
		{$lblLittersParents|ucfirst}
	</h2>

	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_parent'}" class="button icon iconAdd" title="{$lblAddParent|ucfirst}">
			<span>{$lblAddParent|ucfirst}</span>
		</a>
	</div>
</div>

{option:dataGrid}
	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}

{option:!dataGrid}
{$msgNoItems}
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}