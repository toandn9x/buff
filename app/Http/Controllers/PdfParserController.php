<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use Smalot\PdfParser\Parser;

class PdfParserController extends Controller
{
    //
    public function test() {


        $parser = new Parser();
        $pdf = $parser->parseFile('C:\Users\Admin\Desktop\hihi\b.pdf');
        $text = $pdf->getPages()[0]->getText();
        $tables = $pdf->getTables();
        dd($tables);

    }
}
