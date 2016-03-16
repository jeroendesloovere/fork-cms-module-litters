<?php

    /*
     * This file is part of Fork CMS.
     *
     * For the full copyright and license information, please view the license
     * file that was distributed with this source code.
     */
    use Symfony\Component\Filesystem\Filesystem;

    /**
     * This is the edit-action, it will display a form with the item data to edit
     *
     * @author Yohann Bianchi <sbooob@gmail.com>
     */
    class BackendLittersEditParent extends BackendBaseActionEdit
    {
        /**
         * Execute the action
         */
        public function execute()
        {
            parent::execute();

            $this->getData();
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
            if ($this->id == null || !BackendLittersModel::exists($this->id, 'litters_parents')) {
                $this->redirect(BackendModel::createURLForAction('index_parents') . '&error=non-existing');
            }

            $this->record = BackendLittersModel::get($this->id, 'litters_parents');
        }

        /**
         * Load the form
         */
        protected function loadForm()
        {
            $this->frm = new BackendForm('edit');

            // add the affix position radios
            $affixPositions = array(
                array(
                    'label' => BL::getLabel('Prepend', 'litters'),
                    'value' => 'PREPEND'
                ),
                array(
                    'label' => BL::getLabel('Append', 'litters'),
                    'value' => 'APPEND'
                )
            );

            $sexes = array(
                array(
                    'label' => BL::getLabel('Male', 'litters'),
                    'value' => 'M',
                ),
                array(
                    'label' => BL::getLabel('Female', 'litters'),
                    'value' => 'F',
                ),
            );

            $this->frm->addText('name', $this->record['name'], 255, 'inputText title', 'inputTextError title');
            $this->frm->addText('affix', $this->record['affix'], 255);
            $this->frm->addRadiobutton('affix_position', $affixPositions, $this->record['affix_position']);
            $this->frm->addDate('birth_date', $this->record['birth_date']);
            $this->frm->addRadiobutton('sex', $sexes, $this->record['sex']);
            $this->frm->addText('color', $this->record['color'], 100);
            $this->frm->addText('personal_page_url', $this->record['url'], 255);
            $this->frm->addImage('photo');
            if ($this->record['photo_url'] !== null) {
                $this->tpl->assign('photo_url', $this->record['photo_url']);
            }

            // meta
            $this->meta = new BackendMeta($this->frm, null, 'name', true);
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

            $this->tpl->assign('item', $this->record);
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
                $fields['affix']->isFilled(BL::err('FieldIsRequired'));
                $fields['affix_position']->isFilled(BL::err('FieldIsRequired'));
                $fields['birth_date']->isFilled(BL::err('FieldIsRequired'));
                $fields['birth_date']->isValid(BL::err('DateIsInvalid'));
                $fields['sex']->isFilled(BL::err('FieldIsRequired'));

                // validate photo
                if ($fields['photo']->isFilled()) {
                    // correct extension
                    if ($fields['photo']->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly'))) {
                        // correct mimetype?
                        $fields['photo']->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
                    }
                }

                // validate meta
                $this->meta->validate();

                if ($this->frm->isCorrect()) {
                    $item = array(
                        'id'             => $this->id,
                        'language'       => BL::getWorkingLanguage(),
                        'name'           => $fields['name']->getValue(),
                        'affix'          => $fields['affix']->getValue(),
                        'affix_position' => $fields['affix_position']->getValue(),
                        'sex'             => $this->frm->getField('sex')->getValue(),
                        'birth_date'     => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('birth_date'))),
                        'color'          => $fields['color']->getValue(),
                        'url'            => $fields['personal_page_url']->getValue(),
                        'color'          => $fields['color']->getValue(),
                        'meta_id'        =>$this->meta->save(),
                    );

                    // handle photo
                    if ($fields['photo']->isFilled()) {
                        // remove previous
                        if (!empty($this->record['photo_url'])) {
                            $fs = new Filesystem();
                            $fs->remove(PATH_WWW . $this->record['photo_url']);
                        }

                        // save new
                        $photo_url = "/litters/original/parents/{$item['id']}.{$fields['photo']->getExtension()}";
                        if (!$fields['photo']->moveFile(FRONTEND_FILES_PATH . $photo_url)) {
                            $fields['photo']->setError(BL::err('CannotProcessPhoto'));

                            // Revalidate the form to process the error just set for the photo field
                            $this->frm->isCorrect(true);
                        }

                        // add into item to insert
                        $item['photo_url'] = '/frontend/files' . $photo_url;
                    }

                    BackendLittersModel::update($item, 'litters_parents');

                    // add search index
                    BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('name' => $item['name']));

                    BackendModel::triggerEvent($this->getModule(), 'after_edit_parent', $item);
                    $this->redirect(BackendModel::createURLForAction('index_parents') . '&report=edited&highlight=row-' . $item['id']);
                }
            }
        }
    }
