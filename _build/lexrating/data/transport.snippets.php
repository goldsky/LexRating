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
 * LexRating build script
 *
 * @package lexrating
 * @subpackage build
 *
 * @param type $filename
 * @return type
 */

function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = str_replace('<?php', '', $o);
    $o = str_replace('?>', '', $o);
    $o = trim($o);
    return $o;
}

$snippets = array();

$snippets[0] = $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'property_preprocess' => 1,
    'name' => 'LexRating',
    'description' => 'Snippet to rate a topic/object.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/lexrating.snippet.php'),
        ), '', true, true);
$properties = include $sources['properties'] . 'lexrating.properties.php';
$snippets[0]->setProperties($properties);
unset($properties);

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'LexRatingList',
    'description' => 'Snippet to list items under the specified group.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/lexratinglist.snippet.php'),
        ), '', true, true);
$properties = include $sources['properties'] . 'lexratinglist.properties.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'LexRatingQuipPostHook',
    'description' => 'postHook for quip.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/hooks/lexrating.quip.postHook.php'),
        ), '', true, true);

return $snippets;