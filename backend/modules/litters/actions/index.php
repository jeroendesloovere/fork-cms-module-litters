<?php

	/*
	 * This file is part of Fork CMS.
	 *
	 * For the full copyright and license information, please view the license
	 * file that was distributed with this source code.
	 */

	/**
	 * This is the index-action (default), it will display the overview of Litters posts
	 *
	 * @author Yohann Bianchi <sbooob@gmail.com>
	 */
	class BackendLittersIndex extends BackendBaseActionIndex{
		/**
		 * Execute the action
		 */
		public function execute(){
			parent::execute();
			$this->loadDataGrid();

			$this->parse();
			$this->display();
		}

		/**
		 * Load the dataGrid
		 */
		protected function loadDataGrid(){
			$this->dataGrid = new BackendDataGridDB(BackendLittersModel::QRY_DATAGRID_BROWSE, BL::getWorkingLanguage());

			// reform date
			$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[created_on]'), 'created_on', true);

			// drag and drop sequencing
			$this->dataGrid->enableSequenceByDragAndDrop();

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('edit')){
				$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
				$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			}
		}

		/**
		 * Parse the page
		 */
		protected function parse(){
			// parse the dataGrid if there are results
			$this->tpl->assign('dataGrid', (string)$this->dataGrid->getContent());
		}
	}
