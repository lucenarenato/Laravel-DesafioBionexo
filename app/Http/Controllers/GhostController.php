<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Spatie\PdfToImage\Pdf;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Process\Process;
use Org_Heigl\Ghostscript\Ghostscript;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class GhostController extends Controller
{
    // private $filesystemOperator;

    // public function __construct(FilesystemOperator $filesystemOperator)
    // {
    //     $this->filesystemOperator = $filesystemOperator;
    // }

    public function ghostPdf()
    {
        // Create the Ghostscript-Wrapper
        $gs = new Ghostscript ();
        //$file = file_put_contents("/var/www/html/storage/local_S3/$href", file_get_contents('www/html/storage/local_S3/Leitura PDF.PDF'));
        $gs->setDevice(new Pdf('/var/www/html/storage/local_S3/Leitura PDF.PDF'));
        //$gs->setGsPath($this->gsExe);

        $gs->setInputFile(Storage::disk('local_s3')->path('Leitura PDF.PDF'));


        // Set the output file that will be created in the same directory as the input
        $gs->setOutputFile(Storage::disk('local_s3')->path('out/gost.xls'));
        $gs->setResolution(96);
        $gs->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH);
        if ($gs->render()) {
            echo 'success';
        } else {
            throw new Exception(
                sprintf(
                    'Ghostscript error: render process has failed to write compressed file: "%s"',
                    $compressedFilename
                )
            );
        }

        /*
        foreach (range(1, $pdf->getNumberOfPages()) as $pageNumber) {
            $file_name = public_path('page'.$pageNumber.'jpg');
            $pdf->setPage($pageNumber)->saveImage($file_name);
        }*/
        //$gs->render(Storage::disk('local_s3')->path('out/gost.xls'));
        // Set the resolution to 96 pixel per inch
            //->setResolution(96)
        // Set Text-antialiasing to the highest level
            //->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH);
        // Set the jpeg-quality to 100 (This is device-dependent!)
            //->getDevice()->setQuality(100);
        // convert the input file to an image
        /*if (true === $gs->render()) {
            echo 'success';
        } else {
            echo 'some error occured';
        }*/
    }

    public function uploadFile(Request $request){
        $x = 1;
        $reference_upload = array();
        foreach($request->reference_upload as $upload){
            $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
            $extension = $upload->getClientOriginalExtension();
            $filename = $datetime.'_'.$request->reqnum.'-'.$x.'.'.$extension;
            array_push($reference_upload, $filename);
            $x++;
        }
        dump($request->reqnum);
        dump($reference_upload);
        for($i=0; $i < count($reference_upload); $i++){
            $request->reference_upload[$i]->move(public_path('/uploads'), $reference_upload[$i]);
        }
        dump($request->reqnum);
        dump($reference_upload);
        $reference_delete = array();
        for($c=0; $c < count($reference_upload); $c++){
            if(str_contains($reference_upload[$c], '.pdf') == true){
                $pdf = new Pdf(public_path('uploads/'.$reference_upload[$c]));
                $pdfcount = $pdf->getNumberOfPages();
                $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
                for($a=1; $a < $pdfcount+1; $a++){
                    $filename = $datetime.'_'.$request->reqnum.'-'.$a.'-'.Str::random(5).'.jpg';
                    $pdf->setPage($a)
                        ->setOutputFormat('jpg')
                        ->saveImage(public_path('uploads/'.$filename));
                    array_push($reference_upload, $filename);
                }
                unlink(public_path('uploads/'.$reference_upload[$c]));
                array_push($reference_delete, $reference_upload[$c]);
            }
        }
        $reference_upload = json_encode($reference_upload);
        dump($reference_upload);
        for($d=0; $d < count($reference_delete); $d++){
            $reference_upload = str_replace('"'.$reference_delete[$d].'",', "", $reference_upload);
            $reference_upload = str_replace('"'.$reference_delete[$d].'"', "", $reference_upload);
            $reference_upload = str_replace($reference_delete[$d], "", $reference_upload);
        }

        dump($reference_upload);
        return 'teste';
    }
}
