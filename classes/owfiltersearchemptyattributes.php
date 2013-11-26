<?php

class OWFilterSearchEmptyAttributes {
    protected $_filterParams;
    protected $_contentClass;
    protected $_emptyAttributeFilters;
    protected $_emptyAttributeFilterType;
    protected $_filledAttributeFilters;
    protected $_filledAttributeFilterType;
    protected $_translationFilter;

    public function __construct( $filterParams ) {
        $this->_filterParams = $filterParams;
        $this->_contentClass = eZContentClass::fetchByIdentifier( $filterParams['ClassFilter'] );
        $this->_emptyAttributeFilters = isset( $filterParams['EmptyAttributeFilters'] ) ? $filterParams['EmptyAttributeFilters'] : array( );
        $this->_emptyAttributeFilterType = isset( $filterParams['EmptyAttributeFilterType'] ) ? $filterParams['EmptyAttributeFilterType'] : "AND";
        $this->_filledAttributeFilters = isset( $filterParams['FilledAttributeFilters'] ) ? $filterParams['FilledAttributeFilters'] : array( );
        $this->_filledAttributeFilterType = isset( $filterParams['FilledAttributeFilterType'] ) ? $filterParams['FilledAttributeFilterType'] : "AND";
        $this->_translationFilter = isset( $filterParams['TranslationFilter'] ) ? $filterParams['TranslationFilter'] : "";
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
        // empty attributs
        if( $this->_emptyAttributeFilterType == "OR" ) {
            $custom_tables[] = 'ezcontentobject_attribute';
            $whereList[] = "ezcontentobject_attribute.contentobject_id = ezcontentobject.id";
            $whereList[] = "ezcontentobject_attribute.version = ezcontentobject_tree.contentobject_version";
            if( !empty( $this->_translationFilter ) ) {
                $whereList[] = sprintf( "ezcontentobject_attribute.language_code = '%s'", $this->_translationFilter );
            }
            eZDebug::writeDebug( 'emptyAttributeFilterType = OR' );
            $filterAttributList = array( );
            foreach( $this->_emptyAttributeFilters as $attribute ) {
                $classAttribute = $this->_contentClass->fetchAttributeByIdentifier( $attribute );
                $filterAttributList[] = sprintf( "ezcontentobject_attribute.contentclassattribute_id = %s AND ( %s )", $classAttribute->attribute( 'id' ), $this->getEmptyAttributeQueryPart( $classAttribute ) );
            }
            if( !empty( $filterAttributList ) ) {
                $filterAttribut = '( ' . implode( ' ) ' . $this->_emptyAttributeFilterType . ' ( ', $filterAttributList ) . ' )';
                $whereList[] = $filterAttribut;
            }
        } else {
            eZDebug::writeDebug( 'emptyAttributeFilterType = AND' );
            $filterAttributListPart1 = array( );
            $filterAttributListPart2 = array( );
            foreach( $this->_emptyAttributeFilters as $filterCount => $attribute ) {
                $classAttribute = $this->_contentClass->fetchAttributeByIdentifier( $attribute );
                $filterAttributeID = $classAttribute->attribute( 'id' );
                $filterField = "attr_" . $filterAttributeID;
                $custom_tables[] = "ezcontentobject_attribute $filterField";
                $whereList[] = "$filterField.contentobject_id = ezcontentobject_tree.contentobject_id AND $filterField.contentclassattribute_id = $filterAttributeID AND $filterField.version = ezcontentobject_tree.contentobject_version";
                if( !empty( $this->_translationFilter ) ) {
                    $whereList[] = sprintf( "$filterField.language_code = '%s'", $this->_translationFilter );
                }
                $whereList[] = $this->getEmptyAttributeQueryPart( $classAttribute, $filterField );
            }
        }

        // filled attributs
        if( $this->_filledAttributeFilterType == "OR" ) {
            $custom_tables[] = 'ezcontentobject_attribute';
            $whereList[] = "ezcontentobject_attribute.contentobject_id = ezcontentobject.id";
            $whereList[] = "ezcontentobject_attribute.version = ezcontentobject_tree.contentobject_version";
            if( !empty( $this->_translationFilter ) ) {
                $whereList[] = sprintf( "ezcontentobject_attribute.language_code = '%s'", $this->_translationFilter );
            }
            eZDebug::writeDebug( 'filledAttributeFilters = OR' );
            $filterAttributList = array( );
            foreach( $this->_filledAttributeFilters as $attribute ) {
                $classAttribute = $this->_contentClass->fetchAttributeByIdentifier( $attribute );
                $filterAttributList[] = sprintf( "ezcontentobject_attribute.contentclassattribute_id = %s AND ( %s )", $classAttribute->attribute( 'id' ), $this->getFilledAttributeQueryPart( $classAttribute ) );
            }
            if( !empty( $filterAttributList ) ) {
                $filterAttribut = '( ' . implode( ' ) ' . $this->_filledAttributeFilterType . ' ( ', $filterAttributList ) . ' )';
                $whereList[] = $filterAttribut;
            }
        } else {
            eZDebug::writeDebug( 'filledAttributeFilters = AND' );
            $filterAttributListPart1 = array( );
            $filterAttributListPart2 = array( );
            foreach( $this->_filledAttributeFilters as $filterCount => $attribute ) {
                $classAttribute = $this->_contentClass->fetchAttributeByIdentifier( $attribute );
                $filterAttributeID = $classAttribute->attribute( 'id' );
                $filterField = "attr_" . $filterAttributeID;
                $custom_tables[] = "ezcontentobject_attribute $filterField";
                $whereList[] = "$filterField.contentobject_id = ezcontentobject_tree.contentobject_id AND $filterField.contentclassattribute_id = $filterAttributeID AND $filterField.version = ezcontentobject_tree.contentobject_version";
                if( !empty( $this->_translationFilter ) ) {
                    $whereList[] = sprintf( "$filterField.language_code = '%s'", $this->_translationFilter );
                }
                $whereList[] = $this->getFilledAttributeQueryPart( $classAttribute, $filterField );
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

    protected function getEmptyAttributeQueryPart( $classAttribute, $tablePrefix = 'ezcontentobject_attribute' ) {
        switch( $classAttribute->attribute( 'data_type_string' ) ) {
            case 'ezxmltext' :
                return "trim( TRAILING '\\n' from $tablePrefix.data_text ) NOT LIKE '%</section>'";
                break;
            case 'ezkeyword' :
                return "$tablePrefix.id NOT IN (SELECT DISTINCT objectattribute_id FROM ezkeyword_attribute_link WHERE objectattribute_id = $tablePrefix.id)";
                break;
            case 'sckenhancedselection' :
                return "$tablePrefix.data_text LIKE 'a:0:{}'";
                break;
            case 'ezobjectrelationlist' :
                return "$tablePrefix.data_text NOT LIKE  '%<relation-item %'";
                break;
            default :
                return "( $tablePrefix.data_int = 0 OR $tablePrefix.data_int IS NULL ) AND ( $tablePrefix.data_float = 0 OR $tablePrefix.data_float IS NULL ) AND ( $tablePrefix.data_text = '' OR $tablePrefix.data_text IS NULL )";
                break;
        }
    }

    protected function getFilledAttributeQueryPart( $classAttribute, $tablePrefix = 'ezcontentobject_attribute' ) {
        switch( $classAttribute->attribute( 'data_type_string' ) ) {
            case 'ezxmltext' :
                return "trim( TRAILING '\\n' from $tablePrefix.data_text ) LIKE '%</section>'";
                break;
            case 'ezkeyword' :
                return "$tablePrefix.id IN (SELECT DISTINCT objectattribute_id FROM ezkeyword_attribute_link WHERE objectattribute_id = $tablePrefix.id)";
                break;
            case 'sckenhancedselection' :
                return "$tablePrefix.data_text NOT LIKE 'a:0:{}'";
                break;
            case 'ezobjectrelationlist' :
                return "$tablePrefix.data_text LIKE  '%<relation-item %'";
                break;
            default :
                return "( $tablePrefix.data_int IS NOT NULL AND $tablePrefix.data_int > 0 ) OR ( $tablePrefix.data_float IS NOT NULL AND $tablePrefix.data_float > 0 ) OR ( $tablePrefix.data_text != '' AND $tablePrefix.data_text IS NOT NULL )";
                break;
        }
    }

}
