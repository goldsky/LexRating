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
 * LexRating snippet
 *
 * @package lexrating
 * @subpackage snippet
 */
/**
 * Defines the rating's name
 * @var     string  any string characters
 * @default resource's ID
 */
$scriptProperties['name'] = $modx->getOption('name', $scriptProperties, $modx->resource->get('id'));
/**
 * Defines the rating's group name
 * @var     string  any string characters
 * @default modResource
 */
$scriptProperties['group'] = $modx->getOption('group', $scriptProperties, 'modResource');
/**
 * Defines who is able to vote
 * @var     string  comma delimited of usergroups
 * @default empty
 */
$scriptProperties['userGroups'] = $modx->getOption('userGroups', $scriptProperties);
/**
 * Get a particular rating with this specified Extended value of the Count object
 * @var     string  string | json format
 * @default empty
 * @since   1.0.0-beta.2
 * @example {"quipReplyId":"qcom29"}	quip's reply id
 */
$scriptProperties['extended'] = $modx->getOption('extended', $scriptProperties);
/**
 * Load the values using Ajax
 * @var     bool    0 | 1
 * @default 1
 */
$scriptProperties['initialAjax'] = $modx->getOption('initialAjax', $scriptProperties, 0);
/**
 * Is the rating for read only?
 * @var     bool    0 | 1
 * @default 0
 */
$scriptProperties['readOnly'] = $modx->getOption('readOnly', $scriptProperties, 0);
/**
 * Template chunk for the output
 * @var     string  chunkname
 * @default rating
 */
$scriptProperties['tpl'] = $modx->getOption('tpl', $scriptProperties, 'lexrating');
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
    $modx->regClientStartupScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
}
$modx->regClientStartupScript('assets/components/lexrating/vendors/rateit/src/jquery.rateit.min.js');
if (!empty($scriptProperties['js'])) {
    $modx->regClientStartupScript($scriptProperties['js']);
}

$phs = $lexrating->getRating();
if (!empty($toArray)) {
    $output = '<pre>' . print_r($phs, 1) . '</pre>';
} else {
    $output = $lexrating->parseTpl($scriptProperties['tpl'], $phs);
}

if (!empty($toPlaceholder)) {
    $modx->toPlaceholder($toPlaceholder, $output);
    return;
}

return $output;