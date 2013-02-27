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
 * Build the setup options form.
 *
 * @package lexrating
 * @subpackage build
 */
/* set some default values */
$output = '';
/* get values based on mode */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        /* do output html */
        $output = '
<h2>LexRating Uninstaller</h2>
<p>You are about to uninstall LexRating snippet. Do you also want to remove the LexRating\'s database?</p>
<br />
<input type="checkbox" name="lexrating_keep_db" id="lexrating_keep_db" value="1" selected="selected" />
<p>It is recommended if you keep the rating countings.</p>
<br /><br />
';
        break;
}

return $output;