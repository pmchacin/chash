<?php

namespace Chash\Command\Installation;

use Chash\Command\Common\CommonCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class WipeCommand.
 */
class WipeCommand extends CommonCommand
{
    protected function configure(): void
    {
        $this
            ->setName('chash:chamilo_wipe')
            ->setDescription('Prepares a portal for a new installation')
            ->addArgument('path', InputArgument::OPTIONAL, 'The path to the Chamilo folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Arguments
        $path = $input->getArgument('path');
        $helper = $this->getHelperSet()->get('question');

        $configurationPath = $this->getConfigurationHelper()->getConfigurationPath($path);
        $configurationFilePath = $this->getConfigurationHelper()->getConfigurationFilePath($path);
        $this->writeCommandHeader($output, 'Wipe command.');
        $output->writeln('<comment>This command will clean your Chamilo installation. Removing the database and courses.</comment>');

        if (false == $configurationPath) {
            $output->writeln('<comment>A Chamilo installation was not detected. You can add a path: </comment><info>chamilo:wipe /var/www/chamilo </info>');

            return 0;
        } else {
            $question = new ConfirmationQuestion(
                '<comment>A Chamilo configuration file was found here:</comment><info> '.$configurationPath.' </info> <question>Are you sure you want to continue?</question>(y/N)',
                false
            );
            if (!$helper->ask($input, $output, $question)) {
                return 0;
            }
        }
        $output->writeln('<comment>This command will clean your installation: drop db, removes config files, cache files.</comment>');
        // Drop database Chash command.
        $command = $this->getApplication()->find('db:drop_databases');

        $arguments = [
            'command' => 'files:drop_databases',
            '--conf' => $configurationFilePath,
        ];

        $inputDrop = new ArrayInput($arguments);
        $command->run($inputDrop, $output);

        // Clean temp Chash command
        $command = $this->getApplication()->find('files:clean_temp_folder');

        $arguments = [
            'command' => 'files:clean_temp_folder',
            '--conf' => $configurationFilePath,
        ];
        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Cleaning courses files
        $command = $this->getApplication()->find('files:clean_courses_files');

        $arguments = [
            'command' => 'files:clean_courses_files',
            '--conf' => $configurationFilePath,
        ];
        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Cleaning config files (last one)

        $command = $this->getApplication()->find('files:clean_config_files');
        $arguments = [
            'command' => 'files:clean_config_files',
            '--conf' => $configurationFilePath,
        ];
        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        return 0;
    }
}
