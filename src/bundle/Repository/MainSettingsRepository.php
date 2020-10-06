<?php

namespace Ethinking\PushConnectorBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ethinking\PushConnectorBundle\Entity\MainSettings;

class MainSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainSettings::class);
    }
}