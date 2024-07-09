<?php

namespace App\Commands;

use App\Database\DatabaseManager;
use App\Database\IPDOAble;
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
            $output->writeln('<error>Файл "' . $filePath . '" не найден</error>');
            return Command::FAILURE;
        }

        $sql = file_get_contents($filePath);

        if (!$sql) {
            $output->writeln('<error>Пустой SQL-файл "' . $filePath . '"</error>');
            return Command::FAILURE;
        }

        /** @var IPDOAble $pdo */
        $pdo = DatabaseManager::getInstance()->connection()->getPdo();
        $pdo->exec($sql);

        $output->writeln("<info>Файл {$filePath} успешно импортирован</info>");
        return Command::SUCCESS;
    }
}