<?php

class OWFilterSearchMissingTranslations {
    protected $_filterParams;
    protected $_contentClass;
    protected $_missingTranslationFilters;
    protected $_missingTranslationFilterType;
    protected $_existingTranslationFilters;
    protected $_existingTranslationFilterType;

    public function __construct( $filterParams ) {
        $this->_filterParams = $filterParams;
        $this->_contentClass = eZContentClass::fetchByIdentifier( $filterParams['ClassFilter'] );
        $this->_missingTranslationFilters = isset( $filterParams['MissingTranslationFilters'] ) ? $filterParams['MissingTranslationFilters'] : array( );
        $this->_missingTranslationFilterType = isset( $filterParams['MissingTranslationFilterType'] ) ? $filterParams['MissingTranslationFilterType'] : "AND";
        $this->_existingTranslationFilters = isset( $filterParams['ExistingTranslationFilters'] ) ? $filterParams['ExistingTranslationFilters'] : array( );
        $this->_existingTranslationFilterType = isset( $filterParams['ExistingTranslationFilterType'] ) ? $filterParams['ExistingTranslationFilterType'] : "AND";
    }

    public function getResults( $limit = null ) {
        $result = array(
            'nodes' => array( ),
            'count' => 0
        );
        $def = eZContentObjectTreeNode::definition( );
        $field_filters = array( );
        $conds = null;
        $sorts = array( "ezcontentobject.name" => 'asc' );
        $asObject = true;
        $grouping = array( 'ezcontentobject_tree.main_node_id' );
        $custom_tables = array( 'ezcontentobject' );
        $custom_conds = null;

        $whereList = array(
            "ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id",
            "ezcontentobject.id = ezcontentobject_tree.contentobject_id",
            "ezcontentobject.contentclass_id = " . $this->_contentClass->attribute( 'id' )
        );

        $missingTranslationFilters = array( );
        foreach( $this->_missingTranslationFilters as $translationCode ) {
            $language = eZContentLanguage::fetchByLocale( $translationCode );
            $missingTranslationFilters[] = sprintf( "( ezcontentobject.language_mask & %d ) != %d", $language->attribute( 'id' ), $language->attribute( 'id' ) );
        }

        $existingTranslationFilters = array( );
        foreach( $this->_existingTranslationFilters as $translationCode ) {
            $language = eZContentLanguage::fetchByLocale( $translationCode );
            $existingTranslationFilters[] = sprintf( "( ezcontentobject.language_mask & %d ) = %d", $language->attribute( 'id' ), $language->attribute( 'id' ) );
        }

        // empty attributs
        if( !empty( $missingTranslationFilters ) ) {
            if( $this->_missingTranslationFilterType == "OR" ) {
                $whereList[] = implode( ' OR ', $missingTranslationFilters );
            } else {
                $whereList[] = implode( ' AND ', $missingTranslationFilters );
            }
        }

        // filled attributs
        if( !empty( $existingTranslationFilters ) ) {
            if( $this->_existingTranslationFilterType == "OR" ) {
                $whereList[] = implode( ' OR ', $existingTranslationFilters );
            } else {
                $whereList[] = implode( ' AND ', $existingTranslationFilters );
            }
        }

        if( empty( $whereList ) ) {
            return $results;
        } else {
            $whereList = array_unique( $whereList );
            $custom_conds = ' WHERE ( ' . implode( ' ) ' . PHP_EOL . ' AND ( ', $whereList ) . ' )';
        }
        $custom_fields = array(
            'ezcontentobject_tree.node_id',
            'ezcontentobject_tree.parent_node_id',
            'ezcontentobject_tree.main_node_id',
            'ezcontentobject_tree.contentobject_id',
            'ezcontentobject_tree.contentobject_version',
            'ezcontentobject_tree.contentobject_is_published',
            'ezcontentobject_tree.depth',
            'ezcontentobject_tree.sort_field',
            'ezcontentobject_tree.sort_order',
            'ezcontentobject_tree.priority',
            'ezcontentobject_tree.modified_subnode',
            'ezcontentobject_tree.path_string',
            'ezcontentobject_tree.path_identification_string',
            'ezcontentobject_tree.remote_id',
            'ezcontentobject_tree.is_hidden',
            'ezcontentobject_tree.is_invisible',
        );
        $custom_tables = array_unique( $custom_tables );
        $results['nodes'] = @eZPersistentObject::fetchObjectList( $def, $field_filters, $conds, $sorts, $limit, $asObject, $grouping, $custom_fields, $custom_tables, $custom_conds );
        $custom_fields = array( array(
                'operation' => 'count( * )',
                'name' => 'count'
            ) );
        $rows = @eZPersistentObject::fetchObjectList( $def, $field_filters, $conds, false, null, false, false, $custom_fields, $custom_tables, $custom_conds );
        $results['count'] = $rows[0]['count'];
        return $results;
    }

}
