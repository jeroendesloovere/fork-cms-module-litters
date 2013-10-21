<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the Litters module
 *
 * @author Yohann Bianchi <sbooob@gmail.com>
 */
class FrontendLittersModel
{
	/**
	 * Fetches a certain item
	 *
	 * @param string $URL
	 * @return array
	 */
	public static function get($URL)
	{
		$item = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
			 FROM litters AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE m.url = ?',
			array((string) $URL)
		);

		// no results?
		if(empty($item)) return array();

		// create full url
		$item['full_url'] = FrontendNavigation::getURLForBlock('litters', 'detail') . '/' . $item['url'];

		return $item;
	}

	/**
	 * Get all items (at least a chunk)
	 *
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getAll($limit = 10, $offset = 0)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*, m.url
			 FROM litters AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.language = ?
			 ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
			array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

		// no results?
		if(empty($items)) return array();

		// get detail action url
		$detailUrl = FrontendNavigation::getURLForBlock('litters', 'detail');

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] =  $detailUrl . '/' . $item['url'];
		}

		// return
		return $items;
	}

	/**
	 * Get the number of items
	 *
	 * @return int
	 */
	public static function getAllCount()
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(i.id) AS count
			 FROM litters AS i'
		);
	}

	/**
	* Get all category items (at least a chunk)
	*
	* @param int $categoryId
	* @param int[optional] $limit The number of items to get.
	* @param int[optional] $offset The offset.
	* @return array
	*/
	public static function getAllByCategory($categoryId, $limit = 10, $offset = 0)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*, m.url
			 FROM litters AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.category_id = ? AND i.language = ?
			 ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
			array($categoryId, FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

		// no results?
		if(empty($items)) return array();

		// get detail action url
		$detailUrl = FrontendNavigation::getURLForBlock('litters', 'detail');

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = $detailUrl . '/' . $item['url'];
		}

		// return
		return $items;
	}

	/**
	* Get all categories used
	*
	* @return array
	*/
	public static function getAllCategories()
	{
		$return = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, m.data AS meta_data
			 FROM litters_categories AS c
			 INNER JOIN litters AS i ON c.id = i.category_id AND c.language = i.language
			 INNER JOIN meta AS m ON c.meta_id = m.id
			 GROUP BY c.id
			 ORDER BY c.sequence AS',
			array(), 'id'
		);

		// loop items and unserialize
		foreach($return as &$row)
		{
			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);
		}

		return $return;
	}

	/**
	* Fetches a certain category
	*
	* @param string $URL
	* @return array
	*/
	public static function getCategory($URL)
	{
		$item = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
			 FROM litters_categories AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE m.url = ?',
			array((string) $URL)
		);

		// no results?
		if(empty($item)) return array();

		// create full url
		$item['full_url'] = FrontendNavigation::getURLForBlock('litters', 'category') . '/' . $item['url'];

		return $item;
	}



	/**
	* Get the number of items in a category
	*
	* @param int $categoryId
	* @return int
	*/
	public static function getCategoryCount($categoryId)
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(i.id) AS count
			 FROM litters AS i
			 WHERE i.category_id = ?',
			array((int) $categoryId)
		);
	}

}
