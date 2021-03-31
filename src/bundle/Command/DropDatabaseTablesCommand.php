<?php

namespace Ethinking\PushConnectorBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropDatabaseTablesCommand extends Command
{
    protected static $defaultName = 'ibexa:push-connector:drop-tables';

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
        $this->setDescription('Drops database tables')
            ->setHelp('This command removes database tables for the push connector');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->em->getConnection();

        try {
            $connection->executeStatement('DROP TABLE IF EXISTS `push_delivery_main_settings`');
        } catch (Exception $e) {
            return Command::FAILURE;
        } finally {
            $connection->close();
        }

        return Command::SUCCESS;
    }
}