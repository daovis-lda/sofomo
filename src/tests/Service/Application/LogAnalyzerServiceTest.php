<?php

namespace App\tests\Service\Application;

use App\Service\Application\LogAnalyzerRequest;
use App\Service\Application\LogAnalyzerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogAnalyzerServiceTest extends KernelTestCase
{
    public function testLogsAnalysis(): void
    {
        $tmpFileName = '../../../data/test-1.logs';
        $logAnalyzerRequest = new LogAnalyzerRequest($tmpFileName, 'now');
        $logService = new LogAnalyzerService();
        $result = $logService->execute($logAnalyzerRequest);

        $this->assertTrue($result);
    }
}