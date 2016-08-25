<?php

namespace HotfixBundle\Command;

use Oro\Bundle\InstallerBundle\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helps installing all frontend assets during building the docker image.
 */
class AssetsInstallCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('hotfix:assets:install')
            ->setDescription('Install frontent assets.')
            ->addOption(
                'symlink',
                null,
                InputOption::VALUE_NONE,
                'Symlinks the assets instead of copying it'
            )
            ->addOption(
                'skip-translations',
                null,
                InputOption::VALUE_NONE,
                'Skip translations when a database connection is not available'
            )
        ;

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assetsOptions = [
            '--exclude' => ['OroInstallerBundle']
        ];

        $processOptions = [
            '--process-isolation' => true
        ];

        if ($input->hasOption('symlink') && $input->getOption('symlink')) {
            $assetsOptions['--symlink'] = true;
        }

        $commandExecutor = $this->getCommandExecutor($input, $output);

        $commandExecutor
            ->runCommand('oro:assets:install', $assetsOptions)
            ->runCommand('assetic:dump')
            ->runCommand('fos:js-routing:dump', $processOptions)
            ->runCommand('oro:localization:dump', $processOptions)
            ;

        // Translations require database connection
        if (!$input->hasOption('skip-translations')) {
            $commandExecutor->runCommand('oro:translation:dump', $processOptions);
        }

        $commandExecutor
            ->runCommand(
                'oro:requirejs:build',
                array_merge($processOptions, ['--ignore-errors' => true])
            );
    }
}
