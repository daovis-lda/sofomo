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
    
    //another example of test
    public function testCleanupLogs(): void
    {
        // test log file with some old and some new records
        $logFile = 'test.log';
        file_put_contents($logFile, "2022-01-01 00:00:00 This is a new record\n");
        file_put_contents($logFile, "2021-01-01 00:00:00 This is an old record\n", FILE_APPEND);
        file_put_contents($logFile, "2022-01-01 00:00:00 This is a new record\n", FILE_APPEND);

        // Test removing records older than 1 day
        $logAnalyzerRequest = new LogAnalyzerRequest($logFile, 1);
        $logService = new LogAnalyzerService();
        $result = $logService->execute($logAnalyzerRequest);

        $lines = file($logFile);
        $this->assertEquals(count($lines), 2);
        $this->assertEquals($lines[0], "2022-01-01 00:00:00 This is a new record\n");
        $this->assertEquals($lines[1], "2022-01-01 00:00:00 This is a new record\n");

        // Test removing records older than 365 days
        $logAnalyzerRequest = new LogAnalyzerRequest($logFile, 365);
        $lines = file($logFile);
        $this->assertEquals(count($lines), 0);
    }
}
