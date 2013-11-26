{ezcss_require( 'owfiltersearch.css' )}
{def $class_list = fetch( 'class', 'list', hash(
        'sort_by', array( 'name', true() )
    ) )}
<div id="missing_translations">
    <div class="context-block">
        <div class="box-header">
            <div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
                <h1 class="context-title">{'Missing translations'|i18n('owfiltersearch/missing_translations' )}</h1>
                <div class="header-mainline"></div>
            </div></div></div></div></div>
        </div>
        <div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
            <div class="box-content">
                <div class="context-attributes">
                    <div class="block">
                        <form action={'owfiltersearch/missing_translations'|ezurl()} method="POST">
                            <fieldset>
                                <legend>{'Filters'|i18n('owfiltersearch/missing_translations' )}</legend>
                                <label>{'Content class'|i18n('owfiltersearch/missing_translations' )}</label>
                                <select name="ClassFilter" class="class_filter_field">
                                    {foreach $class_list as $class}
                                        <option value="{$class.identifier}" {if $class_filter|eq($class.identifier)}selected="selected"{/if}>{$class.name}</option>
                                    {/foreach}
                                </select>
                                <hr />
                                <label>{'Missing translations'|i18n('owfiltersearch/missing_translations' )}</label>
                                <select name="MissingTranslationFilters[]" multiple class="missing_translation_filter_field">
                                    {foreach fetch( 'content', 'translation_list' ) as $locale}
                                        <option value="{$locale.locale_code}" {if $missing_translation_filters|contains( $locale.locale_code )}selected="selected"{/if}>{$locale.country_name} ({$locale.locale_code})</option>
                                    {/foreach}
                                </select>
                                <input type="radio" name="MissingTranslationFilterType" value="OR" id="MissingTranslationFilterType_or" {if $missing_translation_filter_type|eq('OR')}checked="checked"{/if} />
                                <label for="MissingTranslationFilterType_or" class="inline">{"At least one translation is missing"|i18n('owfiltersearch/missing_translations' )}</label>
                                <input type="radio" name="MissingTranslationFilterType" value="AND" id="MissingTranslationFilterType_and" {if $missing_translation_filter_type|eq('AND')}checked="checked"{/if} />
                                <label for="MissingTranslationFilterType_and" class="inline">{"All translations are missing"|i18n('owfiltersearch/missing_translations' )}</label>
                                <hr />
                                <label>{'Existing translations'|i18n('owfiltersearch/missing_translations' )}</label>
                                <select name="ExistingTranslationFilters[]" multiple class="missing_translation_filter_field">
                                    {foreach fetch( 'content', 'translation_list' ) as $locale}
                                        <option value="{$locale.locale_code}" {if $existing_translation_filters|contains( $locale.locale_code )}selected="selected"{/if}>{$locale.country_name} ({$locale.locale_code})</option>
                                    {/foreach}
                                </select>
                                <input type="radio" name="ExistingTranslationFilterType" value="OR" id="ExistingTranslationFilterType_or" {if $empty_attribute_filter_type|eq('OR')}checked="checked"{/if} />
                                <label for="ExistingTranslationFilterType_or" class="inline">{"At least one translation exists"|i18n('owfiltersearch/missing_translations' )}</label>
                                <input type="radio" name="ExistingTranslationFilterType" value="AND" id="ExistingTranslationFilterType_and" {if $empty_attribute_filter_type|eq('AND')}checked="checked"{/if} />
                                <label for="ExistingTranslationFilterType_and" class="inline">{"All translations exist"|i18n('owfiltersearch/missing_translations' )}</label>
                                <hr />
                                <div class="block">
                                    <input class="button filter_button" type="submit" name="FilterButton" value="{'Search'|i18n('owfiltersearch/all')}"/>
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
                                        <a href={concat( '/user/preferences/set/owfiltersearch_missing_translations_limit/1/', $page_uri )|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
                                        <span class="current">25</span>
                                        <a href={concat( '/user/preferences/set/owfiltersearch_missing_translations_limit/3/', $page_uri )|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
                                    {/case}
                                
                                    {case match=50}
                                        <a href={concat( '/user/preferences/set/owfiltersearch_missing_translations_limit/1/', $page_uri )|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
                                        <a href={concat( '/user/preferences/set/owfiltersearch_missing_translations_limit/2/', $page_uri )|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
                                        <span class="current">50</span>
                                    {/case}
                                
                                    {case}
                                        <span class="current">10</span>
                                        <a href={concat( '/user/preferences/set/owfiltersearch_missing_translations_limit/2/', $page_uri )|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
                                        <a href={concat( '/user/preferences/set/owfiltersearch_missing_translations_limit/3/', $page_uri )|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
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
                                {"%1 results"|i18n('owfiltersearch/all',,hash( '%1', $result_count ) )}
                                <table class="list result_list">
                                    <thead>
                                        <tr class="yui-dt-first yui-dt-last">
                                            <th class="missing_translations_content"><div class="yui-dt-liner">{'Name'|i18n('owfiltersearch/all' )}</div></th>
                                            <th class="missing_translations_modified"><div class="yui-dt-liner">{'Modified'|i18n('owfiltersearch/all' )}</div></th>
                                            <th class="missing_translations_action"><div class="yui-dt-liner">&nbsp;</div></th>
                                        </tr>
                                    </thead>
                                    <tbody class="yui-dt-data">
                                        {foreach $results as $result sequence array( 'yui-dt-even', 'yui-dt-odd' ) as $style}
                                            <tr class="{if $index|eq(0)}yui-dt-first{/if} {$style}">
                                                <td class="missing_translations_content"><div class="yui-dt-liner"><a href={$result.url_alias|ezurl()}>{$result.name}</a></div></td>
                                                <td class="missing_translations_modified">
                                                    {$result.object.modified|l10n(datetime)}
                                                </td>
                                                <td class="missing_translations_action">
                                                    <a href={concat( '/content/edit/', $result.object.id )|ezurl()}><img src={'edit.gif'|ezimage()} alt="{'Modify'|i18n( 'owfiltersearch/all')}" /></a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            {else}
                                {"No result"|i18n('owfiltersearch/all')}
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