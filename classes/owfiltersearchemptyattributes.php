<?php

class OWFilterSearchEmptyAttributes {
    protected $_contentClass;
    protected $_attributeList;
    protected $_filter;

    public function __construct( $contentClass, $attributeList, $filter = 'AND' ) {
        $this->_contentClass = $contentClass;
        $this->_attributeList = $attributeList;
        $this->_filter = $filter;
    }

    public function getResults( $limit = null ) {
        $result = array(
            'results' => array( ),
            'result_count' => 0
        );

        $def = eZContentObjectTreeNode::definition( );
        $field_filters = array( );
        $conds = null;
        $sorts = array( "ezcontentobject.name" => 'asc' );
        $asObject = true;
        $grouping = false;
        $custom_tables = array( 'ezcontentobject' );
        $custom_conds = null;

        $whereList = array(
            "ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id",
            "ezcontentobject.id = ezcontentobject_tree.contentobject_id"
        );
        $filterAttributList = array( );
        foreach( $this->_attributeList as $filterCount => $attribute ) {
            $classAttribute = $this->_contentClass->fetchAttributeByIdentifier( $attribute );
            if( !$classAttribute instanceof eZContentClassAttribute ) {
                throw new Exception( "Attribute '$attribute' not found in class " . $this->_contentClass->attribute( 'identifier' ) );
            }
            $filterAttributeID = $classAttribute->attribute( 'id' );
            $filterField = "attr_" . $filterAttributeID;

            // Use the same joins as we do when sorting,
            // if more attributes are filtered by we will append them
            $custom_tables[] = "ezcontentobject_attribute $filterField";
            $whereList[] = "$filterField.contentobject_id = ezcontentobject_tree.contentobject_id AND $filterField.contentclassattribute_id = $filterAttributeID AND $filterField.version = ezcontentobject_tree.contentobject_version";
            switch( $classAttribute->attribute( 'data_type_string' ) ) {
                case 'ezxmltext' :
                    $filterAttributList[] = "trim( TRAILING '\\n' from $filterField.data_text ) not like '%</section>'";
                    break;
                case 'ezkeyword' :
                    $filterAttributList[] = "$filterField.id NOT IN (SELECT DISTINCT objectattribute_id FROM ezkeyword_attribute_link WHERE objectattribute_id = $filterField.id)";
                    break;
                case 'sckenhancedselection' :
                    $filterAttributList[] = "$filterField.data_text = 'a:0:{}'";
                    break;
                case 'ezobjectrelationlist' :
                    $filterAttributList[] = "$filterField.data_text NOT LIKE  '%<relation-item %'";
                    break;
                default :
                    $filterAttributList[] = "( $filterField.data_int = 0 OR $filterField.data_int IS NULL ) AND ( $filterField.data_float = 0 OR $filterField.data_float IS NULL ) AND ( $filterField.data_text = '' OR $filterField.data_text IS NULL )";
                    break;
            }
        }
        if( !empty( $filterAttributList ) ) {
            $filterAttribut = '( ' . implode( ' ) ' . $this->_filter . ' ( ', $filterAttributList ) . ' )';
            $whereList[] = $filterAttribut;
        }
        if( !empty( $whereList ) ) {
            $custom_conds = ' WHERE ( ' . implode( ' ) AND ( ', $whereList ) . ' )';
        }

        $custom_fields = array(
            'DISTINCT( ezcontentobject_tree.node_id )',
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
        $results['nodes'] = eZPersistentObject::fetchObjectList( $def, $field_filters, $conds, $sorts, $limit, $asObject, $grouping, $custom_fields, $custom_tables, $custom_conds );
        $custom_fields = array( array(
                'operation' => 'count( DISTINCT ezcontentobject_tree.main_node_id )',
                'name' => 'count'
            ) );
        $rows = eZPersistentObject::fetchObjectList( $def, $field_filters, $conds, false, null, false, false, $custom_fields, $custom_tables, $custom_conds );
        $results['count'] = $rows[0]['count'];
        return $results;
    }

}
