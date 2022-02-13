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
    }
}