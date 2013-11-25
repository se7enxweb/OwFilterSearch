<?php

$Module = $Params["Module"];
if( is_callable( 'eZTemplate::factory' ) ) {
    $tpl = eZTemplate::factory( );
} else {
    include_once ('kernel/common/template.php');
    $tpl = templateInit( );
}

$classFilter = $Params['ClassFilter'];
if( $Module->hasActionParameter( 'ClassFilter' ) ) {
    $classFilter = $Module->actionParameter( 'ClassFilter' );
}

$tpl->setVariable( 'class_filter', $classFilter );
if( $classFilter ) {
    $contentClass = eZContentClass::fetchByIdentifier( $classFilter );
    if( $contentClass instanceof eZContentClass ) {
        $tpl->setVariable( 'class_attribute_list', $contentClass->fetchAttributes( ) );
    } else {
        $error = "Class not found";
    }
}
$attributeFilterList = !empty( $Params['AttributeFilters'] ) ? explode( ',', (string)$Params['AttributeFilters'] ) : array( );
if( $Module->hasActionParameter( 'AttributeFilters' ) ) {
    $attributeFilterList = $Module->actionParameter( 'AttributeFilters' );
}

$tpl->setVariable( 'attribute_filters', $attributeFilterList );

if( isset( $error ) ) {
    $tpl->setVariable( 'error', $error );
} elseif( isset( $contentClass ) && !empty( $attributeFilterList ) ) {
    $filter = new OWFilterSearchEmptyAttributes( $contentClass, $attributeFilterList );
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
        eZPreferences::value( 'owfiltersearch_empty_attributes_limit' ),
        3
    ) )];
    $results = $filter->getResults( array(
        'offset' => $offset,
        'length' => $length
    ) );
    $tpl->setVariable( 'limit', $length );
    $tpl->setVariable( 'results', $results['nodes'] );
    $tpl->setVariable( 'result_count', $results['count'] );
    $page_uri = trim( $Module->redirectionURI( 'owfiltersearch', 'empty_attributes', array(
        $contentClass->attribute( 'identifier' ),
        implode( ',', $attributeFilterList )
    ) ), '/' );
    $tpl->setVariable( 'page_uri', $page_uri );
    $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
}

$Result['content'] = $tpl->fetch( 'design:owfiltersearch/empty_attributes.tpl' );
$Result['left_menu'] = 'design:owfiltersearch/menu.tpl';

if( function_exists( 'ezi18n' ) ) {
    $Result['path'] = array( array(
            'url' => 'filtersearch/list',
            'text' => ezi18n( 'design/admin/parts/owfiltersearch/menu', 'Empty attributes' )
        ) );

} else {
    $Result['path'] = array( array(
            'url' => 'filtersearch/list',
            'text' => ezpI18n::tr( 'design/admin/parts/owfiltersearch/menu', 'Empty attributes' )
        ) );

}
