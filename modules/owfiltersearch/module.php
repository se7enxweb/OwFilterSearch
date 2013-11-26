<?php

$Module = array( 'name' => 'Filter search' );

$ViewList = array( );
$ViewList['empty_attributes'] = array(
    'script' => 'empty_attributes.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'owfiltersearch',
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

$ViewList['missing_translations'] = array(
    'script' => 'missing_translations.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'owfiltersearch',
    'unordered_params' => array( 'offset' => 'Offset', ),
    'params' => array(
        'ClassFilter',
        'MissingTranslationFilters',
        'MissingTranslationFilterType',
        'ExistingTranslationFilters',
        'ExistingTranslationFilterType',
    ),
    'single_post_actions' => array(
        'FilterButton' => 'Filter',
    ),
    'post_action_parameters' => array(
        'Filter' => array(
            'ClassFilter' => 'ClassFilter',
            'MissingTranslationFilters' => 'MissingTranslationFilters',
            'MissingTranslationFilterType' => 'MissingTranslationFilterType',
            'ExistingTranslationFilters' => 'ExistingTranslationFilters',
            'ExistingTranslationFilterType' => 'ExistingTranslationFilterType'
        ),
    ),
    'ui_context' => 'view'
);

$FunctionList['read'] = array( );
?>
