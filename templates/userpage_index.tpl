<{if $allowrss}>
    <div align='right'><a href="<{$xoops_url}>/modules/userpage/rss.php" title="<{$smarty.const._USERPAGE_RSS_FEED}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/rss.gif" border="0" alt="<{$smarty.const._USERPAGE_RSS_FEED}>"></a></div><{/if}>
<h3><{$smarty.const._USERPAGE_YOUR_PAGE}></h3>
<br>
<h4><{$up_title}></h4>
<{if $up_text}>
    <{$up_text}>
<{else}>
    <div style='text-align:center; font-weight: bold;'><{$smarty.const._USERPAGE_EMPTY_PAGE}></div>
<{/if}>
<br><br>
<div align="right">
    <{if $up_dateformated != ''}><a href="#" title="<{$smarty.const._USERPAGE_CREATION_DATE}> <{$up_dateformated}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/calendar.gif" border="0" alt="<{$smarty.const._USERPAGE_CREATION_DATE}> <{$up_dateformated}>"></a>&nbsp;<{/if}> <{if $up_hits != ''}>
        <a href="#" title="<{$smarty.const._USERPAGE_HITS}> <{$up_hits}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/view.gif" border="0" alt="<{$smarty.const._USERPAGE_HITS}> <{$up_hits}>"></a>&nbsp;<{/if}>
    <{if $currentuser}><a href="<{$xoops_url}>/modules/userpage/userpage_edit.php?op=edit" title="<{$smarty.const._USERPAGE_EDIT}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/edit.gif" border="0" alt="<{$smarty.const._USERPAGE_EDIT}>"></a>&nbsp;<{/if}>
    <{if $mail_cmd}><a href="<{$mail_cmd}>" title="<{$smarty.const._USERPAGE_EMAIL}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/friend.gif" border="0" alt="<{$smarty.const._USERPAGE_EMAIL}>"></a>&nbsp;<{/if}>
    <a href="<{$xoops_url}>/modules/userpage/print.php?page_id=<{$up_pageid}>" target="_blank" rel="nofollow" title="<{$smarty.const._USERPAGE_PRINTABLE}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/print.gif" border="0" alt="<{$smarty.const._USERPAGE_PRINTABLE}>"></a>&nbsp;
    <a href="<{$xoops_url}>/modules/userpage/makepdf.php?page_id=<{$up_pageid}>" target="_blank" rel="nofollow" title="<{$smarty.const._USERPAGE_PDF}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/pdf.gif" border="0" alt="<{$smarty.const._USERPAGE_PDF}>"></a>&nbsp;
    <a href="<{$xoops_url}>/modules/userpage/userpage_list.php" title="<{$smarty.const._USERPAGE_BOOK}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/search.gif" border="0" alt="<{$smarty.const._USERPAGE_BOOK}>"></a>&nbsp;
    <{if $is_admin}><a href="<{$xoops_url}>/modules/userpage/userpage_delete.php?id=<{$up_pageid}>" <{$confirm_delete}> title="<{$smarty.const._DELETE}>"><img src="<{$xoops_url}>/modules/userpage/assets/images/delete.gif" border="0" alt="<{$smarty.const._DELETE}>"></a>&nbsp;<{/if}>
</div>

<div style="text-align: center; padding: 3px; margin:3px;">
    <{$commentsnav}>
    <{$lang_notice}>
</div>

<div style="margin:3px; padding: 3px;">
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
</div>
