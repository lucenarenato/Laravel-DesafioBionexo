<?php

namespace App\Repositories;

use Exception;
use App\Http\Requests\Request;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Log;
use App\Services\RegexUtils;
use Illuminate\Support\Facades\Storage;

class PdfRepository
{
    public function converterPdf(): array
    {
        $rUtils = new RegexUtils();

        $text = Pdf::getText(Storage::disk('local_s3')->path('Leitura PDF.PDF'));

        $operA = $rUtils->origin($text)->pre("OPERADORA")->pos("DADOS DO PRESTADOR")->get();
        $data['registro ans'] = intval($rUtils->origin($operA)->rule("(\d{6})")->get());
        $data['operadora nome'] = trim($rUtils->origin($operA)->pre("{$data['registro ans']}\n")->get());


        $prestA = $rUtils->origin($text)->pre("DADOS DO PRESTADOR")->pos("DADOS DO LOTE/PROTOCOLO")->get();
        $data['código na operadora'] = intval($rUtils->origin($prestA)->rule("(\d{5})")->get());
        $data['nome contratado'] = trim($rUtils->origin($prestA)->pre("7 - Nome do Contratado")->get());


        $lPA = $rUtils->origin($text)->pre("DADOS DO LOTE/PROTOCOLO")->pos("TOTAL DO PROTOCOLO")->get();
        $data['numero lote'] = intval($rUtils->origin($lPA)->rule("(\d{1})")->get());
        $data['numero protocolo'] = intval($rUtils->origin($lPA)->rule("(\d{7})")->get());
        $data['data protocolo'] = trim($rUtils->origin($lPA)->pre("11- Data do Protocolo")->rule("(\d{2}\/\d{2}\/\d{4})")->get());
        $data['codigo glosa protocolo'] = intval($rUtils->origin($lPA)->pre("12 - Código da Glosa do Protocolo")->get());


        $totalsPA = $rUtils->origin($text)->pre("TOTAL DO PROTOCOLO")->pos("TOTAL GERAL")->get();
        list(
            $data['valor informado protocolo'],
            $data['valor processado protocolo'],
            $data['valor liberado protocolo'],
            $data['valor glosa protocolo']
        ) = array_map([$this, 'convertNumber'], $rUtils->origin($totalsPA)->pre(null)->pos(null)->rule("(\d{1,2}\.\d{3},\d{2})")->all()->get());


        $totalsGA = $rUtils->origin($text)->pre("TOTAL GERAL")->pos("OBSERVAÇÕES")->get();
        list(
            $data['valor informado geral'],
            $data['valor processado geral'],
            $data['valor liberado geral'],
            $data['valor glosa geral']
        ) = array_map([$this, 'convertNumber'], $rUtils->origin($totalsGA)->pre(null)->pos(null)->rule("(\d{1,2}\.\d{3},\d{2})")->all()->get());


        $guideA = $rUtils->origin($text)->pre("DADOS DA GUIA")->pos("Impressão - Portal do Prestador")->all()->get();
        foreach ($guideA as $guideI => $guideAr) {

            $data['guias'][$guideI]['guia no prestador'] = intval($rUtils->origin($guideAr)->pre("13 - Número da Guia no Prestador")->rule("(\d{10,13})")->get());
            $data['guias'][$guideI]['senha'] = trim($rUtils->origin($guideAr)->pre("15 - Senha")->get());
            $data['guias'][$guideI]['nome beneficiario'] = trim($rUtils->origin($guideAr)->pre("16 - Nome do Beneficiário")->get());
            $data['guias'][$guideI]['numero carteira'] = trim($rUtils->origin($guideAr)->pre("17 - Número da Carteira")->get());

            $factoreA = $rUtils->origin($guideAr)->pre("21 - Hora Fim do Faturamento")->pos("23 - Data de\nrealização")->get();
            list($datainiciofaturamento, $datafimfaturamento) = array_map('trim', $rUtils->origin($factoreA)->rule("(\d{2}\/\d{2}\/\d{4})")->all()->get());
            list($horainiciofaturamento, $horafimfaturamento) = array_map('trim', $rUtils->origin($factoreA)->rule("(\d{2}\:\d{2})")->all()->get());
            $data['guias'][$guideI]['data inicio faturamento'] = $datainiciofaturamento;
            $data['guias'][$guideI]['hora inicio faturamento'] = $horainiciofaturamento;
            $data['guias'][$guideI]['data fim faturamento'] = $datafimfaturamento;
            $data['guias'][$guideI]['codigo glosa guia'] = trim($rUtils->origin($factoreA)->pre("{$horafimfaturamento}\n")->get());


            $proceduresArea = $rUtils->origin($guideAr)->pre("23 - Data de\nrealização\n")->pos("TOTAL DA GUIA")->get();
            $datas = array_map('trim', $rUtils->origin($proceduresArea ?: $guideAr)->pre(null)->pos(null)->rule("(\d{2}\/\d{2}\/\d{4})")->all()->get());
            $procedures = array_map('intval', $rUtils->origin($proceduresArea ?: $guideAr)->pre(null)->pos(null)->rule("(\d{8}) [a-zA-Z ().,\-]+")->all()->get());
            $value = array_map([$this, 'convertNumber'], $rUtils->origin($proceduresArea ?: $guideAr)->pre(null)->pos(null)->rule("(\d{1,3},\d{2})")->all()->get());
            $glosa = array_map('trim', $rUtils->origin($proceduresArea ?: $guideAr)->pre(null)->pos(null)->rule("(\d{2}\.\d{2})")->all()->get());

            foreach ($procedures as $procedureItem => &$proceduretext) {
                $descricao = trim($rUtils->origin($proceduresArea ?: $guideAr)->pre("{$proceduretext} ", false)->rule("([\da-zA-Z ().,\-]+)")->get());
                //dump($descricao);
                if (Str::contains($data['guias'][$guideI]['guia no prestador'], $proceduretext)) {
                    unset($procedures[$procedureItem]);
                } else {
                    $proceduretext .= " " . $descricao;
                }
            }

            unset($procedureItem);
            unset($proceduretext);
            $procedures = array_values($procedures);
            $data['guias'][$guideI]['valor informado guia'] = 0;
            $data['guias'][$guideI]['valor processado guia'] = 0;
            $data['guias'][$guideI]['valor liberado guia'] = 0;
            $data['guias'][$guideI]['valor glosa guia'] = 0;

            foreach ($procedures as $procedureItem => $proceduretext) {
                $data['guias'][$guideI]['valor informado guia'] += $value[$procedureItem];
                $data['guias'][$guideI]['valor processado guia'] += $value[$procedureItem + count($procedures)];
                $data['guias'][$guideI]['valor liberado guia'] += $value[$procedureItem + count($procedures) * 2];
                $data['guias'][$guideI]['valor glosa guia'] = isset($glosa[$procedureItem]) ? $glosa[$procedureItem] : null;
                $data['guias'][$guideI]['procedimentos'][$procedureItem] = [
                    'data' => $datas[$procedureItem],
                    'descricao' => $proceduretext,
                    'codigo procedure' => $proceduretext,
                    'valor informado' => $value[$procedureItem],
                    'valor processado' => $value[$procedureItem + count($procedures)],
                    'valor liberado' => $value[$procedureItem + count($procedures) * 2],
                    'valor glosa' => isset($glosa[$procedureItem]) ? $glosa[$procedureItem] : null
                ];
            }
        }

        return $data;
    }

    protected function convertNumber(string $number, bool $brasil = true): ?float
    {
        if ($brasil) $number = Str::replace('.', '', $number);

        $number = Str::replace(',', '.', $number);

        return is_numeric($number) ? (float) $number : null;
    }

}
