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
	class BackendLittersModel{
		const QRY_DATAGRID_BROWSE
			= <<< EOQ
SELECT
	id,
	name,
	UNIX_TIMESTAMP(created_on) AS created_on,
	sequence
FROM litters
	WHERE language = ?
ORDER BY sequence;
EOQ;

		const QRY_DATAGRID_BROWSE_PARENTS
			= <<< EOQ
SELECT
	p.id,
	IF(p.affix_position = "PREPEND", TRIM(CONCAT(IFNULL(p.affix, ""), " ", IFNULL(p.name, ""))), TRIM(CONCAT(IFNULL(p.name, ""), " ", IFNULL(p.affix, "")))) AS name,
	(COUNT(mother.id) + COUNT(father.id)) AS num_items,
	p.sequence,
	p.sex
FROM litters_parents AS p
	LEFT OUTER JOIN litters AS father ON p.id = father.father_id AND father.language = p.language
	LEFT OUTER JOIN litters AS mother ON p.id = mother.mother_id AND mother.language = p.language
WHERE p.language = ?
GROUP BY p.id
ORDER BY p.sequence ASC;
EOQ;

		const QRY_DATAGRID_BROWSE_YOUNGS
			= <<< EOQ
SELECT
	id,
	photo_url AS photo,
	code_name,
	name,
	sex,
	color,
	url AS gallery_url,
	#UNIX_TIMESTAMP(edited_on) AS edited_on,
	sequence
FROM litters_youngs
WHERE language = ?
	AND litter_id = ?
ORDER BY sequence;
EOQ;

		/**
		 * Deletes a certain item
		 *
		 * @param int               $id     the id of the item to be deleted
		 * @param string [optional] $table  the table to delete from
		 */
		public static function delete($id, $table = 'litters'){
			BackendModel::getContainer()->get('database')->delete((string)$table, 'id = ?', (int)$id);
		}

		/**
		 * Checks if an item having a given id exists
		 *
		 * @param int               $id     the id of the item
		 * @param string [optional] $table  the table to check for item existence on
		 *
		 * @return bool
		 */
		public static function exists($id, $table = 'litters'){
			$req = <<< EOQ
SELECT 1
FROM ${table} AS i
WHERE i.id = ?
LIMIT 1;
EOQ;
			return (bool)BackendModel::getContainer()->get('database')->getVar($req, array((int)$id));
		}

		/**
		 * Fetches an item having the given id
		 *
		 * @param int               $id     the id of the item to fetch
		 * @param string [optional] $table  the table to fetch from
		 *
		 * @return array
		 */
		public static function get($id, $table = 'litters'){
			if($table != 'litters_youngs'){
				$req = <<< EOQ
SELECT
	t.*,
	meta.url,
	UNIX_TIMESTAMP(t.birth_date) AS birth_date
FROM ${table} AS t
LEFT JOIN meta ON t.meta_id = meta.id
WHERE t.id = ?;
EOQ;
			}
			else{
				$req = "SELECT * FROM ${table} WHERE id = ?;";
			}
			return (array)BackendModel::getContainer()->get('database')->getRecord($req, array((int)$id));
		}

		/**
		 * Gets the maximum litters sequence.
		 *
		 * @param string [optional] $table  the table to get the maximum sequence from
		 *
		 * @return int
		 */
		public static function getMaximumSequence($table = 'litters'){
			return (int)BackendModel::getContainer()->get('database')->getVar("SELECT MAX(sequence) FROM ${table};");
		}

		/**
		 * Retrieves the unique URL for an item
		 *
		 * @param string            $url
		 * @param int [optional]    $id     the id of the item to ignore.
		 * @param string [optional] $table  the table (=type) of the item
		 *
		 * @return string
		 */
		public static function getURL($url, $id = null, $table = 'litters'){
			$url = SpoonFilter::urlise((string)$url);
			$db  = BackendModel::getContainer()->get('database');

			// new item
			if($id === null){
				// already exists
				$req = <<<EOQ
SELECT 1
FROM ${table} AS i
INNER JOIN meta AS m ON i.meta_id = m.id
WHERE i.language = ? AND m.url = ?
LIMIT 1;
EOQ;
				if((bool)$db->getVar($req, array(BL::getWorkingLanguage(), $url))){
					$url = BackendModel::addNumber($url);

					return self::getURL($url);
				}
			}
			// current item should be excluded
			else{
				// already exists
				$req = <<< EOQ
SELECT 1
FROM ${table} AS i
INNER JOIN meta AS m ON i.meta_id = m.id
WHERE i.language = ? AND m.url = ? AND i.id != ?
LIMIT 1;
EOQ;
				if((bool)$db->getVar($req, array(BL::getWorkingLanguage(), $url, $id))){
					$url = BackendModel::addNumber($url);

					return self::getURL($url, $id);
				}
			}

			return $url;
		}

		/**
		 * Inserts an item in the database
		 *
		 * @param array             $item   the item to insert
		 * @param string [optional] $table  the table to insert into
		 *
		 * @return int
		 */
		public static function insert(array $item, $table = 'litters'){
			$item['created_on'] = $item['edited_on'] = BackendModel::getUTCDate();

			return (int)BackendModel::getContainer()->get('database')->insert($table, $item);
		}

		/**
		 * Updates an item
		 *
		 * @param array             $item   the item to update
		 * @param string [optional] $table  the table to update
		 */
		public static function update(array $item, $table = 'litters'){
			$item['edited_on'] = BackendModel::getUTCDate();

			BackendModel::getContainer()->get('database')->update($table, $item, 'id = ?', (int)$item['id']);
		}

		/**
		 * Fetches all parents
		 *
		 * @return array    An array of parents
		 */
		public static function getAllParents(){
			return (array)BackendModel::getContainer()->get('database')->getRecords(self::QRY_DATAGRID_BROWSE_PARENTS, array(BL::getWorkingLanguage()));
		}

		public static function getEnumAllowedValues($table, $field){
			$values = null;

			$result = (array)BackendModel::getContainer()->get('database')->getRecord("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}';");
			if(isset($result['Type']) && preg_match('/^enum\((.*)\)$/', $result['Type'], $matches)){
				$values = str_getcsv($matches[1], ',', "'");
			}

			return $values;
		}
	}
