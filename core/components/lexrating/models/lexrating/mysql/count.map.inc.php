<?php
$xpdo_meta_map['Count']= array (
  'package' => 'lexrating',
  'version' => '1.1',
  'table' => 'count',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'ObjectID' => NULL,
    'UserID' => 0,
    'UserIP' => NULL,
    'Count' => NULL,
    'Extended' => NULL,
  ),
  'fieldMeta' => 
  array (
    'ObjectID' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'UserID' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'UserIP' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '45',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'Count' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '45',
      'phptype' => 'string',
      'null' => false,
    ),
    'Extended' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'ObjectID' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'UserID' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'UserIP' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Objects' => 
    array (
      'class' => 'Objects',
      'local' => 'ObjectID',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
