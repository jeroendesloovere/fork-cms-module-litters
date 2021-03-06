<?php

	/*
	 * This file is part of Fork CMS.
	 *
	 * For the full copyright and license information, please view the license
	 * file that was distributed with this source code.
	 */

	/**
	 * This is the add_young-action, it will display a form to create a new item
	 *
	 * @author Yohann Bianchi <sbooob@gmail.com>
	 */
	class BackendLittersAddYoung extends BackendBaseActionAdd{
		/**
		 * Execute the actions
		 */
		public function execute(){
			parent::execute();

			$this->loadForm();
			$this->validateForm();
		}

		/**
		 * Load the form
		 */
		protected function loadForm(){
			require_once(BACKEND_MODULE_PATH . '/forms/YoungForm.php');
			$this->frm = new YoungForm('young');
			$this->frm->addHidden('litter_id');
		}

		/**
		 * Validate the form
		 */
		protected function validateForm(){
			$this->litterId = SpoonFilter::getPostValue('litter_id', null, 'int');

			if($this->frm->isSubmitted()){
				$this->frm->cleanupFields();
				$fields = $this->frm->getFields();

				// validation
				$fields['code_name']->isFilled(BL::err('FieldIsRequired'));

				// validate photo
				if($fields['photo']->isFilled()){
					// correct extension
					if($fields['photo']->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly'))){
						// correct mimetype?
						$fields['photo']->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
					}
				}

				if($this->frm->isCorrect()){
					// build the item
					$sex = $fields['sex']->getValue();
					$item = array(
						'litter_id'		=> $this->litterId,
						'language'		=> BL::getWorkingLanguage(),
						'code_name'		=> $fields['code_name']->getValue(),
						'name'			=> $fields['young_name']->getValue(),
						'sex'			=> $sex == '' ? NULL : $sex,
						'color'			=> $fields['color']->getValue(),
						'ems_code'		=> $fields['ems_code']->getValue(),
						'availability'  => $fields['availability']->getValue(),
						'quality'		=> $fields['quality']->getValue(),
						'url'			=> $fields['gallery_url']->getValue(),
						'sequence'		=> BackendLittersModel::getMaximumSequence() + 1,
					);

					// handle photo
					if($fields['photo']->isFilled()){
						$photo_url = "/litters/original/litters/${item['litter_id']}/tmp_" . rand(0, 99) . ".{$fields['photo']->getExtension()}";
						if(!$fields['photo']->moveFile(FRONTEND_FILES_PATH . $photo_url)){
							$fields['photo']->setError(BL::err('CannotProcessPhoto'));

							// Revalidate the form to process the error just set for the photo field
							$this->frm->isCorrect(true);
						}

						// add into item to insert
						$item['photo_url'] = '/frontend/files' . $photo_url;
					}

					// insert it
					$item['id'] = BackendLittersModel::insert($item, 'litters_youngs');

					if($fields['photo']->isFilled()){
						// move the picture to its definitive location & update the db record
						$item['photo_url'] = "/litters/original/litters/{$item['litter_id']}/{$item['id']}.{$fields['photo']->getExtension()}";
						if(rename(FRONTEND_FILES_PATH . $photo_url, FRONTEND_FILES_PATH . $item['photo_url'])){
							$item['photo_url'] = '/frontend/files' . $item['photo_url'];
							BackendLittersModel::update($item, 'litters_youngs');
						}
					}

					BackendModel::triggerEvent($this->getModule(), 'after_add_young', $item);
					BackendModel::triggerEvent($this->getModule(), 'after_edit', BackendLittersModel::get($item['litter_id']));
					$this->redirect(BackendModel::createURLForAction('edit') . "&id={$item['litter_id']}&report=added&highlight=row-{$item['id']}#tabYoungs");
				}
			}
		}
	}
