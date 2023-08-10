<?php

namespace App\Http\Controllers;

use League\Csv\Writer;
use League\Csv\Reader;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\PdfDocument;
use App\Repositories\PdfRepository;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class PDFController extends Controller
{
    public function converterPdf()
    {
        $text = Pdf::getText(Storage::disk('local s3')->path('Leitura PDF.PDF'));

        $arrayOfPages = preg_split('/["\n"]/', $text);
        //dd($arrayOfPages);
        $pages = [];
        preg_match('/(\/)(.*)/', $arrayOfPages[4], $match);

        $qtePage = intval($match[2]);

        for ($i = 1; $i < $qtePage; $i++) {
            $indexQtde = str_pad($i, 4, 0, STR_PAD_LEFT);
            $pattern = "/(.)($indexQtde)(.)/";
            //dump($pattern);

            foreach ($arrayOfPages as $page) {
                if (preg_match($pattern, $page, $match)) {
                    $pages[] = $match[0];
                }
            }
        }

        dd($pages);

        // for ($i = 0; $i < sizeof($arrayOfPages); $i++) {
        //     if (preg_match('/(.*)(0001)(.*)/', $arrayOfPages[$i], $match)) {
        //        dd($match[2]);
        //     }
        // }
    }

    public function teste()
    {
        $text = Pdf::getText(Storage::disk('local s3')->path('Leitura PDF.PDF'));
        $arrayOfPages = preg_split('/["\n"]/', $text);


        preg_match('/(\/)(.*)/', $arrayOfPages[4], $match);

        $qtePage = intval($match[2]);
        $pages = [];

        for ($i = 1; $i < $qtePage; $i++) {
            $indexQtde = str_pad($i, 4, 0, STR_PAD_LEFT);

            $pattern = "/(.*)($indexQtde)(.*)/";


            if (preg_match($pattern, $arrayOfPages[$i], $match)) {
                $pages = $match[2];

            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pdfmysql",
     *     tags={"Desafio"},
     *     summary="pdfmysql file",
     *     description="pdfmysql",
     *     security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Success pdfmysql" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function pdfMysql()
    {
        $results = PdfDocument::search('Operadora')
            ->paginate(20);
        return response()->json($results, Response::HTTP_OK);
    }

    public function gerarCsv(): void
    {
                // Cria o objeto CSV a partir do caminho do arquivo e do modo de escrita
        $csv = \League\Csv\Writer::createFromString();
        //$csv = \League\Csv\Writer::createFromFileObject(new SplTempFileObject());

        // Insere o cabeçalho no arquivo CSV
        $csv->insertOne([
            'registro ans',
            'operadora nome',
            'código na operadora',
            'nome contratado',
            'numero lote',
            'numero protocolo',
            'data protocolo',
            'codigo glosa protocolo',
            'valor informado protocolo',
            'valor processado protocolo',
            'valor liberado protocolo',
            'valor glosa protocolo',
            'valor informado geral',
            'valor processado geral',
            'valor liberado geral',
            'valor glosa geral',
            'guia no prestador',
            'senha',
            'nome beneficiario',
            'numero carteira',
            'data inicio faturamento',
            'hora inicio faturamento',
            'data fim faturamento',
            'codigo glosa guia',
            'valor informado guia',
            'valor processado guia',
            'valor liberado guia',
            'valor glosa guia',
            'data',
            'descricao',
            'valor informado',
            'valor processado',
            'valor liberado',
            'valor glosa',
            'codigo procedimento'
        ]);

        $dados = new PdfRepository();
        $dados = $dados->converterPdf();

        // Itera sobre as guias e procedimentos para inserir as linhas no CSV
        foreach ($dados['guias'] as $guia) {
            foreach ($guia['procedimentos'] as $procedure) {
                $csv->insertOne([
                    $dados['registro ans'],
                    $dados['operadora nome'],
                    $dados['código na operadora'],
                    $dados['nome contratado'],
                    $dados['numero lote'],
                    $dados['numero protocolo'],
                    $dados['data protocolo'],
                    $dados['codigo glosa protocolo'],
                    $dados['valor informado protocolo'],
                    $dados['valor processado protocolo'],
                    $dados['valor liberado protocolo'],
                    $dados['valor glosa protocolo'],
                    $dados['valor informado geral'],
                    $dados['valor processado geral'],
                    $dados['valor liberado geral'],
                    $dados['valor glosa geral'],
                    $guia['guia no prestador'],
                    $guia['senha'],
                    $guia['nome beneficiario'],
                    $guia['numero carteira'],
                    $guia['data inicio faturamento'],
                    $guia['hora inicio faturamento'],
                    $guia['data fim faturamento'],
                    $guia['codigo glosa guia'],
                    $guia['valor informado guia'],
                    $guia['valor processado guia'],
                    $guia['valor liberado guia'],
                    $guia['valor glosa guia'],
                    $procedure['data'],
                    $procedure['descricao'] = isset($procedure['descricao']) ? $procedure['descricao'] : null,
                    $procedure['valor informado'],
                    $procedure['valor processado'],
                    $procedure['valor liberado'],
                    $procedure['valor glosa'],
                ]);
            }
        }

        // Gera a saída do arquivo CSV
        $csv->output('result.csv');
    }

}
