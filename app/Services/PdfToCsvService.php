<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfToCsvService
{
    protected $storage;
    protected $dataToConvert = [];

    public function __construct()
    {
        $this->storage = Storage::disk('local_s3'); // Ou o disco de armazenamento desejado
    }

    public function convertPdfTextToCsv($filename, $data, $mode = 'w+')
    {
        try {
            if (empty($data)) {
                return false;
            }

            $header = array_keys($data[0]);

            $CSV = $this->storage->append($filename, implode(';', $header) . PHP_EOL);

            foreach ($data as $line) {
                $this->storage->append($filename, implode(';', $line) . PHP_EOL);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error while converting PDF to CSV: ' . $e->getMessage());
            return false;
        }
    }

    public function accumulateDataForConversion(array $data)
    {
        $this->dataToConvert = array_merge($this->dataToConvert, $data);
    }

    public function convertAccumulatedDataToCsv(string $csvFilePath)
    {
        if (empty($this->dataToConvert)) {
            return;
        }

        $header = array_keys($this->dataToConvert[0]);

        $CSV = fopen($csvFilePath, 'w+');
        fputcsv($CSV, $header, ';');

        foreach ($this->dataToConvert as $line) {
            fputcsv($CSV, $line, ';');
        }

        fclose($CSV);

        // Limpar os dados acumulados após a conversão
        $this->dataToConvert = [];
    }

    public static function receiveDataGenerateCSV($filename, $data)
    {
        if(!$data) return true;

            $header = array_keys($data[0]);

            $CSV = fopen($filename, 'a+');
            fputcsv($CSV, $header, ';');

            foreach ($data as $line) {
                fputcsv($CSV, $line, ';');
            }

            fclose($CSV);

        return true;
    }
}

