<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class SpreadsheetController {

    private $sheetName;
    private $savePath;
    public $sheet;

    public function __construct($sheetName) {
        $this->sheetName = $sheetName;
        $spreadSheet = new Spreadsheet();
        $this->sheet = $spreadSheet->getActiveSheet();
    }

    public function setCellValue($cell, $value) {
        $this->sheet = $this->sheet->setCellValue($cell, $value);
    }

    public function saveSpreadSheet($path) {
        $this->savePath = $path;
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($path . $this->sheetName . '.xlsx');
    }

    public function downloadSpreadSheet($path) {
        header("Location: " . $this->savePath . $this->sheetName . '.xlsx');
    }
}