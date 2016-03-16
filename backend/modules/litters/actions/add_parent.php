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
    class BackendLittersAddParent extends BackendBaseActionAdd
    {
        /**
         * Execute the actions
         */
        public function execute()
        {
            parent::execute();

            $this->loadForm();
            $this->validateForm();

            $this->parse();
            $this->display();
        }

        /**
         * Load the form
         */
        protected function loadForm()
        {
            $this->frm = new BackendForm('add');

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

            $this->frm->addText('name', null, 255, 'inputText title', 'inputTextError title');
            $this->frm->addText('affix', null, 255);
            $this->frm->addRadiobutton('affix_position', $affixPositions, 'APPEND');
            $this->frm->addDate('birth_date');
            $this->frm->addRadiobutton('sex', $sexes, 'M');
            $this->frm->addText('color', null, 100);
            $this->frm->addText('personal_page_url', null, null);
            $this->frm->addImage('photo');

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
                if (!in_array($fields['affix_position']->getValue(), array('PREPEND', 'APPEND'))) {
                    $fields['affix_position']->setError(BL::err('AffixPositionIsInvalid'));
                }
                $fields['birth_date']->isFilled(BL::err('FieldIsRequired'));
                $fields['birth_date']->isValid(BL::err('DateIsInvalid'));
                $fields['sex']->isFilled(BL::err('FieldIsRequired'));
                if (!in_array($fields['sex']->getValue(), array('M', 'F'))) {
                    $fields['sex']->setError(BL::err('SexIsInvalid'));
                }

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
                    // build the item
                    $item = array(
                        'language'       => BL::getWorkingLanguage(),
                        'name'           => $fields['name']->getValue(),
                        'affix'          => $fields['affix']->getValue(),
                        'affix_position' => $fields['affix_position']->getValue(),
                        'birth_date'     => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('birth_date'))),
                        'color'          => $fields['color']->getValue(),
                        'url'            => $fields['personal_page_url']->getValue(),
                        'sequence'       => BackendLittersModel::getMaximumSequence() + 1,
                    );

                    // handle photo
                    if ($fields['photo']->isFilled()) {
                        $photo_url = "/litters/original/parents/tmp_" . rand(0, 99) . ".{$fields['photo']->getExtension()}";
                        if (!$fields['photo']->moveFile(FRONTEND_FILES_PATH . $photo_url)) {
                            $fields['photo']->setError(BL::err('CannotProcessPhoto'));

                            // Revalidate the form to process the error just set for the photo field
                            $this->frm->isCorrect(true);
                        }

                        // add into item to insert
                        $item['photo_url'] = '/frontend/files' . $photo_url;
                    }

                    $item['meta_id'] = $this->meta->save();

                    // insert it
                    $item['id'] = BackendLittersModel::insert($item, 'litters_parents');

                    if ($fields['photo']->isFilled()) {
                        // move the picture to its definitive location & update the db record
                        $item['photo_url'] = "/litters/original/parents/{$item['id']}.{$fields['photo']->getExtension()}";
                        if (rename(FRONTEND_FILES_PATH . $photo_url, FRONTEND_FILES_PATH . $item['photo_url'])) {
                            $item['photo_url'] = '/frontend/files' . $item['photo_url'];
                            BackendLittersModel::update($item, 'litters_parents');
                        }
                    }

                    // add search index
                    BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('name' => $item['name']));

                    BackendModel::triggerEvent($this->getModule(), 'after_add_parent', $item);
                    $this->redirect(BackendModel::createURLForAction('index_parents') . '&report=added&highlight=row-' . $item['id']);
                }
            }
        }
    }
