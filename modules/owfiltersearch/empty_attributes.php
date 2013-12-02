<?php

$Module = $Params["Module"];
if ( is_callable( 'eZTemplate::factory' ) ) {
	$tpl = eZTemplate::factory();
} else {
	include_once ('kernel/common/template.php');
	$tpl = templateInit();
}

$filterParams = array();
foreach ( $Module->Functions[$Module->currentView()]['unordered_params'] as $parameter ) {
	if ( !empty( $Params[$parameter] ) ) {
		switch ( $parameter ) {
			case 'EmptyAttributeFilters':
			case 'FilledAttributeFilters':
				$filterParams[$parameter] = explode( ',', $Params[$parameter] );
				break;
			default :
				$filterParams[$parameter] = $Params[$parameter];
				break;
		}
	}
}
if ( $Module->currentAction() != FALSE ) {
	foreach ( $Module->Functions[$Module->currentView()]['post_action_parameters'][$Module->currentAction()] as $parameter ) {
		if ( $Module->hasActionParameter( $parameter ) ) {
			$filterParams[$parameter] = $Module->actionParameter( $parameter );
		}
	}
}

$tpl->setVariable( 'empty_attribute_filter_type', 'AND' );
$tpl->setVariable( 'filled_attribute_filter_type', 'AND' );
$tpl->setVariable( 'translation_filter', '' );

foreach ( $filterParams as $variableName => $value ) {
	$variableName = strtolower( preg_replace( '/\B([A-Z])/', '_$1', $variableName ) );
	$tpl->setVariable( $variableName, $value );
}

// if we know the content class, fill the list of its attributes
if ( isset( $filterParams['ClassFilter'] ) ) {
	$contentClass = eZContentClass::fetchByIdentifier( $filterParams['ClassFilter'] );
	if ( $contentClass instanceof eZContentClass ) {
		$tpl->setVariable( 'class_attribute_list', $contentClass->fetchAttributes() );
	}
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
$tpl->setVariable( 'limit', $length );

if ( !empty( $filterParams ) && isset( $filterParams['ClassFilter'] ) && (isset( $filterParams['EmptyAttributeFilters'] ) || isset( $filterParams['FilledAttributeFilters'] )) ) {
	$filter = new OWFilterSearchEmptyAttributes( $filterParams );
	$offset = $Params['Offset'];
	if ( !is_numeric( $offset ) ) {
		$offset = 0;
	}


	$results = $filter->getResults( array(
		'offset' => $offset,
		'length' => $length
			) );
	$tpl->setVariable( 'results', $results['nodes'] );
	$tpl->setVariable( 'result_count', $results['count'] );
	$page_uri = trim( $Module->redirectionURI( 'owfiltersearch', 'empty_attributes' ), '/' );
	$tpl->setVariable( 'page_uri', $page_uri );
	$viewParameters = array(
		'offset' => $offset );
	foreach ( $filterParams as $key => $value ) {
		if ( !empty( $value ) ) {
			switch ( $key ) {
				case 'Offset':
					break;
				case 'EmptyAttributeFilters':
				case 'FilledAttributeFilters':
					$viewParameters[$key] = implode( ',', $value );
					break;
				default :
					$viewParameters[$key] = $value;
					break;
			}
		}
	}
	$tpl->setVariable( 'view_parameters', $viewParameters );
}

$Result['content'] = $tpl->fetch( 'design:owfiltersearch/empty_attributes.tpl' );
$Result['left_menu'] = 'design:owfiltersearch/menu.tpl';

if ( function_exists( 'ezi18n' ) ) {
	$Result['path'] = array(
		array(
			'text' => ezi18n( 'design/admin/parts/owfiltersearch/menu', 'Filter search' ) ),
		array(
			'url' => 'filtersearch/empty_attributes',
			'text' => ezi18n( 'design/admin/parts/owfiltersearch/menu', 'Empty attributes' )
		)
	);
} else {
	$Result['path'] = array(
		array(
			'text' => ezpI18n::tr( 'design/admin/parts/owfiltersearch/menu', 'Filter search' ) ),
		array(
			'url' => 'filtersearch/list',
			'text' => ezpI18n::tr( 'design/admin/parts/owfiltersearch/menu', 'Empty attributes' )
		)
	);
}
