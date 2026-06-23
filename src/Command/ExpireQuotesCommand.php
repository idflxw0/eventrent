<?php

namespace App\Command;

use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:quotes:expire',
    description: 'Expire pending quotes older than 15 days.',
)]
class ExpireQuotesCommand extends Command
{
    public function __construct(
        private readonly QuoteRepository $quoteRepository,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'List quotes without modifying them');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        $quotes = $this->quoteRepository->findExpired();

        if (empty($quotes)) {
            $io->success('No expired quotes found.');
            return Command::SUCCESS;
        }

        foreach ($quotes as $quote) {
            $io->writeln(sprintf(
                '  [%d] %s — created %s',
                $quote->getId(),
                $quote->getEventCity() ?: 'N/A',
                $quote->getCreatedAt()->format('d/m/Y')
            ));

            if (!$dryRun) {
                $quote->setStatus('expired');
            }
        }

        if ($dryRun) {
            $io->note(sprintf('%d quote(s) would be expired (dry-run, no changes saved).', count($quotes)));
        } else {
            $this->em->flush();
            $io->success(sprintf('%d quote(s) expired.', count($quotes)));
        }

        return Command::SUCCESS;
    }
}
