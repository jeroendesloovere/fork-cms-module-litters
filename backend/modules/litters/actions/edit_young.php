<?php

    /**
     * This is the add_young-action, it will display a form to create a new item
     *
     * @author Yohann Bianchi <sbooob@gmail.com>
     */
    use Symfony\Component\Filesystem\Filesystem;

    class BackendLittersEditYoung extends BackendBaseActionAdd
    {
        /**
         * Execute the actions
         */
        public function execute()
        {
            parent::execute();

            $this->loadForm();
            $this->getData();
            $this->validateForm();
        }

        /**
         * Load the form
         */
        protected function loadForm()
        {
            require_once(BACKEND_MODULE_PATH . '/forms/YoungForm.php');
            $this->frm = new YoungForm('young');
            $this->frm->addHidden('litter_id');
        }

        /**
         * Load the item data
         */
        protected function getData()
        {
            $this->id = SpoonFilter::getPostValue('young_id', null, 'int');
            $this->litterId = SpoonFilter::getPostValue('litter_id', null, 'int');
            if ($this->id == null || !BackendLittersModel::exists($this->id, 'litters_youngs')) {
                $this->redirect(BackendModel::createURLForAction('edit') . '&error=non-existing&id=' . $this->litterId);
            }

            $this->record = BackendLittersModel::get($this->id, 'litters_youngs');
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
                $fields['code_name']->isFilled(BL::err('FieldIsRequired'));

                // validate photo
                if ($fields['photo']->isFilled()) {
                    // correct extension
                    if ($fields['photo']->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly'))) {
                        // correct mimetype?
                        $fields['photo']->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
                    }
                }

                if ($this->frm->isCorrect()) {
                    // build the item
                    $sex  = $fields['sex']->getValue();
                    $item = array(
                        'id'  => $this->id,
                        'litter_id'        => $this->litterId,
                        'language'        => BL::getWorkingLanguage(),
                        'code_name'        => $fields['code_name']->getValue(),
                        'name'            => $fields['young_name']->getValue(),
                        'sex'            => $sex === '' ? null : $sex,
                        'color'            => $fields['color']->getValue(),
                        'ems_code'        => $fields['ems_code']->getValue(),
                        'availability'  => $fields['availability']->getValue(),
                        'quality'        => $fields['quality']->getValue(),
                        'url'            => $fields['gallery_url']->getValue(),
                    );

                    // handle photo
                    if ($fields['photo']->isFilled()) {
                        // remove previous
                        if (!empty($this->record['photo_url'])) {
                            $fs = new Filesystem();
                            $fs->remove(PATH_WWW . $this->record['photo_url']);
                        }
                        $photo_url = "/litters/original/litters/${item['litter_id']}/{$item['id']}.{$fields['photo']->getExtension()}";
                        if (!$fields['photo']->moveFile(FRONTEND_FILES_PATH . $photo_url)) {
                            $fields['photo']->setError(BL::err('CannotProcessPhoto'));

                            // Revalidate the form to process the error just set for the photo field
                            $this->frm->isCorrect(true);
                        }

                        // add into item to insert
                        $item['photo_url'] = '/frontend/files' . $photo_url;
                    }

                    // insert it
                    $item['id'] = BackendLittersModel::update($item, 'litters_youngs');

                    BackendModel::triggerEvent($this->getModule(), 'after_add_young', $item);
                    BackendModel::triggerEvent($this->getModule(), 'after_edit', BackendLittersModel::get($item['litter_id']));
                    $this->redirect(BackendModel::createURLForAction('edit') . "&id={$item['litter_id']}&report=edited&highlight=row-{$item['id']}#tabYoungs");
                }
                $this->redirect(BackendModel::createURLForAction('edit') . "&id={$this->litterId}&error=codeNameIsRequired&highlight=row-{$this->id}#tabYoungs");
            }
        }
    }
