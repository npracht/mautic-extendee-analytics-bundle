<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<div class="analytics-header">
    <div class="col-xs-12 va-m mb-20">
        <div class="row">
            <?php foreach ($tags as $utm => $value) { ?>
                <div class="col-xs-12 col-sm-3">
                    <label><?php echo $utm; ?></label>
                    <select name="<?php echo $utm; ?>" class="form-control" autocomplete="false" multiple="multiple">
                        <?php foreach ($value as $tag) { ?>
                            <option selected="selected" value="<?php echo $tag; ?>"><?php echo $tag; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
            }
            ?>
            <i class="analytics-loading fa fa-spin fa-spinner"></i>
        </div>
    </div>
</div>
