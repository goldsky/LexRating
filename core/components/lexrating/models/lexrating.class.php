<?php

/**
 * LexRating
 *
 * Copyright 2012 by goldsky <goldsky@fastmail.fm>
 *
 * This file is part of LexRating, a jQuery AJAX star rating for MODX Revolution
 * Based on http://rateit.codeplex.com
 * Twitter: @gjunge
 * @license Ms-PL
 *
 * LexRating is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * LexRating is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * LexRating; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package lexrating
 * @subpackage main class
 */
class LexRating {

	public $modx;
	public $configs = array();

	public function __construct(& $modx, array $configs = array()) {
		$this->modx = & $modx;
		$this->setConfigs($configs);
	}

	/**
	 * Set class configuration exclusively for multiple snippet calls
	 * @param   array   $config     snippet's parameters
	 */
	public function setConfigs(array $configs = array()) {
		$basePath = $this->modx->getOption('core_path') . 'components/lexrating/';
		$assetsUrl = $this->modx->getOption('assets_url') . 'components/lexrating/';
		$this->configs = array_merge(array(
			'basePath' => $basePath,
			'corePath' => $basePath,
			'modelPath' => $basePath . 'models/',
			'processorsPath' => $basePath . 'processors/',
			'chunksPath' => $basePath . 'elements/chunks/',
			'jsUrl' => $assetsUrl . 'js/',
			'cssUrl' => $assetsUrl . 'css/',
			'assetsUrl' => $assetsUrl,
			'connectorUrl' => $assetsUrl . 'connector.php',
				), $configs);

		$this->modx->addPackage('lexrating', $this->configs['modelPath'], 'modx_lexrating_');
	}

	/**
	 * Define individual config for the class
	 * @param   string  $key    array's key
	 * @param   string  $val    array's value
	 */
	public function setConfig($key, $val) {
		$this->configs[$key] = $val;
	}

	/**
	 * Get rating object
	 * @return  array   rating object's array
	 */
	public function getRating() {
		$object = $this->modx->getObject('Objects'
				, array(
			'ObjectName' => $this->configs['name'],
			'GroupName' => $this->configs['group']
				)
		);
		if (!$object) {
			$c = $this->modx->newObject('Objects');
			$c->fromArray(array(
				'ObjectName' => $this->configs['name'],
				'GroupName' => $this->configs['group'],
				'UserGroups' => $this->configs['userGroups']
			));
			$c->save();
			$id = $c->getPrimaryKey();
		} else {
			$id = $object->getPrimaryKey();
		}

		$results = array();
		$results[$this->configs['phsPrefix'] . 'name'] = $this->configs['name'];
		$results[$this->configs['phsPrefix'] . 'group'] = $this->configs['group'];
		$results[$this->configs['phsPrefix'] . 'id'] = $id;
		$results[$this->configs['phsPrefix'] . 'initialAjax'] = $this->configs['initialAjax'];

		if (!empty($this->configs['initialAjax'])) {
			$allowedToVote = FALSE;
			$totalVoters = 0;
			$value = 0;
		} else {
			$counter = $this->getCount($id);
			$allowedToVote = $counter['allowedToVote'];
			$totalVoters = $counter['total.voters'];
			$value = $counter['value'];
		}
		$results[$this->configs['phsPrefix'] . 'allowedToVote'] = strval($allowedToVote);
		$results[$this->configs['phsPrefix'] . 'total.voters'] = $totalVoters;
		$results[$this->configs['phsPrefix'] . 'value'] = $value;

		return $results;
	}

	/**
	 * Get the count of specified rating object
	 * @param   int             $id ID of the rating object
	 * @return  boolean|array   false on empty ID or placeholders array
	 */
	public function getCount($id) {
		if (empty($id))
			return FALSE;

		// get the number of votes of this rating group
		$object = $this->modx->getObject('Objects', $id);
		$c = null;
		if (!empty($this->configs['extended'])) {
			$c = $this->modx->newQuery('Count');
			$c->where(array(
				'Extended' => $this->configs['extended']
			));
		}
		$votes = $object->getMany('Count', $c);
		$totalVoters = count($votes);
		$id = $object->getPrimaryKey();
		$counter = 0;
		foreach ($votes as $v) {
			$counter += $v->get('Count');
		}

		// get a vote of the current user, if exists
		$exists = $this->modx->getObject('Count', array(
			'ObjectID' => $id,
			'UserID' => $this->modx->user->get('id'),
			'UserIP' => $_SERVER['REMOTE_ADDR'],
			'Extended' => $this->configs['extended']
		));

		if ($exists) {
			$allowedToVote = FALSE;
		} else {
			$allowedToVote = $this->_isAllowedToVote($object->get('UserGroups'));
		}
		$value = 0;
		if ($totalVoters > 0) {
			$value = floatval($counter / $totalVoters);
		}
		$output = array(
			'allowedToVote' => $allowedToVote,
			'total.voters' => $totalVoters,
			'value' => $value,
		);

		return $output;
	}

	/**
	 * Check whether the visitor is allowed to vote or not
	 * @return boolean
	 */
	private function _isAllowedToVote($dbUserGroups) {
		if (!empty($this->configs['readOnly'])) {
			return FALSE;
		}
		if (empty($dbUserGroups)) {
			return TRUE;
		}

		$voterGroups = @explode(',', $dbUserGroups);
		array_walk($voterGroups, create_function('&$v', '$v = trim($v);'));
		$isAllowed = $this->modx->user->isMember($voterGroups);
		return $isAllowed;
	}

	/**
	 * Get the list of the ratings based on the group's name
	 * @return  array   result
	 */
	public function getRatingList() {
		$c = $this->modx->newQuery('Objects');
		$c->where(array(
			'GroupName' => $this->configs['group']
		));
		$totalCount = $this->modx->getCount('Objects', $c);
		$c->limit($this->configs['limit'], $this->configs['offset']);
		$results = $this->modx->getIterator('Objects', $c);

		$output[$this->configs['phsPrefix'] . 'group'] = $this->configs['group'];
		$output[$this->configs['phsPrefix'] . 'total'] = $totalCount;
		$output[$this->configs['phsPrefix'] . 'list'] = array();
		$objArray = array();
		foreach ($results as $obj) {
			$objArray = array(
				$this->configs['phsPrefix'] . 'id' => $obj->get('id'),
				$this->configs['phsPrefix'] . 'group' => $obj->get('GroupName'),
				$this->configs['phsPrefix'] . 'name' => $obj->get('ObjectName')
			);
			$counts = $obj->getMany('Count');
			$votersArray = array();
			$counter = 0;
			foreach ($counts as $x) {
				$countArray = array(
					$this->configs['phsPrefix'] . 'id' => $x->get('ObjectID'),
					$this->configs['phsPrefix'] . 'user.id' => $x->get('UserID'),
					$this->configs['phsPrefix'] . 'user.ip' => $x->get('UserIP'),
					$this->configs['phsPrefix'] . 'count' => $x->get('Count')
				);
				$votersArray[] = $countArray;
				$counter += $x->get('Count');
			}
			$value = 0;
			$totalVoters = count($counts);
			if ($totalVoters > 0) {
				$value = floatval($counter / $totalVoters);
			}
			$objArray[$this->configs['phsPrefix'] . 'voters'] = $votersArray;
			$objArray[$this->configs['phsPrefix'] . 'value'] = $value;
			$objArray[$this->configs['phsPrefix'] . 'total.voters'] = $totalVoters;

			$output[$this->configs['phsPrefix'] . 'list'][] = $objArray;
		}
		$output = $this->sortList($output);
		return $output;
	}

	/**
	 * Sort the result of the rating list
	 * @param   array   $listArray  list array
	 * @return  array   sorted array
	 */
	public function sortList($listArray) {
		$direction = mb_strtolower($this->configs['sort'], 'UTF-8');
		$temp = array();

		foreach ($listArray['lexrating.list'] as $list) {
			$key = (string) $list[$this->configs['phsPrefix'] . 'value'];
			$idKey = $list[$this->configs['phsPrefix'] . 'id'];
			$temp[$key][$idKey] = $list;
		}
		foreach ($temp as $items) {
			if ($direction === 'asc') {
				ksort($items);
			} else {
				krsort($items);
			}
		}
		if ($direction === 'asc') {
			ksort($temp);
		} else {
			krsort($temp);
		}

		$newList = array();
		foreach ($temp as $key) {
			foreach ($key as $val) {
				$newList[] = $val;
			}
		}

		$listArray['lexrating.list'] = $newList;
		return $listArray;
	}

	/**
	 * Parsing template
	 * @param   string  $tpl    @BINDINGs options
	 * @param   array   $phs    placeholders
	 * @return  string  parsed output
	 * @link    http://forums.modx.com/thread/74071/help-with-getchunk-and-modx-speed-please?page=2#dis-post-413789
	 */
	public function parseTpl($tpl, array $phs = array()) {
		$output = '';
		if (preg_match('/^(@CODE|@INLINE)/i', $tpl)) {
			$tplString = preg_replace('/^(@CODE|@INLINE)/i', '', $tpl);
			// tricks @CODE: / @INLINE:
			$tplString = ltrim($tplString, ':');
			$tplString = trim($tplString);
			$output = $this->parseTplCode($tplString, $phs);
		} elseif (preg_match('/^@FILE/i', $tpl)) {
			$tplFile = preg_replace('/^@FILE/i', '', $tpl);
			// tricks @FILE:
			$tplFile = ltrim($tplFile, ':');
			$tplFile = trim($tplFile);
			$tplFile = $this->replacePropPhs($tplFile);
			try {
				$output = $this->parseTplFile($tplFile, $phs);
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}
		// ignore @CHUNK / @CHUNK: / empty @BINDING
		else {
			$tplChunk = preg_replace('/^@CHUNK/i', '', $tpl);
			// tricks @CHUNK:
			$tplChunk = ltrim($tpl, ':');
			$tplChunk = trim($tpl);

			$chunk = $this->modx->getObject('modChunk', array('name' => $tplChunk), true);
			if (empty($chunk)) {
				// try to use @splittingred's fallback
				$f = $this->configs['chunksPath'] . strtolower($tplChunk) . '.chunk.tpl';
				try {
					$output = $this->parseTplFile($f, $phs);
				} catch (Exception $e) {
					$output = $e->getMessage();
					return 'Chunk: ' . $tplChunk . ' is not found, neither the file ' . $output;
				}
			} else {
				$output = $this->modx->getChunk($tpl, $phs);
			}
		}

		return $output;
	}

	/**
	 * Parsing inline template code
	 * @param   string  $code   HTML with tags
	 * @param   array   $phs    placeholders
	 * @return  string  parsed output
	 */
	public function parseTplCode($code, array $phs = array()) {
		$chunk = $this->modx->newObject('modChunk');
		$chunk->setContent($code);
		$chunk->setCacheable(false);
		$phs = $this->replacePropPhs($phs);
		return $chunk->process($phs);
	}

	/**
	 * Parsing file based template
	 * @param   string  $file   file path
	 * @param   array   $phs    placeholders
	 * @return  string  parsed output
	 * @throws  Exception if file is not found
	 */
	public function parseTplFile($file, array $phs = array()) {
		$chunk = false;
		if (!file_exists($file)) {
			throw new Exception('File: ' . $file . ' is not found.');
		}
		$o = file_get_contents($file);
		$chunk = $this->modx->newObject('modChunk');

		// just to create a name for the modChunk object.
		$name = strtolower(basename($file));
		$name = rtrim($name, '.tpl');
		$name = rtrim($name, '.chunk');
		$chunk->set('name', $name);

		$chunk->setCacheable(false);
		$chunk->setContent($o);
		$output = $chunk->process($phs);

		return $output;
	}

	/**
	 * Replace the property's placeholders
	 * @param   string|array    $subject    Property
	 * @return  array           The replaced results
	 */
	public function replacePropPhs($subject) {
		$pattern = array(
			'/\{core_path\}/',
			'/\{base_path\}/',
			'/\{assets_url\}/',
			'/\{filemanager_path\}/',
			'/\[\[\+\+core_path\]\]/',
			'/\[\[\+\+base_path\]\]/'
		);
		$replacement = array(
			$this->modx->getOption('core_path'),
			$this->modx->getOption('base_path'),
			$this->modx->getOption('assets_url'),
			$this->modx->getOption('filemanager_path'),
			$this->modx->getOption('core_path'),
			$this->modx->getOption('base_path')
		);
		if (is_array($subject)) {
			$parsedString = array();
			foreach ($subject as $k => $s) {
				if (is_array($s)) {
					$s = $this->replacePropPhs($s);
				}
				$parsedString[$k] = preg_replace($pattern, $replacement, $s);
			}
			return $parsedString;
		} else {
			return preg_replace($pattern, $replacement, $subject);
		}
	}

}
