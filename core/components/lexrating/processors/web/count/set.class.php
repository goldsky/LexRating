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
class LexRatingWebCountSetProcessor extends modObjectUpdateProcessor {

    public $classKey = 'Objects';

    /**
     * Process the Object create processor
     * {@inheritDoc}
     * @return mixed
     */
    public function process() {
        $props = $this->getProperties();
        $props['UserIP'] = isset($props['UserIP']) && $props['UserIP'] !== '' ?
                $props['UserIP'] :
                strval($_SERVER['REMOTE_ADDR']);
        $props['UserID'] = isset($props['UserID']) && $props['UserID'] !== '' ?
                $props['UserID'] :
                $this->modx->user->get('id');
        $exists = $this->object->getMany('Count', array(
            'ObjectID' => $props['id'],
            'UserID' => $props['UserID'],
            'UserIP' => $props['UserIP']
        ));
        if ($exists) {
            return $this->failure($this->objectType . '_exists');
        }
        $countObj = $this->modx->newObject('Count');
        $countObj->set('ObjectID', $props['id']);
        $countObj->set('UserID', $props['UserID']);
        $countObj->set('UserIP', $props['UserIP']);
        $countObj->set('Count', $props['Count']);
        if (isset($props['Extended'])) {
            $countObj->set('Extended', $props['Extended']);
        }

        $this->object->addMany($countObj);

        /* save element */
        if ($this->object->save() == false) {
            $this->modx->error->checkValidation($this->object);
            return $this->failure($this->modx->lexicon($this->objectType . '_err_save'));
        }

        return $this->cleanup();
    }

    /**
     * Return the success message
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

return 'LexRatingWebCountSetProcessor';