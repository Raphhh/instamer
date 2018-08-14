<?php

namespace App\Command;

use App\Service\AccountCreator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AccountCreateCommand extends Command
{
    /**
     * @var AccountCreator
     */
    private $accountCreator;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(AccountCreator $accountCreator, ObjectManager $objectManager)
    {
        parent::__construct();
        $this->accountCreator = $accountCreator;
        $this->objectManager = $objectManager;
    }

    protected function configure()
    {
        $this
            ->setName('account:create')
            ->setDescription('Create account connection')
            ->addArgument(
                'username',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = $this->accountCreator->createAccount(
            $input->getArgument('username'),
            $input->getArgument('password')
        );

        $this->objectManager->persist($account);
        $this->objectManager->flush();

        $output->writeln(sprintf('<info>account created with id #%s</info>', $account->getId()));
    }
}
