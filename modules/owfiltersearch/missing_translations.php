<?php

$Module = $Params["Module"];
if( is_callable( 'eZTemplate::factory' ) ) {
    $tpl = eZTemplate::factory( );
} else {
    include_once ('kernel/common/template.php');
    $tpl = templateInit( );
}

$filterParams = array( );
if( $Module->currentAction( ) != FALSE ) {
    foreach( $Module->Functions[$Module->currentView()]['post_action_parameters'][$Module->currentAction()] as $parameter ) {
        if( !empty( $Params[$parameter] ) ) {
            $filterParams[$parameter] = $Params[$parameter];
        } elseif( $Module->hasActionParameter( $parameter ) ) {
            $filterParams[$parameter] = $Module->actionParameter( $parameter );
        }
    }
}

$tpl->setVariable( 'missing_translation_filter_type', 'AND' );
$tpl->setVariable( 'empty_attribute_filter_type', 'AND' );

foreach( $filterParams as $variableName => $value ) {
    $variableName = strtolower( preg_replace( '/\B([A-Z])/', '_$1', $variableName ) );
    $tpl->setVariable( $variableName, $value );
}
if( !empty( $filterParams ) && isset( $filterParams['ClassFilter'] ) && (isset( $filterParams['MissingTranslationFilters'] ) || isset( $filterParams['ExistingTranslationFilters'] )) ) {
    $filter = new OWFilterSearchMissingTranslations( $filterParams );
    $offset = $Params['Offset'];
    if( !is_numeric( $offset ) ) {
        $offset = 0;
    }

    $maxElementByPage = array(
        10,
        10,
        25,
        50
    );
    $length = $maxElementByPage[min( array(
        eZPreferences::value( 'owfiltersearch_missing_translations_limit' ),
        3
    ) )];
    $results = $filter->getResults( array(
        'offset' => $offset,
        'length' => $length
    ) );
    $tpl->setVariable( 'limit', $length );
    $tpl->setVariable( 'results', $results['nodes'] );
    $tpl->setVariable( 'result_count', $results['count'] );
    $filterParamURIArray = array( );
    foreach( $filterParams as $key => $value ) {
        $filterParamURIArray[$key] = is_array( $value ) ? implode( ',', $value ) : $value;
    }
    $page_uri = trim( $Module->redirectionURI( 'owfiltersearch', 'missing_translations', $filterParamURIArray ), '/' );
    $tpl->setVariable( 'page_uri', $page_uri );
    $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
}

$Result['content'] = $tpl->fetch( 'design:owfiltersearch/missing_translations.tpl' );
$Result['left_menu'] = 'design:owfiltersearch/menu.tpl';

if( function_exists( 'ezi18n' ) ) {
    $Result['path'] = array(
        array( 'text' => ezi18n( 'design/admin/parts/owfiltersearch/menu', 'Filter search' ) ),
        array(
            'url' => 'filtersearch/missing_translations',
            'text' => ezi18n( 'design/admin/parts/owfiltersearch/menu', 'Missing translations' )
        )
    );

} else {
    $Result['path'] = array(
        array( 'text' => ezpI18n::tr( 'design/admin/parts/owfiltersearch/menu', 'Filter search' ) ),
        array(
            'url' => 'filtersearch/missing_translations',
            'text' => ezpI18n::tr( 'design/admin/parts/owfiltersearch/menu', 'Missing translations' )
        )
    );

}
