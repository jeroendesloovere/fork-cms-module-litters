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

		$rawAvailabilities = BackendLittersModel::getEnumAllowedValues('litters_youngs', 'availability');
		$availabilities = array();
		foreach($rawAvailabilities as $availability){
			$availabilities[$availability] = ucfirst(BL::getLabel('Availability' . ucfirst(strtolower($availability)), 'litters'));
		}

		$rawQualities = BackendLittersModel::getEnumAllowedValues('litters_youngs', 'quality');
		$qualities = array();
		foreach($rawQualities as $quality){
			$qualities[$quality] = ucfirst(BL::getLabel('Quality' . ucfirst(strtolower($quality)), 'litters'));
		}

		$this->addHidden('young_id', null, 255);
		$this->addText('code_name', null, 255);
		$this->addText('young_name', null, 255);
		$this->addRadiobutton('sex', $sexes);
		$this->addText('color', null, 255);
		$this->addText('ems_code', null, 255);
		$this->addDropdown('availability', $availabilities);
		$this->addDropdown('quality', $qualities);
		$this->addText('gallery_url', null, 255);
		$this->addImage('photo');
	}
} 