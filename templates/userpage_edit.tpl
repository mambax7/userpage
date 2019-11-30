<h3><{$smarty.const._USERPAGE_YOUR_PAGE}></h3>
<br>
<form method="post" name="userpage_edit" id="userpage_edit" action="<{$xoops_url}>/modules/userpage/userpage_edit.php">
    <table border="0">
        <tr>
            <td><{$smarty.const._USERPAGE_TITLE}></td>
            <td><input type="text" name="up_title" value="<{$up_title}>" size="50" maxlength="255"></td>
        </tr>
        <tr>
            <td><{$smarty.const._USERPAGE_CONTENT}></td>
            <td><{$editor}></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="hidden" name="up_pageid" value="<{$up_pageid}>"><input type="hidden" name="op" value="save"><input type="reset" name="raz" value="<{$smarty.const._CANCEL}>">&nbsp;<input type="submit" name="go" value="<{$smarty.const._SUBMIT}>"></td>
        </tr>
    </table>
</form>
