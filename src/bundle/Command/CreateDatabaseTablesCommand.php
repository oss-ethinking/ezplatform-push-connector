<?php

namespace Ethinking\PushConnectorBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseTablesCommand extends Command
{
    protected static $defaultName = 'ibexa:push-connector:create-tables';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Creates database tables')
            ->setHelp('This command creates database tables for the push connector');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->em->getConnection();

        try {
            $this->createMainSettingsTable($connection);
        } catch (Exception $e) {
            return Command::FAILURE;
        } finally {
            $connection->close();
        }

        return Command::SUCCESS;
    }

    /**
     * @param Connection $connection
     * @throws Exception
     */
    private function createMainSettingsTable(Connection $connection)
    {
        $doesTableExist = $connection->fetchOne("SHOW TABLES LIKE 'push_delivery_main_settings'") !== false;

        if ($doesTableExist) {
            return;
        }

        $connection->executeStatement($this->getCreateMainSettingsTableSQL());
    }

    /**
     * @return string
     */
    private function getCreateMainSettingsTableSQL()
    {
        return <<<EOD
DROP TABLE IF EXISTS `push_delivery_main_settings`;

CREATE TABLE `push_delivery_main_settings` (
  `settings_id` int(11) NOT NULL DEFAULT 1,
  `client_id` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `domain` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
EOD;
    }
}