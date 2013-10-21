{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLitters|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<table class="name">
		<tr>
			<th><label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></th>
			<th><label for="affix">{$lblAffix|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></th>
			<th><label>{$lblAffixPosition|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></th>
		</tr>
		<tr>
			<td>
				{$txtName} {$txtNameError}
			</td>
			<td>
				{$txtAffix} {$txtAffixError}
			</td>
			<td>
				{iteration:affix_position}
					<label for="{$affix_position.id}">{$affix_position.label|ucfirst} {$affix_position.rbtAffixPosition}</label>
				{/iteration:affix_position}
				{$rbtAffixPositionError}
			</td>
		</tr>
	</table>
	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl"></span></a></span>
				</p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">
					</td>

					<td id="sidebar">
						<div class="box">
							<div class="heading">
								<h3>{$lblPhysicalCharacteristics|ucfirst}</h3>
							</div>
							<div class="options">
								<table>
									<tr>
										<th><label for="sex">{$lblSex|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
										</th>
										<th><label for="birthDate">{$lblBirthDate|ucfirst}
												<abbr title="{$lblRequiredField}">*</abbr></label></th>
										<th><label for="color">{$lblColor|ucfirst}</label></th>
									</tr>
									<tr>
										<td>
											{iteration:sex}
												<label for="{$sex.id}">{$sex.label|ucfirst} {$sex.rbtSex}</label>
											{/iteration:sex}
											{$rbtSexError}
										</td>
										<td><p>{$txtBirthDate} {$txtBirthDateError}</p></td>
										<td><p>{$txtColor} {$txtColorError}</p></td>
									</tr>
								</table>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>{$lblPresentationData|ucfirst}</h3>
							</div>
							<div class="options">
								<table>
									<tr>
										<th><label for="url">{$lblPersonalPageUrl|ucfirst}</label></th>
										<th><label for="photo">{$lblPhoto|ucfirst}</label></th>
									</tr>
									<tr>
										<td><p>{$txtPersonalPageUrl} {$txtPersonalPageUrlError}</p></td>
										<td><p>{$filePhoto} {$filePhotoError}</p></td>
									</tr>
								</table>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>

		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
	</div>
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}