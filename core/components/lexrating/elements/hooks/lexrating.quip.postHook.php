<?php
$defaultLexRatingCorePath = $hook->modx->getOption('core_path') . 'components/lexrating/';
$lexratingCorePath = $hook->modx->getOption('lexrating.core_path', null, $defaultLexRatingCorePath);
$lexrating = $hook->modx->getService('lexrating', 'LexRating', $lexratingCorePath . 'models/');

if (!($lexrating instanceof LexRating)) {
    $hook->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
    $hook->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . ' ');
    $hook->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': !($lexrating instanceof LexRating)');
    return FALSE;
}

//$hook->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
//$hook->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . ' ');
//$hook->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': $fields ' . print_r($fields, 1));

$groupName = !empty($fields['lexrating_groupName']) ? $fields['lexrating_groupName'] : 'modResource';
$objectName = !empty($fields['lexrating_objectName']) ? $fields['lexrating_objectName'] : $hook->modx->resource->get('id');

$object = $hook->modx->getObject('Objects'
        , array(
    'ObjectName' => $objectName,
    'GroupName' => $groupName
        )
);
if (!$object) {
    $c = $hook->modx->newObject('Objects');
    $c->fromArray(array(
        'ObjectName' => $objectName,
        'GroupName' => $groupName,
    ));
    $c->save();
    $id = $c->getPrimaryKey();
} else {
    $id = $object->getPrimaryKey();
}

$processorsPath = $lexrating->configs['processorsPath'];
if (empty($fields['lexrating_extended'])) {
    $extended = array(
        'quipReplyId' => $fields['idprefix'] . $fields['id']
    );
    $fields['lexrating_extended'] = json_encode($extended);
}

$response = $hook->modx->runProcessor('web/count/set', array(
    'id' => $id,
    'ObjectID' => $id,
    'UserID',
    'UserIP',
    'Count' => $fields['lexrating_quip'],
    'Extended' => $fields['lexrating_extended'],
        ), array('processors_path' => $processorsPath)
);

if (!isset($response->response)) {
    return FALSE;
}

return true;