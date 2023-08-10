<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\PdfDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

class StorePdfDocumentAsText implements ShouldQueue
{
    public function handle()
    {
        PdfDocument::create([
            'title' => 'DEMONSTRATIVO DE ANÁLISE DE CONTA',
            'content' => Str::limit(
                Pdf::getText(
                    Storage::disk('local_s3')->path('Leitura PDF.PDF')
                ),
                60000,
            ),
        ]);
    }
}
