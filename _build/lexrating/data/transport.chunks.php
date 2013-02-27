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
 */
$chunks = array();

$chunks[0] = $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'lexrating',
    'description' => 'Template chunk for the LexRating output',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/lexrating.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'lexratinglist.wrapper',
    'description' => 'Template chunk for wrapper of LexRatingList',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/lexratinglist.wrapper.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => 2,
    'name' => 'lexratinglist.list',
    'description' => 'Template chunk for each item of LexRatingList',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/lexratinglist.item.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => 3,
    'name' => 'lexrating.quip',
    'description' => 'Template chunk for each item of quip\'s comment',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/lexrating.quip.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

$chunks[4] = $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => 4,
    'name' => 'lexrating.quipAddComment',
    'description' => 'Template chunk for each item of quip\'s comment',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/lexrating.quipaddcomment.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

return $chunks;