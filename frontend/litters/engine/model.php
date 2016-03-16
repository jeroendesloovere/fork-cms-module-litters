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
     * Fetches a certain litter
     *
     * @param string $URL
     * @return array
     */
    public static function getLitter($URL)
    {
        $query = <<< EOQ
SELECT
	litters.*,
	UNIX_TIMESTAMP(litters.birth_date) AS birth_date,
	meta.keywords AS meta_keywords,
	meta.keywords_overwrite AS meta_keywords_overwrite,
	meta.description AS meta_description,
	meta.description_overwrite AS meta_description_overwrite,
	meta.title AS meta_title,
	meta.title_overwrite AS meta_title_overwrite,
	meta.url,
	father.name AS father_name,
	father.affix AS father_affix,
	father.affix_position AS father_affix_position,
	father.url AS father_url,
	father.photo_url AS father_photo_url,
	mother.name AS mother_name,
	mother.affix AS mother_affix,
	mother.affix_position AS mother_affix_position,
	mother.url AS mother_url,
	mother.photo_url AS mother_photo_url
FROM litters
INNER JOIN meta ON litters.meta_id = meta.id
LEFT JOIN litters_parents AS father on litters.father_id = father.id
LEFT JOIN litters_parents AS mother on litters.mother_id = mother.id
WHERE meta.url = ?
EOQ;
        $item = (array) FrontendModel::getContainer()->get('database')->getRecord($query, array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = FrontendNavigation::getURLForBlock('litters', 'detail') . '/' . $item['url'];

        // add booleans mark affix positions
        if ($item['mother_affix_position'] == 'PREPEND') {
            $item['mother_affix_prepend'] = true;
        } elseif ($item['mother_affix_position'] == 'APPEND') {
            $item['mother_affix_append'] = true;
        }
        if ($item['father_affix_position'] == 'PREPEND') {
            $item['father_affix_prepend'] = true;
        } elseif ($item['father_affix_position'] == 'APPEND') {
            $item['father_affix_append'] = true;
        }

        return $item;
    }

    /**
     * Fetches the youngs belonging to a given litter
     *
     * @param int $id
     * @return array
     */
    public static function getYoungs($id)
    {
        $query = <<< EOQ
SELECT *
FROM litters_youngs
WHERE litter_id = ?
ORDER BY sequence ASC, id DESC;
EOQ;
        $youngs = (array)FrontendModel::getContainer()->get('database')->getRecords($query, array((int)$id));
        foreach ($youngs as &$young) {
            $young['availability'] = ucfirst(FL::lbl('Availability' . ucfirst(strtolower($young['availability']))));
            $young['quality'] = ucfirst(FL::lbl('Quality' . ucfirst(strtolower($young['quality']))));
        }
        return $youngs;
    }

    /**
     * Get all items (at least a chunk)
     *
     * @param int[optional]		$limit	The number of items to get
     * @param int[optional]		$offset	The offset
     * @param string[optional]	$table	The table to query
     * @return array
     */
    public static function getAll($limit = 10, $offset = 0, $table = 'litters')
    {
        $query = <<< EOQ
SELECT *
FROM ${table}
JOIN meta ON {$table}.meta_id = meta.id
WHERE language = ?
ORDER BY sequence ASC, ${table}.id DESC
LIMIT ?, ?;
EOQ;
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords($query, array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = FrontendNavigation::getURLForBlock('litters', 'detail');

        // prepare items for search
        foreach ($items as &$item) {
            $item['full_url'] =  $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }

    /**
     * Get the number of items
     *
     * @param	string[optional]	$table	The table to query
     * @return	int
     */
    public static function getAllCount($table = 'litters')
    {
        $query = <<< EOQ
SELECT COUNT(id) AS count
FROM ${table};
EOQ;

        return (int) FrontendModel::getContainer()->get('database')->getVar($query);
    }

    public static function search(array $ids)
    {
        $query = <<< EOQ
SELECT
	litters.id,
	litters.name AS title,
	litters.birth_date,
	litters.description_after,
	meta.url,
	CASE
		WHEN father.affix_position = 'APPEND'
			THEN TRIM(CONCAT(IFNULL(father.name, ""), " ", IFNULL(father.affix, "")))
		WHEN father.affix_position = 'PREPEND'
			THEN TRIM(CONCAT(IFNULL(father.affix, ""), " ", IFNULL(father.name, "")))
	END AS father,
	CASE
		WHEN mother.affix_position = 'APPEND'
			THEN TRIM(CONCAT(IFNULL(mother.name, ""), " ", IFNULL(mother.affix, "")))
		WHEN mother.affix_position = 'PREPEND'
			THEN TRIM(CONCAT(IFNULL(mother.affix, ""), " ", IFNULL(mother.name, "")))
	END AS mother
FROM litters
	INNER JOIN meta ON litters.meta_id = meta.id
	INNER JOIN litters_parents AS father ON father_id = father.id
	INNER JOIN litters_parents AS mother ON mother_id = mother.id
WHERE litters.language = ?;
EOQ;

        $items = (array) FrontendModel::getContainer()->get('database')->getRecords($query, array(FRONTEND_LANGUAGE), 'id');

        // prepare items for search
        $detailUrl = FrontendNavigation::getURLForBlock('litters', 'detail');
        foreach ($items as $key => $item) {
            $items[$key]['full_url'] = $detailUrl . '/' . $item['url'];
            $items[$key]['text'] = sprintf(FL::lbl('SearchText'), urldecode($item['mother']), urldecode($item['father']), urldecode($item['birth_date']));
        }

        return $items;
    }
}
