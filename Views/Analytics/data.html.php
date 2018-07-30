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

<div class="analytics-data">
    <div class="clearfix row pt-0 pl-15 pb-15 pr-15 analytics-auth" style="display: none">
        <div class="col-sm-4 col-xs-6">
            <section id="auth-button"></section>
        </div>
    </div>

    <div class="clearfix eanalytics-stats-no-results" style="display: none">
        <div class="col-xs-12">
            <div class="alert alert-info">
                <?php echo $view['translator']->trans('plugin.extendee.analytics.no.results'); ?>
            </div>
        </div>
    </div>
    <div class="eanalytics-stats clearfix pb-15" style="display: none">
        <?php
        if (!empty($metrics['overview'])) {
            foreach ($metrics['overview'] as $metric => $label) {
                ?>
                <div class="col-sm-4 col-xs-6">
                    <h3 class="pull-left"><span class="label label-primary <?php echo str_replace('ga:', '', $metric); ?>"
                                                ></span></h3>
                    <span class="mt-5 pt-2 ml-10 pull-left"><?php echo $label; ?></span>
                </div>
                <?php
            }
        }
        ?>


        <?php
        if (!empty($metrics['ecommerce'])) {
            ?>
            <div class="panel-body box-layout pt-20">
                <div class="col-xs-12 va-m">
                    <h5 class="text-white dark-md fw-sb mb-xs">
                        <span class="fa fa-line-chart"></span>
                        <?php echo $view['translator']->trans('plugin.extendee.analytics.ecommerce'); ?>
                    </h5>
                </div>
            </div>
            <?php
            foreach ($metrics['ecommerce'] as $metric => $label) {
                ?>
                <div class="col-sm-4 col-xs-6">
                    <h3 class="pull-left"><span class="label label-success <?php echo str_replace('ga:', '', $metric); ?>"
                                                ></span></h3>
                    <span class="mt-5 pt-2 ml-10 pull-left"><?php echo $label; ?></span>
                </div>
                <?php
            }
        }
        ?>

        <?php
        if (!empty($metrics['goals'])) {
            ?>
            <div class="panel-body box-layout pt-20">
                <div class="col-xs-4 va-m">
                    <h5 class="text-white dark-md fw-sb mb-xs">
                        <span class="fa fa-line-chart"></span>
                        <?php echo $view['translator']->trans('plugin.extendee.analytics.goals'); ?>
                    </h5>
                </div>
                <div class="col-xs-8 va-m">
                </div>
            </div>
            <?php
            foreach ($metrics['goals'] as $metric => $label) {
                ?>
                <div class="col-sm-4 col-xs-6">
                    <h3 class="pull-left"><span class="label label-info <?php echo str_replace('ga:', '', $metric); ?>"
                                                ></span></h3>
                    <span class="mt-5 pt-2 ml-10 pull-left"><?php echo $label; ?></span>
                </div>
                <?php
            }
        }
        ?>
        <div class="panel-body box-layout pt-20 pb-0">
            <div class="col-xs-4 va-m">
                <h5 class="text-white dark-md fw-sb mb-xs">
                    <span class="fa fa-line-chart"></span>
                    <?php echo $view['translator']->trans('plugin.extendee.analytics.graph.by.days'); ?>
                </h5>
            </div>
            <div class="col-xs-8 va-m">
            </div>
        </div>
        <div class="col-xs-12">
            <div class="chart-container" id="chart-container-<?php echo uniqid(mt_rand());?>"></div>
        </div>
    </div>
</div>
