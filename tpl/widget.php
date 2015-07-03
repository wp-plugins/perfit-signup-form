    <p>
        <label for="widget-optin-list"></label>
        <select name="<?php echo $this->get_field_name('optin_id')?>" style="width: 100%;">
            <option value="">------</option>
            <?php  if ($optins->data): 
                    foreach ($optins->data as $optin): 
            ?>
            <option value="<?php echo $optins->request->account?>:<?php echo $optin->pubId?>" <?php echo ($optins->request->account.':'.$optin->pubId == $optin_id)? 'selected="selected"' : ''?> ><?php echo $optin->name?></option>
            <?php      endforeach; 
                endif; 
            ?>
        </select>

        <?php  foreach ($optinModes as $k => $v): ?>
        <div class="radio">
            <label><input type="radio" name="<?php echo $this->get_field_name('optin_mode')?>" <?=($k == $optin_mode)? 'checked="checked"' : ''?> value="<?php echo $k?>"><?php echo $v?></label>
        </div>
        <?php  endforeach; ?>

<!--
        <select name="<?php echo $this->get_field_name('optin_mode')?>" style="width: 100%;">
            <?php  foreach ($optinModes as $k => $v): ?>
            <option value="<?php echo $k?>" <?php echo ($k == $optin_mode)? 'selected="selected"' : ''?> ><?php echo $v?></option>
            <?php  endforeach; ?>
        </select>
-->

    </p>

