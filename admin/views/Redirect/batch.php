<?=form_open_multipart()?>
    <fieldset>
        <legend>CSV File</legend>
        <p>
            This should be a *.csv file which contains 3 columns: old URL, new URL, type of redirect (301 or 302).
        </p>
        <p>
            <input type="file" name="upload" style="border: 1px solid #CCC;padding: 1rem; width: 100%;margin-bottom: 0">
        </p>
    </fieldset>
    <fieldset>
        <legend>Action to perform</legend>
        <p>
            Define how to process the redirects.
        </p>
        <p>
            <select name="action" class="select2" style="width: 100%;">
                <option value="APPEND">Append - add new items in the CSV to the database</option>
                <option value="REPLACE">Replace - replace the items in the database with those in the CSV</option>
                <option value="REMOVE">Remove - remove items in the CSV from the database</option>
            </select>
        </p>
    </fieldset>
<?php

echo \Nails\Admin\Helper::floatingControls();
echo form_close();
