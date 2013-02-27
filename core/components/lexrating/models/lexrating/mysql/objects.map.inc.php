<?php
$xpdo_meta_map['Objects']= array (
  'package' => 'lexrating',
  'version' => '1.1',
  'table' => 'objects',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'ObjectName' => NULL,
    'GroupName' => NULL,
    'UserGroups' => NULL,
  ),
  'fieldMeta' => 
  array (
    'ObjectName' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'GroupName' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'UserGroups' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'composites' => 
  array (
    'Count' => 
    array (
      'class' => 'Count',
      'local' => 'id',
      'foreign' => 'ObjectID',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
