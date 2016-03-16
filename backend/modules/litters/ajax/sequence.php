<?php

    /**
     * Alters the sequence of Litters articles
     *
     * @author Yohann Bianchi <sbooob@gmail.com>
     */
    class BackendLittersAjaxSequence extends BackendBaseAJAXAction
    {
        public function execute()
        {
            parent::execute();

            // get parameters
            $newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

            // list id
            $ids = (array)explode(',', rtrim($newIdSequence, ','));

            // loop id's and set new sequence
            foreach ($ids as $i => $id) {
                $item['id']       = $id;
                $item['sequence'] = $i + 1;

                // update sequence
                if (BackendLittersModel::exists($id)) {
                    BackendLittersModel::update($item);
                }
            }

            // success output
            $this->output(self::OK, null, 'sequence updated');
        }
    }
