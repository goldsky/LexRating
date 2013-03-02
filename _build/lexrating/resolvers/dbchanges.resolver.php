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
 * Resolve changing db tables on upgrading
 *
 * @package lexrating
 * @subpackage build
 */
if ($modx = & $object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('core_path') . 'components/lexrating/models/';
            $modelPath = realpath($modelPath) . DIRECTORY_SEPARATOR;
            $modx->addPackage('lexrating', $modelPath, 'modx_lexrating_');

            $manager = $modx->getManager();
            $modx->log(xPDO::LOG_LEVEL_WARN, 'Database changes notification:');
            $manager->addField('Objects', 'UserGroups');
            $manager->addField('Count', 'Extended');

            break;
        default :
            break;
    }
}
return true;