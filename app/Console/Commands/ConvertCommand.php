<?php

namespace App\Console\Commands;

use Exception;
use App\Parser;
use App\Exporter;
use InvalidArgumentException;
use Smalot\PdfParser\Parser as PdfReader;
use Illuminate\Console\Command;

class ConvertCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'convert
                            {file* : The input file(s)}
                            {--f|format=csv : The output file format (one of "csv", "xls", "xlsx" or "ods")}
                            {--o|output= : The output file name}
                            {--autosize : Resize columns to fit content}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Convert a tabular format.

  Converted data will be displayed on the screen if an output file is not specified.';

    /**
     * Header of output data.
     *
     * @var string[]
     */
    protected $header = [
        'Trade Date',
        'Settle Date',
        'Currency',
        'Activity Type',
        'Symbol / Description',
        'Symbol',
        'Description',
        'Quantity',
        'Price',
        'Amount'
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = $this->argument('file');
        foreach ($files as $file) {
            if (!is_readable($file)) {
                return $this->error(sprintf('Input file %s does not exist.', $file));
            }
        }

        try {
            $reader = new PdfReader();
            $exporter = new Exporter($this->destination(), $this->format());
            $exporter->setAutoSize($this->option('autosize'));

            $exporter->insertOne($this->header);
            foreach ($files as $file) {
                $text = $reader->parseFile($file)->getText();
                $this->line(sprintf("Extracted text\n%s", $text), null, 'vv');

                $records = Parser::parse($text);
                $this->info(sprintf('%d matches found in %s', count($records), $file), 'v');

                $exporter->insertMany($records);
            }

            // Send file to stdout if destination not given
            if (empty($this->option('output'))) {
                $exporter->print();
            }
        } catch (Exception $e) {
            return $this->error(sprintf('Conversion failed: %s', $e->getMessage()));
        }
    }

    /**
     * Return sanitized filename. It enforces that the extension
     * matches the format wanted by the user. This way, for instance,
     * we avoid generating a XLSX file with a ".xls" extension that
     * Excel may refuse to open.
     *
     * @return string|null
     */
    protected function destination(): ?string
    {
        $destination = $this->option('output');
        if (empty($destination)) {
            return null;
        }

        return preg_replace('/(?:csv|xlsx|xls|ods)$/', $this->format(), $destination);
    }

    /**
     * @return string
     */
    protected function format(): string
    {
        $format = strtolower($this->option('format'));

        $supportedFormats = Exporter::getSupportedFormats();
        if (!in_array($format, $supportedFormats)) {
            throw new InvalidArgumentException(sprintf(
                'Format must be one of: %s',
                implode(', ', $supportedFormats)
            ));
        }

        return $format;
    }
}
