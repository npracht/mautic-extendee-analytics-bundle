<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeAnalyticsBundle\EventListener;

use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber as MainDashboardSubscriber;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Helper\GoogleAnalyticsHelper;

/**
 * Class DashboardSubscriber.
 */
class DashboardSubscriber extends MainDashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'extendee';

    /**
     * Define the widget(s).
     *
     * @var string
     */
    protected $types = [
        'extendee.analytics' => [
            'formAlias' => 'dashboard_extendee_analytics',
        ],
    ];

    /**
     * @var GoogleAnalyticsHelper
     */
    private $analyticsHelper;


    /**
     * DashboardSubscriber constructor.
     *
     * @param GoogleAnalyticsHelper $analyticsHelper
     */
    public function __construct(GoogleAnalyticsHelper $analyticsHelper)
    {

        $this->analyticsHelper = $analyticsHelper;
    }

    /**
     * Set a widget detail when needed.
     *
     * @param WidgetDetailEvent $event
     */
    public function onWidgetDetailGenerate(WidgetDetailEvent $event)
    {
        $this->checkPermissions($event);

        if ($event->getType() == 'extendee.analytics' && $this->analyticsHelper->enableEAnalyticsIntegration()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();
            //if (!$event->isCached()) {
                $this->analyticsHelper->setUtmTagsFromChannels((new DateTimeHelper($params['dateFrom']))->toLocalString('Y-m-d'), (new DateTimeHelper($params['dateTo']))->toLocalString('Y-m-d'));
            $this->analyticsHelper->setDynamicFilter($params);

            $event->setTemplateData([
                    'params' => $params,
                    'tags'   =>     $this->analyticsHelper->getFlatUtmTags($params),
                    'keys'       => $this->analyticsHelper->getAnalyticsFeatures(),
                    'filters'    => $this->analyticsHelper->getFilter(),
                    'metrics'    => $this->analyticsHelper->getMetricsFromConfig(),
                    'rawMetrics' => $this->analyticsHelper->getRawMetrics(),
                    'dateFrom' =>  (new DateTimeHelper($params['dateFrom']))->toLocalString('Y-m-d'),
                    'dateTo' =>  (new DateTimeHelper($params['dateTo']))->toLocalString('Y-m-d'),
                    'widget' => $widget,
                    'user' => $this->analyticsHelper->getUserHelper()->getUser(true),
                ]);
            //}

            $event->setTemplate('MauticExtendeeAnalyticsBundle:Analytics:analytics-dashboard.html.php');
            $event->stopPropagation();
        }
    }
}
