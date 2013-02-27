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
 * Define the MODX path constants necessary for installation
 *
 * @package lexrating
 * @subpackage build schema
 */
require_once dirname(__FILE__) . '/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

echo '<pre>';
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$root = dirname(dirname(dirname(__FILE__))) . '/';
$sources = array(
    'model' => $root . 'core/components/lexrating/models/',
    'schema_file' => $root . 'core/components/lexrating/models/schema/lexrating.mysql.schema.xml'
);
$manager = $modx->getManager();
$generator = $manager->getGenerator();
if (!is_dir($sources['model'])) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Model directory not found! : ' . $sources['model']);
    die();
}
if (!file_exists($sources['schema_file'])) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Schema file not found!');
    die();
}
$generator->parseSchema($sources['schema_file'], $sources['model']);

echo 'Done.';
exit();