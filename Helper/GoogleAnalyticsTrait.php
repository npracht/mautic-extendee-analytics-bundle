<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeAnalyticsBundle\Helper;


trait GoogleAnalyticsTrait
{

    /**
     * @return array
     */
    public function getMetricsFromConfig()
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

        if (!empty($this->analyticsFeatures['ecommerce'])) {
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

        if (!empty($this->analyticsFeatures['goals']) && !empty($this->analyticsFeatures['goals']['list'])) {
            foreach ($this->analyticsFeatures['goals']['list'] as $goal) {
                $metrics['goals']['ga:goal'.$goal['value'].'Completions'] = $goal['label'];
            }
        }

        $this->metrics = $metrics;

        return $metrics;
    }


    public function enableEAnalyticsIntegration()
    {
        /** @var EAnalyticsIntegration $analyticsIntegration */
        $analyticsIntegration = $this->integrationHelper->getIntegrationObject('EAnalytics');
        if ($analyticsIntegration && $analyticsIntegration->getIntegrationSettings()->getIsPublished()) {
            $this->analyticsFeatures = $analyticsIntegration->getKeys();
            if (empty($this->analyticsFeatures['clientId']) || empty($this->analyticsFeatures['viewId'])) {
                return false;
            }
            return true;
        }

        return false;
    }


    /**
     * @return array
     */
    public function getFlatUtmTags()
    {
        $flat = [];
        foreach ($this->utmTags as $fields) {
            foreach ($fields as $utmTags) {
                foreach ($utmTags as $key => $tag) {
                    if (empty($tag)) {
                        continue;
                    }
                    if (!isset($flat[$key][$tag])) {
                        $key = str_replace('utmContent', 'utmAdContent', $key);
                        $key = strtolower(str_replace('utm', '', $key));
                        $flat[$key][$tag] = $tag;
                    }
                }
            }
        }

        return $flat;
    }

    public function getFilter()
    {
        $filter = '';
        foreach ($this->getFlatUtmTags() as $key => $utmTag) {
            $filterImp = [];
            foreach ($utmTag as $tag) {
                //$filter.= 'ga:'.strtolower($key).'=='.$utmTag.';';
                $filterImp[] = 'ga:'.$key.'=='.$tag.'';
            }
            $filter .= implode(',', $filterImp).';';
        }
        $filter = substr_replace($filter, '', -1);

        return str_replace('ga:content', 'ga:adContent', $filter);
    }



    /**
     * @return array
     */
    public function getRawMetrics()
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
     * @return mixed
     */
    public function getAnalyticsFeatures()
    {
        return $this->analyticsFeatures;
    }

}