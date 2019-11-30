<table width="100%" border="0">
    <{foreach item=page from=$block.pages}>     <{* Note, you can also use page.up_text to display the page's text *}>
        <tr class="<{cycle values="even,odd"}>">
            <td><{$page.user_name}></td>
            <td><a href="<{$page.up_url_rewrited}>" title="<{$page.up_href_title}>"><{$page.up_title}></a></td>
            <td align="right"><{$page.up_created_formated}></td>
        </tr>
    <{/foreach}>
</table>
