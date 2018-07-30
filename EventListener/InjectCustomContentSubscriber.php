<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeAnalyticsBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomContentEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Helper\GoogleAnalyticsHelper;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Integration\EAnalyticsIntegration;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class InjectCustomContentSubscriber extends CommonSubscriber
{
    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var TemplatingHelper
     */
    protected $templatingHelper;

    /** @var Translator */
    protected $translator;

    /** @var array */
    private $metrics = [];

    /**
     * @var GoogleAnalyticsHelper
     */
    private $analyticsHelper;

    /**
     * ButtonSubscriber constructor.
     *
     * @param IntegrationHelper              $integrationHelper
     * @param TemplatingHelper               $templateHelper
     * @param Translator|TranslatorInterface $translator
     * @param RouterInterface                $router
     * @param GoogleAnalyticsHelper          $analyticsHelper
     */
    public function __construct(
        IntegrationHelper $integrationHelper,
        TemplatingHelper $templateHelper,
        TranslatorInterface $translator,
        RouterInterface $router,
        GoogleAnalyticsHelper $analyticsHelper
    ) {
        $this->integrationHelper = $integrationHelper;
        $this->templateHelper    = $templateHelper;
        $this->translator        = $translator;
        $this->router            = $router;
        $this->analyticsHelper   = $analyticsHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_CONTENT => ['injectViewCustomContent', 0],
        ];
    }

    /**
     * @param CustomContentEvent $customContentEvent
     */
    public function injectViewCustomContent(CustomContentEvent $customContentEvent)
    {
        if (!$this->analyticsHelper->enableEAnalyticsIntegration() || $customContentEvent->getContext(
            ) != 'details.stats.graph.below'
        ) {
            return;
        }
        $keys = $this->analyticsHelper->getAnalyticsFeatures();
        if (!$keys['display_details_graph'] || empty($keys['clientId']) || empty($keys['viewId'])) {
            return;
        }

        $parameters = $customContentEvent->getVars();
        $utmTags    = [];
        $channel    = '';
        foreach ($parameters as $key => $parameter) {
            if (method_exists($parameter, 'getUtmTags')) {
                $entityId = $parameter->getId();
                $utmTags  = $parameter->getUtmTags();
                $channel  = $key;
                break;
            }
        }

        $utmTags = array_filter($utmTags);
        if (empty($utmTags)) {
            return;
        }
        $filters = '';
        $tags    = [];

        $this->analyticsHelper->setUtmTags($utmTags, $channel, $entityId);

        $dateFrom = '';
        $dateTo   = '';
        if (!empty($parameters['dateRangeForm'])) {
            /** @var FormView $dateRangeForm */
            $dateRangeForm = $parameters['dateRangeForm'];
            $dateFrom      = $dateRangeForm->children['date_from']->vars['data'];
            $dateTo        = $dateRangeForm->children['date_to']->vars['data'];
        }

        $content = $this->templateHelper->getTemplating()->render(
            'MauticExtendeeAnalyticsBundle:Analytics:analytics-details.html.php',
            [
                'tags'       => $this->analyticsHelper->getFlatUtmTags(),
                'keys'       => $this->analyticsHelper->getAnalyticsFeatures(),
                'filters'    => $this->analyticsHelper->getFilter(),
                'metrics'    => $this->analyticsHelper->getMetricsFromConfig(),
                'rawMetrics' => $this->analyticsHelper->getRawMetrics(),
                'dateFrom'   => $dateFrom,
                'dateTo'     => $dateTo,
                'multiple'   => false,

            ]
        );

        $customContentEvent->addContent($content);

    }

    private function getRawMetrics()
    {
        $rawMetrics = [];
        foreach ($this->metrics as $metrics) {
            foreach ($metrics as $metric => $label) {
                $rawMetrics[$metric] = $label;
            }
        }

        return $rawMetrics;
    }


    /**
     * @param $keys
     */
    private function getMetricsFromConfig($keys)
    {
        if (!empty($this->metrics)) {
            return $this->metrics;
        }
        $metrics = [
            'overview' => [
                'ga:sessions'           => $this->translator->trans('plugin.extendee.analytics.sessions'),
                'ga:avgSessionDuration' => $this->translator->trans('plugin.extendee.analytics.average.duration'),
                'ga:bounceRate'         => $this->translator->trans('plugin.extendee.analytics.bounce.rate'),
            ],
        ];

        if (!empty($keys['ecommerce'])) {
            $metrics['ecommerce']['ga:transactions']       = $this->translator->trans(
                'plugin.extendee.analytics.transactions'
            );
            $metrics['ecommerce']['ga:transactionRevenue'] = $this->translator->trans(
                'plugin.extendee.analytics.transactions.revenue'
            );

            $metrics['ecommerce']['ga:revenuePerTransaction'] = $this->translator->trans(
                'plugin.extendee.analytics.revenue.per.transaction'
            );
        }
        if (!empty($keys['goals']) && !empty($keys['goals']['list'])) {
            foreach ($keys['goals']['list'] as $goal) {
                $metrics['goals']['ga:goal'.$goal['value'].'Completions'] = $goal['label'];
            }
        }
        $this->metrics = $metrics;

        return $metrics;
    }

}
