<?php

namespace HotfixBundle\EventListener;

use Akeneo\Bundle\BatchBundle\Event\StepExecutionEvent;
use Oro\Bundle\ImportExportBundle\Job\JobExecutor;
use Oro\Bundle\ImportExportBundle\Processor\ProcessorRegistry;

/**
 * Apply custom configurations to execution contexts.
 *
 * @see https://www.orocrm.com/forums/topic/import-csvfilereader
 */
class ImportExportConfigListener
{
    /**
     * @var array
     */
    private $processorMap = [
        JobExecutor::JOB_EXPORT_TEMPLATE_TO_CSV => ProcessorRegistry::TYPE_EXPORT_TEMPLATE,
        JobExecutor::JOB_EXPORT_TO_CSV => ProcessorRegistry::TYPE_EXPORT,
        JobExecutor::JOB_IMPORT_FROM_CSV =>ProcessorRegistry::TYPE_IMPORT,
        JobExecutor::JOB_VALIDATE_IMPORT_FROM_CSV => ProcessorRegistry::TYPE_IMPORT_VALIDATION,
    ];

    /**
     * @var array
     */
    private $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param StepExecutionEvent $event
     */
    public function onStepExecution(StepExecutionEvent $event)
    {
        $jobInstance = $event
            ->getStepExecution()
            ->getJobExecution()
            ->getJobInstance()
        ;

        // Check if the job is an import/export one
        if (false == array_key_exists($jobInstance->getAlias(), $this->processorMap)) {
            return;
        }

        $processorType = $this->processorMap[$jobInstance->getAlias()];

        $rawConfiguration = $jobInstance->getRawConfiguration();

        // Check if there is custom configuration for the current processor alias and merge it
        if (array_key_exists($rawConfiguration[$processorType]['processorAlias'], $this->config)) {
            $rawConfiguration[$processorType] = array_merge(
                $rawConfiguration[$processorType],
                $this->config[$rawConfiguration[$processorType]['processorAlias']]
            );

            $jobInstance->setRawConfiguration($rawConfiguration);
        }
    }
}
