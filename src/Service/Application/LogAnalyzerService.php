<?php

namespace App\Service\Application;

use DateTime;
use Exception;
use SplFileObject;

class LogAnalyzerService
{
    private string $tmpFileName = 'data/tmp_file.csv';

    /**
     * @throws Exception
     */
    public function execute(object $request): bool
    {
        assert($request instanceof LogAnalyzerRequest);
        $result = false;

        $file = new SplFileObject($request->getLogsDir());
        $tmpFile = new SplFileObject($this->tmpFileName);

        $rawArray = explode(',', $file->fgets());
        $datePos = array_search('date', $rawArray);
        $tmpFile->fputcsv($file->fgetcsv()); // putting headers

        while(!$file->eof())
        {
            $rawArray = explode(',', $file->fgets());
            $date = $rawArray[$datePos];
            assert(is_string($date));

            $rawArray = new DateTime($date);
            if ($rawArray >= $request->getOutdatedTime()) {
                $tmpFile->fputcsv($file->fgetcsv());
                $result = true;
            }
        }

        rename($this->tmpFileName, $request->getLogsDir());

        $file->rewind();
        $tmpFile->rewind();

//        throw LogFileException; we throw custom exception if smth will go wrong and will handle it in controller
        return $result;
        
        
        // other option 
        $result = (bool) $this->cleanupLogs($this->tmpFileName, $request->getOutdatedTime()->getTimestamp());
        
        return $result;
    }
    
    private function cleanupLogs(string $logFile, int $maxAge): int
    {
        $lines = file($logFile);
        $newLines = [];
        $removed = 0;

        foreach ($lines as $line) {
        if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\s+(.*)$/', $line, $matches)) {
          $timestamp = $matches[1];
          $message = $matches[2];

          $age = (time() - strtotime($timestamp)) / 86400; // 86400 seconds in a day
          if ($age <= $maxAge) {
            $newLines[] = $line;
          } else {
            $removed++;
          }
        }
        }

        file_put_contents($logFile, implode("\n", $newLines));

        return $removed;
    }
}
