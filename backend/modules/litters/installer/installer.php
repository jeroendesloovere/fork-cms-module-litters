<?php

class LittersInstaller extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        $this->importSQL(dirname(__FILE__) . '/data/install.sql');

        $this->addModule('litters');

        $this->importLocale(dirname(__FILE__) . '/data/locale.xml');

        // Enable search on this module
        $this->makeSearchable('litters');

        // Give permissions to the members of the admin group
        $this->setModuleRights(1, 'litters');
        $this->setActionRights(1, 'litters', 'index');
        $this->setActionRights(1, 'litters', 'add');
        $this->setActionRights(1, 'litters', 'edit');
        $this->setActionRights(1, 'litters', 'delete');
        $this->setActionRights(1, 'litters', 'index_parents');
        $this->setActionRights(1, 'litters', 'add_parent');
        $this->setActionRights(1, 'litters', 'edit_parent');
        $this->setActionRights(1, 'litters', 'delete_parent');
        $this->setActionRights(1, 'litters', 'add_young');
        $this->setActionRights(1, 'litters', 'edit_young');
        $this->setActionRights(1, 'litters', 'delete_young');
        $this->setActionRights(1, 'litters', 'get_young');
        $this->setActionRights(1, 'litters', 'sequence');

        $littersId = $this->insertExtra('litters', 'block', 'Litters');
        $this->insertExtra('litters', 'block', 'LittersDetail', 'detail');

        // Create backend navigation elements
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationLittersId = $this->setNavigation($navigationModulesId, 'Litters');
        $this->setNavigation($navigationLittersId, 'Litters', 'litters/index', array('litters/add', 'litters/edit'));
        $this->setNavigation($navigationLittersId, 'Parents', 'litters/index_parents', array('litters/index_parent', 'litters/add_parent', 'litters/edit_parent'));
    }
}
