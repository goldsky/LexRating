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
 * @subpackage processor
 */
class LexRatingWebCountGetProcessor extends modObjectGetProcessor {

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'Objects';
    public $objectType = 'LexRatingWebCountGetProcessor.count';

    /**
     * Return the response
     * @return array
     */
    public function cleanup() {
        $objectArray = $this->object->toArray();
        $cleanOutput = array(
            'id' => $objectArray['id']
        );
        $countPhs = $this->modx->lexrating->getCount($objectArray['id']);

        return $this->success('', array_merge($cleanOutput, $countPhs));
    }

}

return 'LexRatingWebCountGetProcessor';