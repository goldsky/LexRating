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
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

define('PKG_NAME', 'LexRating');
define('PKG_NAME_LOWER', 'lexrating'); // work around the extra's namespace
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'beta.2');

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$root = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
$sources = array(
    'root' => $root,
    'build' => BUILD_PATH,
    'data' => BUILD_PATH . 'data' . DIRECTORY_SEPARATOR,
    'properties' => realpath(BUILD_PATH . 'data/properties/') . DIRECTORY_SEPARATOR,
    'resolvers' => realpath(BUILD_PATH . 'resolvers/') . DIRECTORY_SEPARATOR,
    'validators' => realpath(BUILD_PATH . 'validators/') . DIRECTORY_SEPARATOR,
    'lexicon' => realpath(MODX_CORE_PATH . 'components/lexrating/lexicon/') . DIRECTORY_SEPARATOR,
    'docs' => realpath(MODX_CORE_PATH . 'components/lexrating/docs/') . DIRECTORY_SEPARATOR,
    'chunks' => realpath(MODX_CORE_PATH . 'components/lexrating/themes/default/chunks/') . DIRECTORY_SEPARATOR,
    'source_assets' => realpath(MODX_ASSETS_PATH . 'components/lexrating'),
    'source_core' => realpath(MODX_CORE_PATH . 'components/lexrating'),
);
unset($root);

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
echo '<pre>'; /* used for nice formatting of log messages */

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
$modx->getService('lexicon', 'modLexicon');
$modx->lexicon->load('lexrating:properties');

/* create category */
$category = $modx->newObject('modCategory');
$category->set('category', PKG_NAME);

/* add snippets */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in snippets...');
flush();
$snippets = include $sources['data'] . 'transport.snippets.php';
if (empty($snippets))
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in snippets.');
$category->addMany($snippets);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in snippets done.');

/* add chunks */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in chunks...');
flush();
$chunks = include $sources['data'] . 'transport.chunks.php';
if (empty($chunks))
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not pack in chunks.');
$category->addMany($chunks);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in chunks done.');

/* create category vehicle */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in category...');
flush();
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        )
    )
);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in category done.');
$vehicle = $builder->createVehicle($category, $attr);

$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to category...');
flush();
$vehicle->resolve('file', array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$modx->log(modX::LOG_LEVEL_INFO, 'File resolvers done.');

$builder->putVehicle($vehicle);
$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP resolvers...');
flush();
$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'tables.resolver.php',
));
$builder->putVehicle($vehicle);
$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP resolvers done.');

$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP validators...');
flush();
$vehicle->validate('php', array(
    'source' => $sources['validators'] . 'options.validator.php',
));
$builder->putVehicle($vehicle);
$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP validators done.');

/* now pack in the license file, readme and setup options */
$modx->log(modX::LOG_LEVEL_INFO, 'Adding package attributes and setup options...');
flush();
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => array(
        'source' => $sources['build'] . 'setup.options.php'
    )
));
$modx->log(modX::LOG_LEVEL_INFO, 'Adding package attributes and setup options done.');
flush();

unset($vehicle);

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
flush();
$builder->pack();
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip done.');

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit();