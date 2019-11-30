<{if $allowrss}>
    <div align='right'>
        <a href="<{$smarty.const.USERPAGE_URL}>rss.php" title="<{$smarty.const._USERPAGE_RSS_FEED}>"><img src="<{$smarty.const.USERPAGE_IMAGES_URL}>rss.gif" border="0" alt="<{$smarty.const._USERPAGE_RSS_FEED}>"></a>
    </div>
<{/if}>

<h3><{$smarty.const._USERPAGE_BOOK}></h3>

<{if $pagenav !=''}>
    <div style="text-align: right; margin: 10px;">
        <{$pagenav}>
    </div>
<{/if}>

<table border="0" width="95%">
    <tr>
        <th align="center"><{$smarty.const._USERPAGE_USER}></th>
        <th align="center"><{$smarty.const._USERPAGE_TITLE}></th>
        <th align="center"><{$smarty.const._USERPAGE_DATE}></th>
        <th align="center"><{$smarty.const._USERPAGE_HITS}></th>
    </tr>
    <{foreach item=page from=$pages}>
        <tr class="<{cycle values="even,odd"}>" onclick="window.location='<{$page.up_url_rewrited}>'">
            <td><{$page.user_name}></td>
            <td><a href="<{$page.up_url_rewrited}>" href="<{$page.up_href_title}>"><{$page.up_title}></a></td>
            <td align="center"><{$page.up_created_formated}></td>
            <td align="center"><{$page.up_hits}></td>
        </tr>
    <{/foreach}>
</table>

<{if $pagenav !=''}>
    <div style="text-align: right; margin: 10px;">
        <{$pagenav}>
    </div>
<{/if}>
