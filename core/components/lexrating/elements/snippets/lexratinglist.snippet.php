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
 * LexRatingList snippet
 *
 * @package lexrating
 * @subpackage snippet
 */
/**
 * Defines the rating's group name
 * @var     string  any string characters
 * @default modResource
 */
$scriptProperties['group'] = $modx->getOption('group', $scriptProperties, 'modResource');
/**
 * Query's limit
 * @var     int  number of limit
 * @default 10
 */
$scriptProperties['limit'] = $modx->getOption('limit', $scriptProperties, 10);
/**
 * Query's limit offset
 * @var     int  offset the limit
 * @default 0
 */
$scriptProperties['offset'] = $modx->getOption('offset', $scriptProperties, 0);
/**
 * Sorting direction
 * @var     string  asc (lo-hi) | desc (hi-lo)
 * @default desc
 */
$scriptProperties['sort'] = $modx->getOption('sort', $scriptProperties, 'desc');
/**
 * Template chunk for wrapper
 * @var     string  chunkname
 * @default list.wrapper
 */
$scriptProperties['tplListWrapper'] = $modx->getOption('tplListWrapper', $scriptProperties, 'lexratinglist.wrapper');
/**
 * Template chunk for each item
 * @var     string  chunkname
 * @default list.item
 */
$scriptProperties['tplListItem'] = $modx->getOption('tplListItem', $scriptProperties, 'lexratinglist.item');
/**
 * CSS filename
 */
$scriptProperties['css'] = $modx->getOption('css', $scriptProperties, 'assets/components/lexrating/default/css/lexrating.css');
/**
 * Javascript filename
 */
$scriptProperties['js'] = $modx->getOption('js', $scriptProperties, 'assets/components/lexrating/default/js/lexrating.js');
/**
 * Auto load jQuery
 * @var     boolean 0 | 1
 * @default 1
 */
$scriptProperties['loadjQuery'] = $modx->getOption('loadjQuery', $scriptProperties, 1);
/**
 * Prefix for placeholders
 * @var     string  any string
 * @default lexrating.
 */
$scriptProperties['phsPrefix'] = $modx->getOption('phsPrefix', $scriptProperties, 'lexrating.');
/**
 * Option to defer JavaScript
 * @var     boolean 0 | 1
 * @default 0
 */
$scriptProperties['scriptsBottom'] = $modx->getOption('scriptsBottom', $scriptProperties, 0);

$defaultLexRatingCorePath = $modx->getOption('core_path') . 'components/lexrating/';
$lexratingCorePath = $modx->getOption('lexrating.core_path', null, $defaultLexRatingCorePath);
$lexrating = $modx->getService('lexrating', 'LexRating', $lexratingCorePath . 'models/');

if (!($lexrating instanceof LexRating))
    return '';

$lexrating->setConfigs($scriptProperties);

$modx->regClientCSS('assets/components/lexrating/vendors/rateit/src/rateit.css');
if (!empty($scriptProperties['css'])) {
    $modx->regClientCSS($scriptProperties['css']);
}
if (!empty($scriptProperties['loadjQuery'])) {
    (!empty($scriptProperties['scriptsBottom'])) ? $modx->regClientScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js') : $modx->regClientStartupScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
}
(!empty($scriptProperties['scriptsBottom'])) ? $modx->regClientScript('assets/components/lexrating/vendors/rateit/src/jquery.rateit.min.js') : $modx->regClientStartupScript('assets/components/lexrating/vendors/rateit/src/jquery.rateit.min.js');
if (!empty($scriptProperties['js'])) {
    (!empty($scriptProperties['scriptsBottom'])) ? $modx->regClientScript($scriptProperties['js']) : $modx->regClientStartupScript($scriptProperties['js']);
}

$list = $lexrating->getRatingList();
if (!empty($toArray)) {
    $output = '<pre>' . print_r($list, 1) . '</pre>';
} else {
    if (empty($list[$scriptProperties['phsPrefix'] . 'list'])) {
        return;
    }
    $items = array();
    foreach ($list[$scriptProperties['phsPrefix'] . 'list'] as $objArray) {
        $items[] = $lexrating->parseTpl($scriptProperties['tplListItem'], $objArray);
    }
    $items = @implode("\n", $items);
    $listWrapper[$scriptProperties['phsPrefix'] . 'list.items'] = $items;
    $output = $lexrating->parseTpl($scriptProperties['tplListWrapper'], $listWrapper);
}

if (!empty($toPlaceholder)) {
    $modx->toPlaceholder($toPlaceholder, $output);
    return;
}

return $output;