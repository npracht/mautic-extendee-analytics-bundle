<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeAnalyticsBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Helper\GoogleAnalyticsHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DashboardExtendeeAnalyticsWidgetType.
 */
class DashboardExtendeeAnalyticsWidgetType extends AbstractType
{
    /**
     * @var GoogleAnalyticsHelper
     */
    private $analyticsHelper;

    /**
     * DashboardExtendeeAnalyticsWidgetType constructor.
     *
     * @param GoogleAnalyticsHelper $analyticsHelper
     */
    public function __construct(GoogleAnalyticsHelper $analyticsHelper)
    {

        $this->analyticsHelper = $analyticsHelper;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'dynamic_filter',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.plugin.recombee.form.dynamic.filter',
            ]
        );

        $builder->add(
            'filters',
            ChoiceType::class,
            [
                'choices' => [
                    'source'    => 'mautic.email.campaign_source',
                    'medium'    => 'mautic.email.campaign_medium',
                    'campaign'  => 'mautic.email.campaign_name',
                    'adcontent' => 'mautic.email.campaign_content',
                ],
                'expanded'    => false,
                'multiple'    => true,
                'label'       => 'mautic.plugin.recombee.form.filters',
                'label_attr'  => ['class' => ''],
                'empty_value' => false,
                'required'    => true,
                'attr'=>[
                    'data-show-on' => '{"widget_params_dynamic_filter_1":"checked"}',
                ]
            ]
        );

        $this->analyticsHelper->setUtmTagsFromChannels();
        $utmTags = $this->analyticsHelper->getFlatUtmTags();
        foreach ($utmTags as $key=>$utmTag) {
            $builder->add(
                $key,
                ChoiceType::class,
                [
                    'choices' => $utmTag,
                    'expanded'    => false,
                    'multiple'    => true,
                    'label' => $this->analyticsHelper->tagToLabel($key),
                    'label_attr'  => ['class' => ''],
                    'empty_value' => false,
                    'required'    => false,
                    'attr'=>[
                        'data-show-on' => '{"widget_params_dynamic_filter_0":"checked"}',
                    ]
                ]
            );
        }
        $builder->add(
            'sessionGraph',
            YesNoButtonGroupType::class,
            [
                'label' => 'plugin.extendee.analytics.graph.session.enable',
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dashboard_extendee_analytics';
    }
}
