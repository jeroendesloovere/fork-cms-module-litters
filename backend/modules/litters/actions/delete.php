<?php

	/*
	 * This file is part of Fork CMS.
	 *
	 * For the full copyright and license information, please view the license
	 * file that was distributed with this source code.
	 */

	/**
	 * This is the delete-action, it deletes an item
	 *
	 * @author Yohann Bianchi <sbooob@gmail.com>
	 */
	class BackendLittersDelete extends BackendBaseActionDelete{
		/**
		 * Execute the action
		 */
		public
		function execute(){
			$this->id = $this->getParameter('id', 'int');

			// does the item exist
			if($this->id !== null && BackendLittersModel::exists($this->id)){
				parent::execute();
				$this->record = (array)BackendLittersModel::get($this->id);

				BackendLittersModel::delete($this->id);

				BackendModel::triggerEvent(
				            $this->getModule(), 'after_delete',
				            array('id' => $this->id)
				);

				$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['title']));
			}
			else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}
	}
