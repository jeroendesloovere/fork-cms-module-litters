jsBackend.littersEdit = {
	youngFormDialog: null,
	addYoungButton:  null,
	youngForm:       null,
	self:            this,

	// initialization
	init: function (){
		//		jsBackend.littersEdit.initAjax();
		jsBackend.littersEdit.initVars();
		jsBackend.littersEdit.bindDialog();
		jsBackend.littersEdit.bindButtons();
	},

	initVars: function (){
		self.addYoungButton = $('#addYoungButton');
		self.editYoungButtons = $('.actionEdit > a');
		self.youngFormDialog = $('#youngFormDialog');
		self.youngForm = $('#young');
	},

	bindDialog: function (){
		self.youngFormDialog.dialog({
			autoOpen:    false,
			draggable:   false,
			resizable:   false,
			modal:       true,
			width:       400,
			buttons:     [
				{
					text:  utils.string.ucfirst(jsBackend.locale.lbl('Save')),
					click: function (){
						self.youngForm.submit();
					}
				},
				{
					text:  utils.string.ucfirst(jsBackend.locale.lbl('Cancel')),
					click: function (e){
						$(this).dialog('close');
					}
				}
			],
			open: function (e){
				// focus on first input element
				if($(this).find(':input:visible').length > 0) $(this).find(':input:visible')[0].focus();
			},

			// before closing the dialog
			beforeclose: function (e){
				var form = self.youngForm[0];
				form.reset();
				$('#photoPreview', form).removeAttr('src')
										.hide(0);
			}
		});
	},

	bindButtons: function (){
		self.addYoungButton.click(function (e){
			e.preventDefault();
			self.youngFormDialog.dialog('option', 'title', self.addYoungButton.attr('title'));
			self.youngForm.attr('action', self.addYoungButton.attr('href'));
			self.youngFormDialog.dialog('open');
		});

		self.editYoungButtons.click(function (e){
			var button;
			e.preventDefault();

			$.ajax({
				data: {
					fork: {
						module:   'litters',
						action:   'get_young',
						language: jsBackend.current.language
					},
					id:   $(this).parents('tr').first().attr('data-id')
				},

				success: function (data, txtStatus, jqXHR){
					var form = self.youngForm[0];
					form.young_id.value		= data.data.id;
					form.code_name.value	= data.data.code_name;
					form.young_name.value	= data.data.name;
					form.color.value		= data.data.color;
					form.ems_code.value		= data.data.ems_code;
					form.availability.value	= data.data.availability;
					form.quality.value		= data.data.quality;
					form.gallery_url.value	= data.data.url;
					$('input[name=sex][value=' + data.data.sex + ']', form).prop('checked', true);

					if(data.data.photo_url && data.data.photo_url.length > 0){
						$('#photoPreview', form).attr('src', data.data.photo_url)
												.show(0);
					}
					self.youngFormDialog.dialog('option', 'title', jsBackend.locale.lbl('EditYoung'));
					self.youngForm.attr('action', $(button).attr('href'));
					self.youngFormDialog.dialog('open');
				}
			});
		});
	},

	// define the end of the object
	eoo:         true
};

$(jsBackend.littersEdit.init);