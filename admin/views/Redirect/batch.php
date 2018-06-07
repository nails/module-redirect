<?=form_open_multipart()?>
<p>
    <strong>1. CSV File</strong>
</p>
<p>
    This should be a *.csv file which contains 3 columns: old URL, new URL, type of redirect (301 or 302).
</p>
<p>
    <input type="file" name="upload" style="border: 1px solid #CCC;padding: 1rem; width: 100%;margin-bottom: 1rem">
</p>
<p>
    <strong>2. Action</strong>
</p>
<p>
    Define how to process the redirects.
</p>
<p>
    <select name="action" class="select2">
        <option value="APPEND">Append - add new items in the CSV to the database</option>
        <option value="REPLACE">Replace - replace the items in the database with those in the CSV</option>
        <option value="REMOVE">Remove - remove items in the CSV from the database</option>
    </select>
</p>
<hr>
<div class="admin-floating-controls">
    <button type="submit" class="btn btn-primary">
        Save Changes
    </button>
</div>
<?=form_close()?>
