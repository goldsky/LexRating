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

$objectName = 'Property Rating';
$groupName = $fields['thread'];
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


$userId = $hook->modx->user->get('id');
$processorsPath = $lexrating->configs['processorsPath'];
$extended = array(
    'quipReplyId' => $fields['idprefix'] . $fields['id']
);
$extended = json_encode($extended);

$response = $hook->modx->runProcessor('web/count/set', array(
    'id' => $id,
    'ObjectID' => $id,
    'UserID' => $userId,
    'UserIP' => $fields['id'],
    'Count' => $fields['lexrating.quip'],
    'Extended' => $extended,
	), array('processors_path' => $processorsPath));

if (!isset($response->response)) {
    return FALSE;
}

return true;