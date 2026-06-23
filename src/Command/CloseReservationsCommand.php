<?php

namespace App\Command;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reservations:close',
    description: 'Close confirmed reservations whose end date has passed.',
)]
class CloseReservationsCommand extends Command
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'List reservations without modifying them');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        $reservations = $this->reservationRepository->findConfirmedPastEndDate();

        if (empty($reservations)) {
            $io->success('No reservations to close.');
            return Command::SUCCESS;
        }

        foreach ($reservations as $reservation) {
            $io->writeln(sprintf(
                '  [%d] %s — ended %s',
                $reservation->getId(),
                $reservation->getEventCity(),
                $reservation->getEndDate()->format('d/m/Y')
            ));

            if (!$dryRun) {
                $reservation->setStatus('completed');
            }
        }

        if ($dryRun) {
            $io->note(sprintf('%d reservation(s) would be closed (dry-run, no changes saved).', count($reservations)));
        } else {
            $this->em->flush();
            $io->success(sprintf('%d reservation(s) closed.', count($reservations)));
        }

        return Command::SUCCESS;
    }
}
