{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
  
{if (!$chartEnabled || !$chartSupported )&& $rows}
    {if $pager and $pager->_response and $pager->_response.numPages > 1}
        <div class="report-pager">
            {include file="CRM/common/pager.tpl" location="top" noForm=0}
        </div>
    {/if}
    <table class="report-layout display">
        {capture assign="tableHeader"}
            {foreach from=$columnHeaders item=header key=field}
                {assign var=class value=""}
                {if $header.type eq 1024 OR $header.type eq 1}
                {assign var=class value="class='reports-header-right'"}
                {else}
                    {assign var=class value="class='reports-header'"}
                {/if}
                {if !$skip}
                   {if $header.colspan}
                       <th colspan={$header.colspan}>{$header.title}</th>
                      {assign var=skip value=true}
                      {assign var=skipCount value=`$header.colspan`}
                      {assign var=skipMade  value=1}
                   {else}
                       <th {$class}>{$header.title}</th>
                   {assign var=skip value=false}
                   {/if}
                {else} {* for skip case *}
                   {assign var=skipMade value=`$skipMade+1`}
                   {if $skipMade >= $skipCount}{assign var=skip value=false}{/if}
                {/if}
            {/foreach}
        {/capture}

        {if !$sections} {* section headers and sticky headers aren't playing nice yet *}
            <thead class="sticky">
            <tr>
                {$tableHeader}
        </tr>
        </thead>
        {/if}

        {* pre-compile section header here, rather than doing it every time under foreach *}
        {capture assign=sectionHeaderTemplate}
            {assign var=columnCount value=$columnHeaders|@count}
            {assign var=l value=$smarty.ldelim}
            {assign var=r value=$smarty.rdelim}
            {foreach from=$sections item=section key=column name=sections}
                {counter assign="h"}
                {$l}isValueChange value=$row.{$column} key="{$column}" assign=isValueChanged{$r}
                {$l}if $isValueChanged{$r}

                    {$l}if $sections.{$column}.type & 4{$r}
                        {$l}assign var=printValue value=$row.{$column}|crmDate{$r}
                    {$l}elseif $sections.{$column}.type eq 1024{$r}
                        {$l}assign var=printValue value=$row.{$column}|crmMoney{$r}
                    {$l}else{$r}
                        {$l}assign var=printValue value=$row.{$column}{$r}
                    {$l}/if{$r}

                    <tr><th colspan="{$columnCount}">
                        <h{$h}>{$section.title}: {$l}$printValue|default:"<em>none</em>"{$r}
                            ({$l}sectionTotal key=$row.{$column} depth={$smarty.foreach.sections.index}{$r})
                        </h{$h}>
                    </th></tr>
                    {if $smarty.foreach.sections.last}
                        <tr>{$l}$tableHeader{$r}</tr>
                    {/if}
                {$l}/if{$r}
            {/foreach}
        {/capture}

        {foreach from=$rows item=row key=rowid}
          {eval var=$sectionHeaderTemplate}
          {if isset($row.total)}
            <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}"><td colspan="42"><hr /></td></tr>
            <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}">
              <td colspan="{$row.col_span}"><strong>EINDTOTAAL</strong></td>
              <td><strong>{$row.total}</strong></td>
            </tr>
          {else}  
            {if $row.last eq 1}
              <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}">
                <td colspan="{$row.col_span}"><strong>Totaal {$row.previous}</strong></td>
                <td><strong>{$row.total_count}</strong></td>
              </tr>
            {else}  
              {if $row.level_break eq 1}
                {if $row.total_count > 0}
                  <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}">
                    <td colspan="{$row.col_span}"><strong>Totaal {$row.previous}</strong></td>
                    <td><strong>{$row.total_count}</strong></td>
                  </tr>
                {/if}  
                <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}"><td colspan="42"><hr /></td></tr>
              {/if}
              <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}">
                {foreach from=$columnHeaders item=header key=field}
                  <td class="crm-report-{$field} report-contents-left">
                    {$row.$field}
                  </td>
                {/foreach}
              </tr>
            {/if}
          {/if}
        {/foreach}
        {if $row.total_count > 0 and $row.last ne 1}
          <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}">
            <td colspan="{$row.col_span}"><strong>Totaal {$row.current}</strong></td>
            <td><strong>{$row.total_count}</strong></td>
          </tr>
        {/if}  
        <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$rowid}"><td colspan="42"><hr /></td></tr>

    </table>
    {if $pager and $pager->_response and $pager->_response.numPages > 1}
        <div class="report-pager">
            {include file="CRM/common/pager.tpl"  noForm=0}
        </div>
    {/if}
{/if}
