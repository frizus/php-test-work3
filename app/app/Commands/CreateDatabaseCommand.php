<?php

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:db-create', description: 'Создать БД из SQL-файла')]
class CreateDatabaseCommand extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('filepath', InputArgument::OPTIONAL, 'Путь до SQL файла от корня сайта', 'dump.sql');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = root_path() . '/' . $input->getArgument('filepath');

        if (!file_exists($filePath)) {
            if ($output->isVerbose()) {
                $output->writeln('<error>Файл "' . $filePath . '" не найден</error>');
            }
            return Command::FAILURE;
        }

        $sql = file_get_contents($filePath);

        if (!$sql) {
            if ($output->isVerbose()) {
                $output->writeln('<error>Пустой SQL-файл "' . $filePath . '"</error>');
            }
            return Command::FAILURE;
        }

        db_connection()->getPdo()->exec($sql);

        if ($output->isVerbose()) {
            $output->writeln('<info>Файл "' . $filePath . '" успешно импортирован</info>');
        }
        return Command::SUCCESS;
    }
}
