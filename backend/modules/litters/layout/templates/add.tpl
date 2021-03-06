{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblLitters|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<label for="name">{$lblName|ucfirst}</label>
	{$txtName} {$txtNameError}
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
								<h3>
									<label for="father">{$lblFatherId|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								</h3>
							</div>
							<div class="options">
								{$ddmFatherId} {$ddmFatherIdError}
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>
									<label for="mother">{$lblMotherId|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
								</h3>
							</div>
							<div class="options">
								{$ddmMotherId} {$ddmMotherIdError}
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>
									<label for="birthDateDate">{$lblBirthDate|ucfirst}
										<abbr title="{$lblRequiredField}">*</abbr></label>
								</h3>
							</div>
							<div class="options">
								<p>{$txtBirthDate} {$txtBirthDateError}</p>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>
									<label for="descriptionBefore">{$lblDescriptionBefore|ucfirst}
								</h3>
							</div>
							<div class="options">
								<p>{$txtDescriptionBefore} {$txtDescriptionBeforeError}</p>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>
									<label for="descriptionAfter">{$lblDescriptionAfter|ucfirst}
								</h3>
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
	</div>
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}