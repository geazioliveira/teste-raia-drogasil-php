<?php

namespace App\Repositories;

use Illuminate\Support\Facades\File;


class FileRepository
{
    private static $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
    private static $fileName = 'routes.csv';
    private $file;

    public function save($data)
    {
        if (!File::exists(self::getFileName())) {
            $this->createCsvFile();
        }

        if (!is_array($data)) {
            throw new \Exception('Mande um array valido');
        }

        $string = join(',', array_map('strtoupper', $data));
        return File::append(self::getFileName(), $string . PHP_EOL);
    }

    private function createCsvFile()
    {
        $this->file = fopen(self::getFileName(), 'w+');
        fwrite($this->file, 'from,to,price' . PHP_EOL);
        fclose($this->file);
    }

    public function read()
    {
        $csv = array_map('str_getcsv', file(self::getFileName()));
        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv); # remove column header
        return $csv;
    }

    private static function getFileName()
    {
        return self::$filePath . self::$fileName;
    }
}
