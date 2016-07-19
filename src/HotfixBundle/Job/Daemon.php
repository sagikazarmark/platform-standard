<?php

namespace HotfixBundle\Job;

/**
 * This hotfix allows to see the daemon status with the overriden job queue command.
 */
class Daemon extends \Oro\Bundle\CronBundle\Job\Daemon
{
    /**
     * {@inheritdoc}
     */
    public function getPid()
    {
        if (!$this->pid) {
            $this->pid = $this->findProcessPid(
                sprintf('%sconsole hotfix:jms-job-queue:run', $this->rootDir . DIRECTORY_SEPARATOR)
            );
        }

        return $this->pid;
    }
}
