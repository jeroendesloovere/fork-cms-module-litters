<?php

	/**
	 * Fetches a litters_youngs record & return it in JSON
	 *
	 * @author Yohann Bianchi <sbooob@gmail.com>
	 */
	class BackendLittersAjaxGetYoung extends BackendBaseAJAXAction{
		public
		function execute(){
			parent::execute();

			// get parameter
			$id = trim(SpoonFilter::getPostValue('id', null, '', 'int'));
			$record = BackendLittersModel::get($id, 'litters_youngs');

			if(!empty($record)) $this->output(self::OK, $record, 'sequence updated');
			else                $this->output(404, null, 'not found');
		}
	}
