<?php

	/*
	 * This file is part of Fork CMS.
	 *
	 * For the full copyright and license information, please view the license
	 * file that was distributed with this source code.
	 */

	/**
	 * This is the add-action, it will display a form to create a new item
	 *
	 * @author Yohann Bianchi <sbooob@gmail.com>
	 */
	class BackendLittersAdd extends BackendBaseActionAdd{
		/**
		 * Execute the actions
		 */
		public
		function execute(){
			parent::execute();

			$this->loadForm();
			$this->validateForm();

			$this->parse();
			$this->display();
		}

		/**
		 * Load the form
		 */
		protected
		function loadForm(){
			$this->frm = new BackendForm('add');

			$this->frm->addHidden('uploaded_images');
			$this->frm->addText('name', null, null, 'inputText title', 'inputTextError title');

			// build arrays with options for the father & mother dropdowns
			$parents              = BackendLittersModel::getAllParents();
			$dropdownFatherValues = $dropdownMotherValues = array();
			foreach($parents as $parent){
				if($parent['sex'] === 'M') $dropdownFatherValues[$parent['id']] = $parent['name'];
				elseif($parent['sex'] === 'F') $dropdownMotherValues[$parent['id']] = $parent['name'];
			}

			$this->frm->addDropdown('father_id', $dropdownFatherValues)->setDefaultElement('');
			$this->frm->addDropdown('mother_id', $dropdownMotherValues)->setDefaultElement('');

			// add date field
			$this->frm->addDate('birth_date');

			// meta
			$this->meta = new BackendMeta($this->frm, null, 'name', true);
		}

		/**
		 * Parse the page
		 */
		protected
		function parse(){
			parent::parse();

			// get url
			$url    = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
			$url404 = BackendModel::getURL(404);

			// parse additional variables
			if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
		}

		/**
		 * Validate the form
		 */
		protected
		function validateForm(){
			if($this->frm->isSubmitted()){
				$this->frm->cleanupFields();

				// validation
				$fields = $this->frm->getFields();

				$fields['name']->isFilled(BL::err('FieldIsRequired'));
				$fields['father_id']->isFilled(BL::err('FieldIsRequired'));
				$fields['mother_id']->isFilled(BL::err('FieldIsRequired'));
				$fields['birth_date']->isFilled(BL::err('FieldIsRequired'));
				$fields['birth_date']->isValid(BL::err('DateIsInvalid'));

				// validate meta
				$this->meta->validate();

				if($this->frm->isCorrect()){
					// build the item
					$item['language']   = BL::getWorkingLanguage();
					$item['name']       = $fields['name']->getValue();
					$item['father_id']  = $fields['father_id']->getValue();
					$item['mother_id']  = $fields['mother_id']->getValue();
					$item['birth_date'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('birth_date')));
					$item['sequence']   = BackendLittersModel::getMaximumSequence() + 1;

					$item['meta_id'] = $this->meta->save();

					// insert it
					$item['id'] = BackendLittersModel::insert($item);

					// add search index
					BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('name' => $item['name']));

					BackendModel::triggerEvent($this->getModule(), 'after_add', $item);
					$this->redirect(BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']);
				}
			}
		}
	}
