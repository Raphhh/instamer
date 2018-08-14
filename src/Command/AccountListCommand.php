<?php

namespace App\Command;

use App\Repository\AccountRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AccountListCommand extends Command
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        parent::__construct();

        $this->accountRepository = $accountRepository;
    }

    protected function configure()
    {
        $this
            ->setName('account:list')
            ->setDescription('List existing account connections');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->accountRepository->generateAll() as $account) {
            $output->writeln(sprintf(
                '[%s] %s (%s)',
                $account->getId(),
                $account->getUsername(),
                $account->getAccountId()
            ));
       }
    }
}
