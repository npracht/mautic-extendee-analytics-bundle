<?php

return [
    'name'        => 'MauticExtendeeAnalyticsBundle',
    'description' => 'Google Analytics integration for Mautic',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',
    'routes'      => [
        'public' => [
            
        ],
    ],
    'services'    => [
        'events'       => [
            'mautic.plugin.extendee.analytics.inject.custom.content.subscriber' => [
                'class'     => \MauticPlugin\MauticExtendeeAnalyticsBundle\EventListener\InjectCustomContentSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.helper.templating',
                    'translator',
                    'router',
                ],
            ],
            'mautic.plugin.extendee.analytics.dashboard.subscriber' => [
                'class'     => \MauticPlugin\MauticExtendeeAnalyticsBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'mautic.plugin.extendee.analytics.helper'
                ],
            ],
        ],
        'others'=>[
            'mautic.plugin.extendee.analytics.helper'=> [
                'class' => MauticPlugin\MauticExtendeeAnalyticsBundle\Helper\GoogleAnalyticsHelper::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'translator',
                    'doctrine.orm.entity_manager',
                    'router',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.EAnalytics' => [
                'class' => \MauticPlugin\MauticExtendeeAnalyticsBundle\Integration\EAnalyticsIntegration::class,
            ],
        ],
    ],
];
