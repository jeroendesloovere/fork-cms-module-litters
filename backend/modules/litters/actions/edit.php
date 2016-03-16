<?php

    /*
     * This file is part of Fork CMS.
     *
     * For the full copyright and license information, please view the license
     * file that was distributed with this source code.
     */

    /**
     * This is the edit-action, it will display a form with the item data to edit
     *
     * @author Yohann Bianchi <sbooob@gmail.com>
     */
    class BackendLittersEdit extends BackendBaseActionEdit
    {
        /**
         * The youngs datagrid
         *
         * @var	SpoonDataGrid
         */
        protected $dgYoungs;

        /**
         * The add young form
         *
         * @var SpoonForm
         */
        protected $youngForm;

        /**
         * Execute the action
         */
        public function execute()
        {
            parent::execute();

            $this->getData();
            $this->dgYoungs = $this->loadDatagrid();
            $this->loadYoungForm();
            $this->loadForm();
            $this->validateForm();

            $this->parse();
            $this->display();
        }

        /**
         * Load the item data
         */
        protected function getData()
        {
            $this->id = $this->getParameter('id', 'int', null);
            if ($this->id == null || !BackendLittersModel::exists($this->id)) {
                $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
            }

            $this->record = BackendLittersModel::get($this->id);
        }

        /**
         * Load the form
         */
        protected function loadForm()
        {
            // create form
            $this->frm = new BackendForm('edit');

            $this->frm->addText('name', $this->record['name'], 255, 'inputText title', 'inputTextError title');

            // build arrays with options for the father & mother dropdowns
            $parents              = BackendLittersModel::getAllParents();
            $dropdownFatherValues = $dropdownMotherValues = array();
            foreach ($parents as $parent) {
                if ($parent['sex'] === 'M') {
                    $dropdownFatherValues[$parent['id']] = $parent['name'];
                } elseif ($parent['sex'] === 'F') {
                    $dropdownMotherValues[$parent['id']] = $parent['name'];
                }
            }

            $this->frm->addDropdown('father_id', $dropdownFatherValues)->setSelected($this->record['father_id']);
            $this->frm->addDropdown('mother_id', $dropdownMotherValues)->setSelected($this->record['mother_id']);

            // add date field
            $this->frm->addDate('birth_date', $this->record['birth_date']);

            // add editors
            $this->frm->addEditor('description_before', $this->record['description_before']);
            $this->frm->addEditor('description_after', $this->record['description_after']);

            // meta
            $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'name', true);
            $this->meta->setUrlCallBack('BackendLittersModel', 'getUrl', array($this->record['id']));
        }

        protected function loadYoungForm()
        {
            require_once(BACKEND_MODULE_PATH . '/forms/YoungForm.php');
            $this->youngForm = new YoungForm('young', BackendModel::createURLForAction('edit_young') . '&litter_id=' . $this->id);
            $this->youngForm->addHidden('litter_id', $this->record['id']);
        }

        /**
         * Parse the page
         */
        protected function parse()
        {
            parent::parse();

            // get url
            $url    = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
            $url404 = BackendModel::getURL(404);

            // parse additional variables
            if ($url404 != $url) {
                $this->tpl->assign('detailURL', SITE_URL . $url);
            }

            // fetch proper slug
            $this->record['url'] = $this->meta->getURL();

            $this->tpl->assign('item', $this->record);
            $datagrid = $this->dgYoungs->getContent();
            if ($datagrid !== null) {
                $this->tpl->assign('dgYoungs', $datagrid);
            }

            if ($this->youngForm) {
                $this->youngForm->parse($this->tpl);
            }
        }

        /**
         * Validate the form
         */
        protected function validateForm()
        {
            if ($this->frm->isSubmitted()) {
                $this->frm->cleanupFields();
                $fields = $this->frm->getFields();

                // validation
                $fields['name']->isFilled(BL::err('FieldIsRequired'));
                $fields['father_id']->isFilled(BL::err('FieldIsRequired'));
                $fields['mother_id']->isFilled(BL::err('FieldIsRequired'));
                $fields['birth_date']->isFilled(BL::err('FieldIsRequired'));
                $fields['birth_date']->isValid(BL::err('DateIsInvalid'));

                // validate meta
                $this->meta->validate();

                if ($this->frm->isCorrect()) {
                    $item['id']       = $this->id;
                    $item['language'] = BL::getWorkingLanguage();

                    $item['name']                = $fields['name']->getValue();
                    $item['father_id']            = $fields['father_id']->getValue();
                    $item['mother_id']            = $fields['mother_id']->getValue();
                    $item['birth_date']            = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('birth_date')));
                    $item['description_before']    = $fields['description_before']->getValue();
                    $item['description_after']    = $fields['description_after']->getValue();
                    $item['sequence']            = BackendLittersModel::getMaximumSequence() + 1;

                    $item['meta_id'] = $this->meta->save();

                    BackendLittersModel::update($item);
                    $item['id'] = $this->id;

                    // add search index
                    BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('name' => $item['name']));

                    BackendModel::triggerEvent($this->getModule(), 'after_edit', $item);
                    $this->redirect(BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']);
                }
            }
        }

        /**
         * Load the dataGrid
         */
        protected function loadDataGrid()
        {
            $datagrid = new BackendDataGridDB(BackendLittersModel::QRY_DATAGRID_BROWSE_YOUNGS, array(BL::getWorkingLanguage(), $this->id));
            // reform date
            $datagrid->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
            $datagrid->setColumnFunction(array('BackendLittersHelper', 'createImgTag'), array('[photo]', '', 100), 'photo', true);

            // drag and drop sequencing
            $datagrid->enableSequenceByDragAndDrop();

            // check if this action is allowed
            if (BackendAuthentication::isAllowedAction('edit')) {
                $datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_young') . '&amp;id=[id]', BL::lbl('Edit'));
                $datagrid->setColumnURL('name', BackendModel::createURLForAction('edit_young') . '&amp;id=[id]');
                $datagrid->addColumn('delete', null, BL::lbl('Delete'), BackendModel::createURLForAction('delete_young') . '&amp;id=[id]', BL::lbl('Delete'));
                $datagrid->setColumnURL('name', BackendModel::createURLForAction('delete_young') . '&amp;id=[id]');
            }

            return $datagrid;
        }
    }
