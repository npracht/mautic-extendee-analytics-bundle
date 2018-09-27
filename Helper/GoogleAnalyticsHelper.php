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


use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Integration\EAnalyticsIntegration;
use MauticPlugin\MauticRecombeeBundle\Integration\RecombeeIntegration;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class GoogleAnalyticsHelper
{
    use GoogleAnalyticsTrait;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $metrics;

    private $analyticsFeatures;

    /**
     * @var EntityManager
     */
    private $entityManager;

    private $utmTags = [];


    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserHelper
     */
    private $userHelper;


    /**
     * Generator constructor.
     *
     * @param IntegrationHelper   $integrationHelper
     * @param TranslatorInterface $translator
     * @param EntityManager       $entityManager
     * @param RouterInterface     $router
     *
     * @param UserHelper          $userHelper
     *
     * @internal param FormFactoryBuilder $formFactoryBuilder
     */
    public function __construct(
        IntegrationHelper $integrationHelper,
        TranslatorInterface $translator,
        EntityManager $entityManager,
        RouterInterface $router,
        UserHelper $userHelper
    ) {

        $this->integrationHelper = $integrationHelper;
        $this->translator        = $translator;
        $this->entityManager     = $entityManager;
        $this->router            = $router;
        $this->userHelper        = $userHelper;
    }

    /**
     * @param null $dateFrom
     * @param null $dateTo
     *
     * @return array
     */
    public function setUtmTagsFromChannels($dateFrom = null, $dateTo = null)
    {
        // already exists
        if (!empty($this->utmTags)) {
            return $this->utmTags;
        }

        $q = $this->entityManager->getConnection()->createQueryBuilder();

        $tables = ['emails', 'focus', 'push_notifications'];

        foreach ($tables as $table) {
            $q->select('e.id, e.utm_tags')
                ->from(MAUTIC_TABLE_PREFIX.$table, 'e');
            if ($dateFrom && $dateTo) {
                $q->where(
                    $q->expr()->gt('e.date_modified', ':dateFrom'),
                    $q->expr()->lt('e.date_modified', ':dateTo')
                )
                    ->setParameter('dateFrom', $dateFrom)
                    ->setParameter('dateTo', $dateTo);
            }
            $utmTags = $q->execute()->fetchAll();
            if ($utmTags) {
                foreach ($utmTags as $utmTag) {
                    $utm = $this->transformUtmTagsFromDBToArray($utmTag['utm_tags']);
                    if (!empty($utm)) {
                        $this->utmTags[$table][$utmTag['id']] = $utm;
                    }
                }
            }
        }

        return $this->utmTags;
    }

    /**
     * @param array $utmTags
     */
    public function setUtmTags(array $utmTags, $channel, $channelId)
    {
        $this->utmTags[$channel][$channelId] = $utmTags;
    }

    /**
     * @return UserHelper
     */
    public function getUserHelper()
    {
        return $this->userHelper;
    }

}