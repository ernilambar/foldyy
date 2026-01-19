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
				'  foldyy /path/to/folder --html' . "\n" .
				'  foldyy /path/to/folder --html --output tree.html'
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
			'html',
			null,
			InputOption::VALUE_NONE,
			'Generate HTML output with collapsible folders.'
		)->addOption(
			'output',
			'o',
			InputOption::VALUE_REQUIRED,
			'Output file path (for HTML mode). If not specified, outputs to stdout.'
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
		$html_mode   = $input->getOption('html');
		$output_file = $input->getOption('output');

		$service = new FoldyyService();

		try {
			if ($html_mode) {
				$html_output = $service->generateHtmlTree($folder_path, $max_depth, ! $no_sizes);

				if ($output_file) {
					file_put_contents($output_file, $html_output);
					$output->writeln('<info>HTML file generated: ' . $output_file . '</info>');
				} else {
					$output->write($html_output);
				}
			} else {
				$tree_output = $service->generateTree($folder_path, $max_depth, ! $no_sizes);

				if ($output_file) {
					file_put_contents($output_file, $tree_output);
					$output->writeln('<info>Tree file generated: ' . $output_file . '</info>');
				} else {
					$output->writeln($tree_output);
				}
			}

			return Command::SUCCESS;
		} catch (RuntimeException $e) {
			$output->writeln('<error>' . $e->getMessage() . '</error>');
			return Command::FAILURE;
		}
	}
}
