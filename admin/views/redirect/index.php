<div class="group-redirect browse">
    <p>
        <?=lang('redirects_index_intro')?>
    </p>
    <div class="table-responsive">
        <?=form_open()?>
        <table class="blank redirect-table">
            <thead>
                <tr>
                    <th><?=lang('redirects_index_th_old')?></th>
                    <th><?=lang('redirects_index_th_new')?></th>
                    <th width="220"><?=lang('redirects_index_th_type')?></th>
                    <th width="50"></th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($redirects) {

                    $count = 0;
                    foreach ($redirects as $redirect) {

                        echo '<tr>';
                            echo '<td>';

                                echo form_input('old[]', set_value('old[' . $count . ']', $redirect->old));

                            echo '</td>';
                            echo '<td>';

                                echo form_input('new[]', set_value('new[' . $count . ']', $redirect->new));

                            echo '</td>';
                            echo '<td>';

                                echo form_dropdown(
                                    'type[]',
                                    array(
                                        '301' => '301 - Moved Permanently',
                                        '302' => '302 - Moved Temporarily'
                                    ),
                                    set_value('type[' . $count . ']', $redirect->type),
                                    'class="select2"'
                                );

                            echo '</td>';
                            echo '<td class="actions">';

                                if (userHasPermission('admin:redirect:redirect:delete')) :

                                    ?>

                                        <a href="#" class="delete">
                                            <span class="fa fa-minus-circle"></span>
                                        </a>

                                    <?php

                                endif;

                            echo '</td>';
                        echo '<tr>';
                        $count++;
                    }

                }

                ?>
                <tr>
                    <td colspan="4" class="add-row">
                        <a href="#" id="add"><span class="fa fa-plus"></span> Add Row</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    <?=form_close()?>
    </div>
</div>
<script type="text/template" id="template-row">
    <tr>
        <td>
            <input type="text" name="old[]" value="" placeholder="/old/slug/here">
        </td>
        <td>
            <input type="text" name="new[]" value="" placeholder="http://www.newurl.com/new/slug/here">
        </td>
        <td>
            <select name="type[]" class="select2">
                <option selected="selected" value="301">301 - Moved Permanently</option>
                <option value="302">302 - Moved Temporarily</option>
            </select>
        </td>
        <td class="text-center;">
            <a href="#" class="delete">
                <span class="fa fa-minus-circle"></span>
            </a>
        </td>
    </tr>
</script>