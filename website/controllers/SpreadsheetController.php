<?php

namespace classes;

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class SpreadsheetController {

    private $sheetName;
    private $savePath;
    private $spreadsheet;
    public $sheet;

    public function __construct($sheetName) {
        $this->sheetName = $sheetName;
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    public function setCellValue($cell, $value) {
        $this->sheet->setCellValue($cell, $value);
    }

    public function saveSpreadSheet($path) {
        $this->savePath = $path;
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($path . $this->sheetName . '.xlsx');
    }

    public function downloadSpreadSheet() {
        header("Location: " . $this->savePath . $this->sheetName . '.xlsx');
    }
}