<?php

use Illuminate\Support\Facades\Route;
use KhmerPdf\LaravelKhPdf\Facades\PdfKh;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/kh-pdf-test', function () {
    $html = view('khPdf_test', ['title' => 'សួស្តី ពិភពលោក!'])->render();
    return PdfKh::loadHtml($html)->stream('khmer_document.pdf');
});
