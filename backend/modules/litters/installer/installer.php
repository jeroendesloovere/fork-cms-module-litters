<?php

	/*
	 * This file is part of Fork CMS.
	 *
	 * For the full copyright and license information, please view the license
	 * file that was distributed with this source code.
	 */

	/**
	 * Installer for the Litters module
	 *
	 * @author Yohann Bianchi <sbooob@gmail.com>
	 */
	class LittersInstaller extends ModuleInstaller{
		public
		function install(){
			// import the sql
			$this->importSQL(dirname(__FILE__) . '/data/install.sql');

			// install the module in the database
			$this->addModule('litters');

			// install the locale, this is set here beceause we need the module for this
			$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

			$this->setModuleRights(1, 'litters');

			$this->setActionRights(1, 'litters', 'index');
			$this->setActionRights(1, 'litters', 'add');
			$this->setActionRights(1, 'litters', 'edit');
			$this->setActionRights(1, 'litters', 'delete');
			$this->setActionRights(1, 'litters', 'sequence');
			$this->setActionRights(1, 'litters', 'categories');
			$this->setActionRights(1, 'litters', 'add_category');
			$this->setActionRights(1, 'litters', 'edit_category');
			$this->setActionRights(1, 'litters', 'delete_category');
			$this->setActionRights(1, 'litters', 'sequence_categories');

			$this->insertExtra('litters', 'block', 'LittersCategory', 'category', null, 'N', 1002);
			$this->insertExtra('litters', 'widget', 'Categories', 'categories', null, 'N', 1003);

			// copy the qqFileUploader needed for multiple fileupload
			if(!SpoonFile::exists(PATH_LIBRARY . '/external/qqFileUploader.php')){
				copy(dirname(__FILE__) . '/data/qqFileUploader.php', PATH_LIBRARY . '/external/qqFileUploader.php');
			}

			// add extra's
			$subnameID = $this->insertExtra('litters', 'block', 'Litters', null, null, 'N', 1000);
			$this->insertExtra('litters', 'block', 'LittersDetail', 'detail', null, 'N', 1001);

			$navigationModulesId = $this->setNavigation(null, 'Modules');
			$navigationLittersId = $this->setNavigation($navigationModulesId, 'Litters');
			$this->setNavigation(
			     $navigationLittersId, 'Litters', 'litters/index',
			     array('litters/add', 'litters/edit')
			);
			$this->setNavigation(
			     $navigationLittersId, 'Categories', 'litters/categories',
			     array('litters/add_category', 'litters/edit_category')
			);
		}
	}
