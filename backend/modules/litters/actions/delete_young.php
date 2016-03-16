<?php

    use \Symfony\Component\Filesystem\Filesystem;
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
    class BackendLittersDeleteYoung extends BackendBaseActionDelete
    {
        /**
         * Execute the action
         */
        public function execute()
        {
            $this->id = $this->getParameter('id', 'int');

            // does the item exist
            if ($this->id !== null && BackendLittersModel::exists($this->id, 'litters_youngs')) {
                parent::execute();
                $this->record = (array)BackendLittersModel::get($this->id, 'litters_youngs');

                // remove previous
                if (!empty($this->record['photo_url'])) {
                    $fs = new Filesystem();
                    $fs->remove(PATH_WWW . $this->record['photo_url']);
                }

                BackendLittersModel::delete($this->id, 'litters_youngs');

                BackendModel::triggerEvent($this->getModule(), 'after_delete_young', array('id' => $this->id));

                $this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $this->record['litter_id'] . '&report=deleted&var=' . urlencode($this->record['code_name']) . '#tabYoungs');
            } else {
                $this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $this->record['litter_id'] . '&error=non-existing#tabYoungs');
            }
        }
    }
