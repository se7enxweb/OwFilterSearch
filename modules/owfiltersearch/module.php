<?php

$Module = array( 'name' => 'Filter search' );

$ViewList = array( );
$ViewList['empty_attributes'] = array(
    'script' => 'empty_attributes.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'filtersearch',
    'unordered_params' => array( 'offset' => 'Offset', ),
    'params' => array(
        'ClassFilter',
        'EmptyAttributeFilters',
        'EmptyAttributeFilterType',
        'FilledAttributeFilters',
        'FilledAttributeFilterType',
        'TranslationFilter'
    ),
    'single_post_actions' => array(
        'FilterButton' => 'Filter',
        'SelectClassButton' => 'SelectClass'
    ),
    'post_action_parameters' => array(
        'Filter' => array(
            'ClassFilter' => 'ClassFilter',
            'EmptyAttributeFilters' => 'EmptyAttributeFilters',
            'EmptyAttributeFilterType' => 'EmptyAttributeFilterType',
            'FilledAttributeFilters' => 'FilledAttributeFilters',
            'FilledAttributeFilterType' => 'FilledAttributeFilterType',
            'TranslationFilter' => 'TranslationFilter'
        ),
        'SelectClass' => array( 'ClassFilter' => 'ClassFilter' )
    ),
    'ui_context' => 'view'
);

$FunctionList['read'] = array( );
?>
