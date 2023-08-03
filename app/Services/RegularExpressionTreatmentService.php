<?php

namespace App\Services;

use Exception;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RegularExpressionTreatmentService
{
    public static function readPdf(string $path)
    {
        Storage::disk('local_s3')->delete(Storage::disk('local_s3')->allFiles('temp'));
        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($path);
        $pages = $pdf->getPages();
        $pageCounter = 0;

        foreach ($pages as $page) {
            $pageCounter++;
            $data = self::getValuesByRegularExpression($page->getText());
            PdfToCsvService::convertPdfTextToCsv(Storage::disk('local_s3')->path('temp/LeituraPDF.csv'), $data);
        }

        return Storage::disk('local_s3')->files('temp');
    }

    public static function getValuesByRegularExpression(string $textpage)
    {
        // remover quebra de linhas
        $vectorPage = preg_split('/["\n"]/', $textpage);
        //dump($vectorPage);
        //busca exata
        if (preg_match('/1 \- Registro ANS/', $textpage)) {
            // paginas sem procedimentos
            return self::getPageDataWithoutProcedures($vectorPage);
        } elseif (preg_match('/13 \- Número da Guia no Prestador/', $textpage)) {
            // com procedimentos
            return self::getPageDataWithProcedures($vectorPage);
        } else {
            return [];
        }
    }

    // 1 page
    public static function getPageDataWithoutProcedures($vectorPage)
    {
        $data = [];
        // match quebra tudo que tem depois **
        preg_match('/(.*)(3 \- Nome da Operadora)/', $vectorPage[4], $match);
        //dump($match);
        $rowValue['1 - Registro ANS'] = trim($match[1]);
        //dd($rowValue['1 - Registro ANS']);
        preg_match('/(.*)(4 \- CNPJ da Operadora)/', $vectorPage[5], $match);
        $rowValue['3 - Nome da Operadora'] = trim($match[1]);

        preg_match('/(.*)(5 \- Data de Emissão)/', $vectorPage[6], $match);
        $rowValue['4 - CNPJ da Operadora'] = trim($match[1]);

        preg_match('/(44 \- Valor Liberado Geral \(R\$\)TOTAL GERAL)(.*)/', $vectorPage[13], $match);
        //dump($match);
        $rowValue['5 - Data de Emissao'] = trim($match[2]);

        preg_match('/(6 \- Código na Operadora)(.*)(7 \- Nome do Contratado)/', $vectorPage[2], $match);
        $rowValue['7 - Nome do Contratado'] = trim($match[2]);

        preg_match('/(12 \- Código da Glosa do Protocolo)(.*)/', $vectorPage[2], $match);
        $rowValue['8 - Codigo CNES'] = trim($match[2]);

        preg_match('/(8 \- Código CNES)(.*)(9 \- Número do Lote)/', $vectorPage[2], $match);
        $rowValue['9 - Numero do Lote'] = trim($match[2]);

        preg_match('/(9 \- Número do Lote)(.*)(10 \- Nº do Protocolo \(Processo\))/', $vectorPage[2], $match);
        $rowValue['10 - N do Protocolo (Processo)'] = trim($match[2]);

        preg_match('/(10 \- Nº do Protocolo \(Processo\))(.*)(11- Data do Protocolo)/', $vectorPage[2], $match);
        $rowValue['11- Data do Protocolo'] = trim($match[2]);

        $rowValue['12 - Codigo da Glosa do Protocolo'] = '';

        preg_match('/(LOTE\/PROTOCOLOOBSERVAÇÕES)(.*)(38 \- Valor Informado do Protocolo \(R\$\))/', $vectorPage[13], $match);
        $rowValue['38 - Valor Informado do Protocolo (R$)'] = trim($match[2]);

        preg_match('/(38 \- Valor Informado do Protocolo \(R\$\))(.*)(39 \- Valor Processado do Protocolo \(R\$\))/', $vectorPage[13], $match);
        $values = preg_split('/[\s]/', trim($match[2]));
        preg_match('/()(.*)()/', trim($match[2]), $match);
        $rowValue['39 - Valor Processado do Protocolo (R$)'] = trim($values[0]);

        $rowValue['40 - Valor Liberado do Protocolo (R$)'] = trim($values[1]);

        $rowValue['41 - Valor Glosa do Protocolo (R$)'] = trim($values[2]);

        preg_match('/(TOTAL DO PROTOCOLO)(.*)(42 \- Valor Informado Geral \(R\$\))/', $vectorPage[13], $match);
        $rowValue['42 - Valor Informado Geral (R$)'] = trim($match[2]);

        preg_match('/(42 \- Valor Informado Geral \(R\$\))(.*)(43 - Valor Processado Geral \(R\$\))/', $vectorPage[13], $match);
        $values = preg_split('/[\s]/', trim($match[2]));
        $rowValue['43 - Valor Processado Geral (R$)'] = trim($values[0]);

        $rowValue['44 - Valor Liberado Geral (R$)'] = trim($values[1]);

        $rowValue['45 - Valor Glosa Geral (R$)'] = trim($values[2]);

        array_push($data, $rowValue);
        //dd($data);
        return $data;
    }

    public static function getPageDataWithProcedures($vectorPage)
    {
        preg_match('/(DADOS DA GUIA)(.*)(13 \- Número da Guia no Prestador)/', $vectorPage[2], $match);
        $row['13 - Numero da Guia no Prestador'] = trim($match[2]);

        preg_match('/(13 \- Número da Guia no Prestador)(.*)(14 \- Número da Guia Atribuido pela Operadora)/', $vectorPage[2], $match);
        $row['14 - Numero da Guia Atribuido pela Operadora'] = trim($match[2]);

        preg_match('/(14 \- Número da Guia Atribuido pela Operadora)(.*)(15 \- Senha)/', $vectorPage[2], $match);
        $row['15 - Senha'] = trim($match[2]);

        preg_match('/(15 \- Senha)(.*)(16 \- Nome do Beneficiário)/', $vectorPage[2], $match);
        $row['16 - Nome do Beneficiario'] = trim($match[2]);

        preg_match('/(16 \- Nome do Beneficiário)(.*)(17 \- Número da Carteira)/', $vectorPage[2], $match);
        $row['17 - Numero da Carteira'] = trim($match[2]);

        preg_match('/(17 \- Número da Carteira)(.*)(18 \- Data Início do Faturamento)/', $vectorPage[2], $match);
        $row['18 - Data Inicio do Faturamento'] = trim($match[2]);

        preg_match('/(20 \- Data Fim do Faturamento)(.*)(19 \- Hora Início do Faturamento)/', $vectorPage[2], $match);
        $row['19 - Hora Inicio do Faturamento'] = trim($match[2]);

        preg_match('/(18 \- Data Início do Faturamento)(.*)(20 \- Data Fim do Faturamento)/', $vectorPage[2], $match);
        $row['20 - Data Fim do Faturamento'] = trim($match[2]);

        preg_match('/(19 \- Hora Início do Faturamento)(.*)(21 \- Hora Fim do Faturamento)/', $vectorPage[2], $match);
        $row['21 - Hora Fim do Faturamento'] = trim($match[2]);

        $row['22 - Codigo da Glosa da Guia'] = "";

        for ($i = 0; $i < sizeof($vectorPage); $i++) {
            if (preg_match('/(33 - Código da Glosa)(.*)/', $vectorPage[$i], $match) && strlen($vectorPage[$i]) > 87 && strlen($vectorPage[$i]) < 111) {
                $values = preg_split('/\s/', trim($match[2]));
                $row['22 - Codigo da Glosa da Guia'] = trim($values[0]) . " " . trim($values[2]);
            }
        }

        //--------------| section procedures |--------------------------

        preg_match('/(22 \- Código da Glosa da Guia)(.*)/', $vectorPage[2], $match);
        if (strlen($match[2]) > 10) {
            // 1 procedimento
            return self::buildTableWithOneProcedure($row, $vectorPage, $match); // with one procedure
        } else {
            // mas procedimento
            return self::buildTableWithManyProcedures($row, $vectorPage, $match); // form two or more procedures
        }
    }

    public static function buildTableWithOneProcedure($row, $vectorPage, $match)
    {
        $procedure = [];
        $data = [];

        $procedure['23 - Data de realizacao'] =  substr($match[2], 0, 10);
        $procedure['24 - Tabela'] =  substr($match[2], 10, 2);
        $procedure['25 - Codigo Procedimento'] =  substr($match[2], 12, 8);

        for ($i = 2; $i < sizeof($vectorPage); $i++) {
            preg_match('/(.*)(23 \- Data de)/', $vectorPage[$i], $match);
            if ($match) {

                if (strlen($match[1]) > 30) {
                    $values = preg_split('/\s/', $match[1]);
                    $procedure['28 - Valor Informado'] =  $values[sizeof($values) - 6];
                    $procedure['29 - Quant.'] =  $values[sizeof($values) - 5];
                    $procedure['30 - Valor Processado'] =  $values[sizeof($values) - 4];
                    $procedure['31 - Valor Liberado'] =  $values[sizeof($values) - 3];
                    $procedure['32 - Valor Glosa'] =  $values[sizeof($values) - 2];
                    $procedure['33 - Codigo da Glosa'] =  $vectorPage[sizeof($vectorPage) - 1];
                } else {
                    $values = preg_split('/\s/', $match[1]);
                    $procedure['28 - Valor Informado'] =  $values[1];
                    $procedure['29 - Quant.'] =  $values[2];
                    $procedure['30 - Valor Processado'] =  $values[3];
                    $procedure['31 - Valor Liberado'] =  $values[4];
                    $procedure['32 - Valor Glosa'] =  $values[5];
                    $procedure['33 - Codigo da Glosa'] =  $vectorPage[sizeof($vectorPage) - 1];
                }
            }
        }

        for ($i = 2; $i < sizeof($vectorPage); $i++) {
            preg_match('/(34 \- Valor Informado da Guia \(R\$\))(.*)(35 \- Valor Processado da Guia \(R\$\))/', $vectorPage[$i], $match);
            if ($match) {
                $values = preg_split('/\s/', $match[2]);
                $procedure['34 - Valor Informado da Guia (R$)'] =  $vectorPage[sizeof($vectorPage) - 2];
                $procedure['35 - Valor Processado da Guia (R$)'] =  $values[1];
                $procedure['36 - Valor Liberado da Guia (R$)'] =  $values[2];
                $procedure['37 - Valor Glosa da Guia (R$)'] =  $values[3];
            }
        }

        array_push($data, array_merge($row, $procedure));

        return $data;
    }

    public static function buildTableWithManyProcedures($row, $vectorPage, $match)
    {
        $registersTemp = ['Date' => [], 'table' => [], 'codProcedure' => [], 'vlrInfo' => [], 'qtde' => [], 'vrlProc' => [], 'vrlLib' => [], 'vlrGlosa' => [], 'codGlosa' => []];
        $valuesTemp = [];
        $totalGuia = [];
        $data = [];

        array_push($registersTemp['Date'], $match[2]);
        for ($i = 3; $i < sizeof($vectorPage); $i++) {

            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($vectorPage[$i]), $match)) {
                //dd($vectorPage[$i]);
                array_push($registersTemp['Date'], trim($match[0]));
            } elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{6})$/', trim($vectorPage[$i]), $match)) {

                array_push($registersTemp['Date'], substr(trim($match[0]), 0, 10));
                array_push($registersTemp['table'], substr(trim($match[0]), 10, 2));
            } elseif (preg_match('/^(\d{2})$/', trim($vectorPage[$i]), $match)) {

                array_push($registersTemp['table'], trim($match[0]));
            } elseif (preg_match('/^(\d{10})$/', trim($vectorPage[$i]), $match)) {

                array_push($registersTemp['table'], substr(trim($match[0]), 0, 2));
                array_push($registersTemp['codProcedure'], substr(trim($match[0]), 2, 8));
            } elseif (preg_match('/^(\d{8})$/', trim($vectorPage[$i]), $match)) {

                array_push($registersTemp['codProcedure'], trim($match[0]));
            } elseif (preg_match('/^(\d{8})([a-zA-Z])/', trim($vectorPage[$i]), $match)) {

                array_push($registersTemp['codProcedure'], trim($match[1]));
            }
        }

        for ($i = 3; $i < sizeof($vectorPage); $i++) {
            if (preg_match('/([0-9]{0,15}[\.]{0,1}[0-9]{0,15})[,]{1,1}[0-9]{0,2}/', trim($vectorPage[$i])) && !preg_match('/\(/', trim($vectorPage[$i])) || strlen(trim($vectorPage[$i])) == 1) {
                $txt =  preg_split('/(\s)/', trim($vectorPage[$i]));
                for ($j = 0; $j < sizeof($txt); $j++) {
                    array_push($valuesTemp, trim($txt[$j]));
                }
            }
        }
        // array registersTemp
        dd($valuesTemp);

        $indexValueTemp = 0;
        for ($indexValueTemp; $indexValueTemp < sizeof($valuesTemp); $indexValueTemp++) {

            if (!preg_match('/(\s)/', $valuesTemp[$indexValueTemp]) && preg_match('/(\,)/', $valuesTemp[$indexValueTemp]) && !preg_match('/([a-zA-Z])/', $valuesTemp[$indexValueTemp])) {
                for ($i = 0; $i < sizeof($registersTemp['Date']); $i++) {
                    array_push($registersTemp['vlrInfo'], $valuesTemp[$indexValueTemp]);
                    $indexValueTemp++;
                }
                break;
            }
        }

        for ($indexValueTemp; $indexValueTemp < sizeof($valuesTemp); $indexValueTemp++) {

            if (!preg_match('/(\s)/', $valuesTemp[$indexValueTemp]) && strlen($valuesTemp[$indexValueTemp]) == 1) {
                for ($i = 0; $i < sizeof($registersTemp['Date']); $i++) {
                    array_push($registersTemp['qtde'], $valuesTemp[$indexValueTemp]);
                    $indexValueTemp++;
                }
                break;
            }
        }

        for ($indexValueTemp; $indexValueTemp < sizeof($valuesTemp); $indexValueTemp++) {

            if (!preg_match('/(\s)/', $valuesTemp[$indexValueTemp])) {
                for ($i = 0; $i < sizeof($registersTemp['Date']); $i++) {
                    array_push($registersTemp['vrlProc'], $valuesTemp[$indexValueTemp]);
                    $indexValueTemp++;
                }
                break;
            }
        }

        for ($indexValueTemp; $indexValueTemp < sizeof($valuesTemp); $indexValueTemp++) {

            if (!preg_match('/(\s)/', $valuesTemp[$indexValueTemp])) {
                for ($i = 0; $i < sizeof($registersTemp['Date']); $i++) {
                    array_push($registersTemp['vrlLib'], $valuesTemp[$indexValueTemp]);
                    $indexValueTemp++;
                }
                break;
            }
        }

        for ($indexValueTemp; $indexValueTemp < sizeof($valuesTemp); $indexValueTemp++) {

            if (!preg_match('/(\s)/', $valuesTemp[$indexValueTemp])) {
                for ($i = 0; $i < sizeof($registersTemp['Date']); $i++) {
                    array_push($registersTemp['vlrGlosa'], $valuesTemp[$indexValueTemp]);
                    $indexValueTemp++;
                }
                break;
            }
        }

        for ($i = 0; $i < sizeof($vectorPage); $i++) {
            if (preg_match('/(Executada)(.*)/', $vectorPage[$i], $match)) {

                if (strlen($vectorPage[$i]) > 100) {
                    $position = $i + 3;
                    for ($j = 0; $j < sizeof($registersTemp['Date']); $j++) {
                        array_push($registersTemp['codGlosa'], trim($vectorPage[$position]));
                        $position++;
                    }

                    preg_match('/(34 \- Valor Informado da Guia \(R\$\))(.*)(35 \- Valor Processado da Guia \(R\$\))/', $vectorPage[$i], $match);
                    if ($match) {

                        $vlrInfo = $vectorPage[$i + 2];
                        $valueInfo = preg_split('/\s/', $vlrInfo);

                        $values = preg_split('/\s/', $match[2]);
                        $totalGuia['34 - Valor Informado da Guia (R$)'] =  substr($valueInfo[1], 0, 8);
                        $totalGuia['35 - Valor Processado da Guia (R$)'] =  $values[1];
                        $totalGuia['36 - Valor Liberado da Guia (R$)'] =  $values[2];
                        $totalGuia['37 - Valor Glosa da Guia (R$)'] =  $values[3];
                    }
                } else {
                    $position = sizeof($vectorPage) -  sizeof($registersTemp['Date']);
                    for ($j = 0; $j < sizeof($registersTemp['Date']); $j++) {
                        array_push($registersTemp['codGlosa'], trim($vectorPage[$position]));
                        $position++;
                    }
                    $totalGuia['34 - Valor Informado da Guia (R$)'] =  "";
                    $totalGuia['35 - Valor Processado da Guia (R$)'] =  "";
                    $totalGuia['36 - Valor Liberado da Guia (R$)'] =  "";
                    $totalGuia['37 - Valor Glosa da Guia (R$)'] =  "";
                }
            }
        }

        $rowProcedure = [];
        for ($i = 0; $i < sizeof($registersTemp['Date']); $i++) {

            $rowProcedure['23 - Data de realizacao'] = $registersTemp['Date'][$i];
            try {
                $rowProcedure['24 - Tabela'] = $registersTemp['table'][$i];
            } catch (\Exception $e) {
                $rowProcedure['24 - Tabela'] = "";
            }
            $rowProcedure['25 - Codigo Procedimento'] = $registersTemp['codProcedure'][$i];
            $rowProcedure['28 - Valor Informado'] = $registersTemp['vlrInfo'][$i];
            $rowProcedure['29 - Quantidade'] = $registersTemp['qtde'][$i];
            $rowProcedure['30 - Valor Processado'] = $registersTemp['vrlProc'][$i];
            $rowProcedure['31 - Valor Liberado'] = $registersTemp['vrlLib'][$i];
            $rowProcedure['32 - Valor Glosa'] = $registersTemp['vlrGlosa'][$i];
            $rowProcedure['33 - Codigo da Glosa'] = $registersTemp['codGlosa'][$i];


            array_push($data, array_merge($row, $rowProcedure, $totalGuia));
        }

        dd($row, $registersTemp, $totalGuia);

        return $data;
    }

}
