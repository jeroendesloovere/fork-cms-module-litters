<?php

class YoungForm extends BackendForm{
	public function __construct($name = null, $action = null, $method = 'post', $useToken = true, $useGlobalError = true){
		parent::__construct($name, $action, $method, $useToken, $useGlobalError);

		$sexes = array(
			array(
				'label' => BL::getLabel('Undetermined', 'litters'),
				'value' => '',
			),
			array(
				'label' => BL::getLabel('Male', 'litters'),
				'value' => 'M',
			),
			array(
				'label' => BL::getLabel('Female', 'litters'),
				'value' => 'F',
			),
		);

		$this->addHidden('young_id', null, 255);
		$this->addText('code_name', null, 255);
		$this->addText('young_name', null, 255);
		$this->addRadiobutton('sex', $sexes);
		$this->addText('color', null, 255);
		$this->addText('gallery_url', null, 255);
		$this->addImage('photo');
	}
} 