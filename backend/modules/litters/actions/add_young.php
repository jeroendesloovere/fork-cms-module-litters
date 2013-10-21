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
			if($this->frm->isSubmitted()){
				$this->frm->cleanupFields();
				$fields = $this->frm->getFields();

				// validation
				$fields['code_name']->isFilled(BL::err('FieldIsRequired'));
				$fields['litter_id']->isFilled(BL::err('FieldIsRequired'));

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
						'litter_id'      => $fields['litter_id']->getValue(),
						'language'       => BL::getWorkingLanguage(),
						'code_name'      => $fields['code_name']->getValue(),
						'name'           => $fields['young_name']->getValue(),
						'sex'            => $sex == '' ? NULL : $sex,
						'color'          => $fields['color']->getValue(),
						'url'            => $fields['gallery_url']->getValue(),
						'sequence'       => BackendLittersModel::getMaximumSequence() + 1,
					);

					// handle photo
					if($fields['photo']->isFilled()){
						$photo_url = '/litters/' . $item['litter_id'] . '/photos/source/' . rand(0, 3) . '_' . BackendLittersHelper::sanitizeFilename($item['code_name']) . '.' . $fields['photo']->getExtension();
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

					BackendModel::triggerEvent($this->getModule(), 'after_add_young', $item);
					BackendModel::triggerEvent($this->getModule(), 'after_edit', BackendLittersModel::get($item['litter_id']));
					$this->redirect(BackendModel::createURLForAction('edit') . "&id={$item['litter_id']}&report=added&highlight=row-{$item['id']}#tabYoungs");
				}
			}
		}
	}
