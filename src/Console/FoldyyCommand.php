<?php

/**
 * FoldyyCommand
 *
 * @package Foldyy
 */

namespace Nilambar\Foldyy\Console;

use Nilambar\Foldyy\FoldyyService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Foldyy Console Command Class.
 *
 * Symfony Console command for displaying folder tree with sizes.
 *
 * @since 1.0.0
 */
class FoldyyCommand extends Command
{
	/**
	 * Configure the command.
	 *
	 * @since 1.0.0
	 */
	protected function configure()
	{
		$this->setName('foldyy')->setDescription('Display folder tree structure with file and folder sizes.')->setHelp(
			'This command displays a tree view of a folder with size information for each file and folder.' . "\n\n" .
				'Examples:' . "\n" .
				'  foldyy /path/to/folder' . "\n" .
				'  foldyy /path/to/folder --max-depth 5' . "\n" .
				'  foldyy /path/to/folder --no-sizes' . "\n" .
				'  foldyy /path/to/folder --output tree.html' . "\n" .
				'  foldyy /path/to/folder --output-dir /tmp' . "\n" .
				'  foldyy /path/to/folder --output-dir /tmp --porcelain'
		)->addArgument(
			'folder',
			InputArgument::REQUIRED,
			'Path to the folder to display.'
		)->addOption(
			'max-depth',
			'd',
			InputOption::VALUE_REQUIRED,
			'Maximum depth to scan (default: 10).',
			10
		)->addOption(
			'no-sizes',
			null,
			InputOption::VALUE_NONE,
			'Do not display file and folder sizes.'
		)->addOption(
			'output',
			'o',
			InputOption::VALUE_REQUIRED,
			'Output file path.'
		)->addOption(
			'output-dir',
			null,
			InputOption::VALUE_OPTIONAL,
			'Output directory for the HTML diff file. Defaults to system temp directory.',
			null
		)->addOption(
			'porcelain',
			null,
			InputOption::VALUE_NONE,
			'Output only the file path, suitable for parsing.'
		);
	}

	/**
	 * Execute the command.
	 *
	 * @since 1.0.0
	 *
	 * @param InputInterface  $input Input interface.
	 * @param OutputInterface $output Output interface.
	 * @return int Exit code.
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$folder_path = $input->getArgument('folder');
		$max_depth   = (int) $input->getOption('max-depth');
		$no_sizes    = $input->getOption('no-sizes');
		$output_file = $input->getOption('output');
		$output_dir  = $input->getOption('output-dir');
		$porcelain   = $input->getOption('porcelain');

		$service = new FoldyyService();

		try {
			$html_output = $service->generateHtmlTree($folder_path, $max_depth, ! $no_sizes);

			// Determine output file path.
			if (! $output_file) {
				// Use output-dir (defaults to temp directory) or temp directory.
				$target_dir = $output_dir ?: sys_get_temp_dir();
				if (! is_dir($target_dir)) {
					throw new RuntimeException('Output directory does not exist: ' . $target_dir);
				}
				// Generate unique filename.
				$basename = 'foldyy_' . basename($folder_path) . '_' . uniqid() . '.html';
				$output_file = rtrim($target_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename;
			}

			file_put_contents($output_file, $html_output);

			if ($porcelain) {
				$output->writeln($output_file);
			} else {
				$output->writeln('<info>HTML file generated: ' . $output_file . '</info>');
			}

			return Command::SUCCESS;
		} catch (RuntimeException $e) {
			if ($porcelain) {
				$output->writeln($e->getMessage());
			} else {
				$output->writeln('<error>' . $e->getMessage() . '</error>');
			}
			return Command::FAILURE;
		}
	}
}
