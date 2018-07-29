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
<div class="row">
    <?php foreach ($tags as $utm => $value) { ?>
        <div class="col-xs-12 col-sm-3">

            <span class="label" style="background-color:#787a7a"><?php echo $utm; ?></span>
            &nbsp;<small><?php echo $value; ?></small>
        </div>
        <?php
    }
    ?>
    <i id="analytics-loading" class="fa fa-spin fa-spinner"></i>
</div>
