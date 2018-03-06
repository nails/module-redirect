<p>
    Batch update the redirects table; old URLs on the left will redirect to the new URLs on the right. Make sure there
    is only 1 URL per line and that there is an equal number of URLs in either box.
</p>
<hr>
<?=form_open()?>
<table>
    <thead>
        <tr>
            <th>Old URLs</th>
            <th>New URLs</th>
            <th>Redirect Type</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="45%">
                <textarea name="old"><?=set_value('old', $sOldUrls)?></textarea>
            </td>
            <td width="*">
                <textarea name="new"><?=set_value('new', $sNewUrls)?></textarea>
            </td>
            <td width="125">
                <textarea name="type"><?=set_value('type', $sTypes)?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p>
    <button type="submit" class="btn btn-primary">
        Save Changes
    </button>
</p>
<?=form_close()?>
