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
class FrontendLittersDetail extends FrontendBaseBlock
{
	/**
	 * The record
	 *
	 * @var	array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get record
		$this->record = FrontendLittersModel::getLitter($this->URL->getParameter(1));

		// check if record is not empty
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		$this->youngs = FrontendLittersModel::getYoungs($this->record['id']);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		/**
		 * @TODO add specified image
		 * $this->header->addOpenGraphImage(FRONTEND_FILES_URL . '/litters/images/source/' . $this->record['image']);
		 */

		// build Facebook  OpenGraph data
		$this->header->addOpenGraphData('title', $this->record['meta_title'], true);
		$this->header->addOpenGraphData('type', 'article', true);
		$this->header->addOpenGraphData('url', SITE_URL . FrontendNavigation::getURLForBlock('litters', 'detail') . '/' . $this->record['url'], true);
		$this->header->addOpenGraphData('site_name', FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE), true);
		$this->header->addOpenGraphData('description', $this->record['meta_title'], true);

		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['meta_title']);

		// hide action title
		$this->tpl->assign('hideContentTitle', true);

		// show title linked with the meta title
		$this->tpl->assign('title', $this->record['name']);

		// set meta
		$this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->record['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index']));
		if(isset($this->record['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow']));

		// assign item
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('youngs', $this->youngs);

		$this->tpl->mapModifier('createimage', array('FrontendLittersHelper', 'createImage'));
	}
}
