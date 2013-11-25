{ezcss_require( 'owfiltersearch.css' )}
{def $class_list = fetch( 'class', 'list', hash(
        'sort_by', array( 'name', true() )
    ) )}
<div id="empty_attributes">
    <div class="context-block">
        <div class="box-header">
            <div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
                <h1 class="context-title">{'Empty attributes'|i18n('owfiltersearch/empty_attributes' )}</h1>
                <div class="header-mainline"></div>
            </div></div></div></div></div>
        </div>
        <div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
            <div class="box-content">
                <div class="context-attributes">
                    <div class="block">
                        <form action={'owfiltersearch/empty_attributes'|ezurl()} method="POST">
                            <fieldset>
                                <legend>{'Filters'|i18n('owfiltersearch/empty_attributes' )}</legend>
                                <label>{'Content class'|i18n('owfiltersearch/empty_attributes' )}</label>
                                <select name="ClassFilter" class="class_filter_field">
                                    {foreach $class_list as $class}
                                        <option value="{$class.identifier}" {if $class_filter|eq($class.identifier)}selected="selected"{/if}>{$class.name}</option>
                                    {/foreach}
                                </select>
                                <label>{'Class attributes'|i18n('owfiltersearch/empty_attributes' )}</label>
                                {if is_set( $class_attribute_list )}
                                    <select name="AttributeFilters[]" multiple class="attributes_filter_field">
                                        {foreach $class_attribute_list as $class_attribute}
                                            <option value="{$class_attribute.identifier}" {if $attribute_filters|contains( $class_attribute.identifier )}selected="selected"{/if}>{$class_attribute.name}</option>
                                        {/foreach}
                                    </select>
                                {/if}
                                <input class="smallbutton select_class_button" type="submit" name="SelectClassButton" value="{'Update attributes'|i18n('owfiltersearch/empty_attributes')}"/>
                                <div class="block">
                                    <input class="button filter_button" type="submit" name="FilterButton" value="{'Search'|i18n('owfiltersearch/empty_attributes')}"/>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="context-attributes">
	                <div class="context-toolbar">
	                    <div class="block">
	                        <div class="left">
	                            <p>
	                                {switch match=$limit}
	                                {case match=25}
	                                    <a href={concat( '/user/preferences/set/owfiltersearch_empty_attributes_limit/1/', $page_uri )|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
	                                    <span class="current">25</span>
	                                    <a href={concat( '/user/preferences/set/owfiltersearch_empty_attributes_limit/3/', $page_uri )|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
	                                {/case}
	                            
	                                {case match=50}
	                                    <a href={concat( '/user/preferences/set/owfiltersearch_empty_attributes_limit/1/', $page_uri )|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
	                                    <a href={concat( '/user/preferences/set/owfiltersearch_empty_attributes_limit/2/', $page_uri )|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
	                                    <span class="current">50</span>
	                                {/case}
	                            
	                                {case}
	                                    <span class="current">10</span>
	                                    <a href={concat( '/user/preferences/set/owfiltersearch_empty_attributes_limit/2/', $page_uri )|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
	                                    <a href={concat( '/user/preferences/set/owfiltersearch_empty_attributes_limit/3/', $page_uri )|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
	                                {/case}
	                            
	                                {/switch}
	                            </p>
	                        </div>
	                        <div class="break"></div>
	                    </div>
	                </div>
                    <div class="block">
                        <div class="yui-dt">
                            {if $results}
                                {$result_count} resultas
	                            <table class="list result_list">
	                                <thead>
	                                    <tr class="yui-dt-first yui-dt-last">
	                                        <th class="empty_attributes_content"><div class="yui-dt-liner">{'Name'|i18n('owfiltersearch/empty_attributes' )}</div></th>
                                            <th class="empty_attributes_modified"><div class="yui-dt-liner">{'Modified'|i18n('owfiltersearch/empty_attributes' )}</div></th>
	                                        <th class="empty_attributes_action"><div class="yui-dt-liner">&nbsp;</div></th>
	                                    </tr>
	                                </thead>
	                                <tbody class="yui-dt-data">
	                                    {foreach $results as $result sequence array( 'yui-dt-even', 'yui-dt-odd' ) as $style}
	                                        <tr class="{if $index|eq(0)}yui-dt-first{/if} {$style}">
	                                            <td class="empty_attributes_content"><div class="yui-dt-liner"><a href={$result.url_alias|ezurl()}>{$result.name}</a></div></td>
	                                            <td class="empty_attributes_modified">
                                                    {$result.object.modified|l10n(datetime)}
												</td>
												<td class="empty_attributes_action">
												    <a href={concat( '/content/edit/', $result.object.id )|ezurl()}><img src={'edit.gif'|ezimage()} alt="{'Modify'|i18n( 'owfiltersearch/empty_attributes')}" /></a>
												</td>
	                                        </tr>
	                                    {/foreach}
	                                </tbody>
	                            </table>
                            {else}
                                no results
                            {/if}
                        </div>
                        <div class="context-toolbar">
							{include name=navigator uri='design:navigator/google.tpl'
							                        page_uri=$page_uri
							                        item_count=$result_count
							                        view_parameters=$view_parameters
							                        item_limit=$limit}
				        </div>
                    </div>
                </div>
            </div>
        </div></div></div></div></div></div>
    </div>
</div>
{undef}