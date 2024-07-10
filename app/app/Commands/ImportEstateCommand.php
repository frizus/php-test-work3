<?php

namespace App\Commands;

use App\Importers\EstateImporter;
use App\Importers\ImporterBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-estate', description: 'Импортировать квартиры из файла estate')]
class ImportEstateCommand extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('filepath', InputArgument::OPTIONAL, 'Путь до файла от корня сайта (например: estate.xlsx, estate_update.xlsx', 'estate.xlsx')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Указать конкретно формат файла (xlsx, xls). Если не указывать определяется по расширению');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $importer = new EstateImporter(
            (new ImporterBuilder($input->getOption('format')))
                ->setFilePath(root_path() . '/' . $input->getArgument('filepath'))
                ->build()
        );
        $importer->run();

        foreach ($importer->getImportStats() as $name => $value) {
            $output->writeln($name . ': ' . $value);
        }

        return Command::SUCCESS;
    }
}