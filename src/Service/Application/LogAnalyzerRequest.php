<?php

namespace App\Service\Application;

use DateTime;

class LogAnalyzerRequest
{
    private string $logsDir;
    private DateTime $outdatedTime;

    public function __construct(string $logsDir, string $outdatedTime)
    {
        $this->logsDir = $logsDir;
        $this->outdatedTime = new DateTime($outdatedTime);
    }

    public function getLogsDir(): string
    {
        return $this->logsDir;
    }

    public function getOutdatedTime(): DateTime
    {
        return $this->outdatedTime;
    }
}