<div class="group-redirect browse">
    <p>
        <?=lang('redirects_index_intro')?>
    </p>
    <div class="table-responsive">
        <?=form_open()?>
        <?=form_hidden('process', true)?>
        <table class="blank redirect-table">
            <thead>
                <tr>
                    <th class="old_url"><?=lang('redirects_index_th_old')?></th>
                    <th class="new_url"><?=lang('redirects_index_th_new')?></th>
                    <th class="type"><?=lang('redirects_index_th_type')?></th>
                    <th class="remove"></th>
                </tr>
            </thead>
            <tbody data-bind="foreach: redirects">
                <tr>
                    <td class="new_url">
                        <input type="text" data-bind="value: old_url" name="old_url[]" placeholder="/old/slug/here">
                    </td>
                    <td class="old_url">
                        <input type="text" data-bind="value: new_url" name="new_url[]" value="" placeholder="http://www.newurl.com/new/slug/here">
                    </td>
                    <td class="type">
                        <select name="type[]" data-bind="
                            select2,
                            options: $root.redirectTypes,
                            optionsValue: 'code',
                            optionsText: 'label',
                            value: type"></select>
                    </td>
                    <td class="remove text-center">
                        <a href="#" data-bind="click: $root.removeRedirect">
                            <span class="fa fa-minus-circle"></span>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>
            <a href="#" class="btn btn-sm btn-warning btn-block" data-bind="click: addRedirect">
                <span class="fa fa-plus fa-lg"></span> Add Row
            </a>
        </p>
        <hr />
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
        <?=form_close()?>
    </div>
</div>