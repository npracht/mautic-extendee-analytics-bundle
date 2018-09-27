<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$columnClass = "col-xs-12";
if($widget->getWidth() > 25)
{
    $columnClass.=' col-sm-4';
}else{
    $columnClass.=' pb-5';
}


?>
<div class="analytics-header">
    <form>
        <?php if (isset($user)): ?>
            <input type="hidden" name="userId" value="<?php echo $user->getId(); ?>">
        <?php endif; ?>
        <?php if (isset($widget)): ?>
            <input type="hidden" name="widgetId" value="<?php echo $widget->getId(); ?>">
        <?php endif; ?>
        <div class="col-xs-12 va-m mb-20">
            <div class="row">
                <?php
                foreach ($params['filters'] as $utm) {
                    //foreach ($tags as $utm => $value) {
                    if (!isset($tags[$utm])) {
                        continue;
                    }
                    $value = $tags[$utm];
                    ?>
                    <div class="<?php echo $columnClass; ?>">
                        <label><?php echo $view['translator']->trans(
                                'mautic.email.campaign_'.str_replace(
                                    ['campaign', 'adcontent'],
                                    ['name', 'content'],
                                    $utm
                                )
                            ); ?></label>
                        <select name="<?php echo $utm; ?>" class="form-control" autocomplete="false"
                                multiple="multiple">
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
    </form>
</div>
