<?php

/**
 * Ajax Connector
 *
 * @package lexrating
 */
$validActions = array(
    'web/count/get',
    'web/count/set'
);
if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $validActions)) {
    @session_cache_limiter('public');
    define('MODX_REQP', false);
}

define('MODX_API_MODE', true);
// this goes to the www.domain.name/index.php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$lexratingCorePath = $modx->getOption('lexrating.core_path', null, $modx->getOption('core_path') . 'components/lexrating/');
require_once $lexratingCorePath . 'models/lexrating.class.php';
$modx->lexrating = new LexRating($modx);

$modx->lexicon->load('lexrating:web');

if (in_array($_REQUEST['action'], $validActions)) {
    $version = $modx->getVersionData();
    if (version_compare($version['full_version'], '2.1.1-pl') >= 0) {
        if ($modx->user->hasSessionContext($modx->context->get('key'))) {
            $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
        } else {
            $_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
            $_SERVER['HTTP_MODAUTH'] = 0;
        }
    } else {
        $_SERVER['HTTP_MODAUTH'] = $modx->site_id;
    }
    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

/* handle request */
$connectorRequestClass = $modx->getOption('modConnectorRequest.class', null, 'modConnectorRequest');
$modx->config['modRequest.class'] = $connectorRequestClass;
$path = $modx->getOption('processorsPath', $modx->lexrating->configs, $lexratingCorePath . 'processors/');
$modx->getRequest();
$modx->request->sanitizeRequest();
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));