<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the Litters-categories
 *
 * @author Yohann Bianchi <sbooob@gmail.com>
 */
class FrontendLittersWidgetCategories extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // get categories
        $categories = FrontendLittersModel::getAllCategories();

        // any categories?
        if (!empty($categories)) {
            // build link
            $link = FrontendNavigation::getURLForBlock('litters', 'category');

            // loop and reset url
            foreach ($categories as &$row) {
                $row['url'] = $link . '/' . $row['url'];
            }
        }

        // assign comments
        $this->tpl->assign('widgetLittersCategories', $categories);
    }
}
