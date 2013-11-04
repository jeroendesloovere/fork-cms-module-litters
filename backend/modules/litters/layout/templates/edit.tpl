{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLitters|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
	<label for="name">{$lblName|ucfirst}</label>
{$txtName} {$txtNameError}
	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
			<li><a href="#tabYoungs">{$lblYoungs|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">
					</td>

					<td id="sidebar">
						<div class="box">
							<div class="heading">
								<h3><label for="father">{$lblFatherId|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
							</div>
							<div class="options">
								{$ddmFatherId} {$ddmFatherIdError}
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3><label for="mother">{$lblMotherId|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
							</div>
							<div class="options">
								{$ddmMotherId} {$ddmMotherIdError}
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3><label for="birthDate">{$lblBirthDate|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
							</div>
							<div class="options">
								<p>{$txtBirthDate} {$txtBirthDateError}</p>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3><label for="descriptionBefore">{$lblDescriptionBefore|ucfirst}</h3>
							</div>
							<div class="options">
								<p>{$txtDescriptionBefore} {$txtDescriptionBeforeError}</p>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3><label for="descriptionAfter">{$lblDescriptionAfter|ucfirst}</h3>
							</div>
							<div class="options">
								<p>{$txtDescriptionAfter} {$txtDescriptionAfterError}</p>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>

		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
		<div id="tabYoungs">
			<div class="pageTitle">
				<h2>{$lblYoungs|ucfirst}</h2>

				<div class="buttonHolderRight">
					<a href="{$var|geturl:'add_young'}" id="addYoungButton" class="button icon iconAdd" title="{$lblAddYoung|ucfirst}">
						<span>{$lblAddYoung|ucfirst}</span>
					</a>
				</div>
			</div>

			{option:dgYoungs}
				<div class="dataGridHolder">
					{$dgYoungs}
				</div>
			{/option:dgYoungs}

			{option:!dgYoungs}
				{$msgNoItems}
			{/option:!dgYoungs}
		</div>
	</div>
	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>

		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>
	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.name}}
		</p>
	</div>
{/form:edit}

<div id="youngFormDialog">
	{form:young}
		{$hidLitterId}
		{$hidYoungId}

		<div class="options">
			<p>
				<label for="codeName">{$lblCodeName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtCodeName} {$txtCodeNameError}
				<abbr class="help">(?)</abbr>
				<span class="tooltip" style="display: none;">{$msgHelpCodeName}</span>
			</p>
		</div>
		<div class="options">
			<p>
				<label for="youngName">{$lblYoungName|ucfirst}</label>
				{$txtYoungName} {$txtYoungNameError}
			</p>
		</div>
		<div class="options">
			<p class="p0 inlineLabels">
				<label>{$lblSex|ucfirst}</label>
				{iteration:sex}
					<label for="{$sex.id}">{$sex.label|ucfirst} {$sex.rbtSex}</label>
				{/iteration:sex}
				{$rbtSexError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="color">{$lblColor|ucfirst}</label>
				{$txtColor} {$txtColorError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="ems_code">{$lblEms|ucfirst}</label>
				{$txtEmsCode} {$txtEmsCodeError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="availability">{$lblAvailability|ucfirst}</label>
				{$ddmAvailability} {$ddmAvailabilityError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="quality">{$lblQuality|ucfirst}</label>
				{$ddmQuality} {$ddmQualityError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="galleryUrl">{$lblGalleryUrl|ucfirst}</label>
				{$txtGalleryUrl} {$txtGalleryUrlError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="photo">{$lblPhoto|ucfirst}</label>
				<img src="" id="photoPreview" width="100" />
				{$filePhoto} {$filePhotoError}
			</p>
		</div>
	{/form:young}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}