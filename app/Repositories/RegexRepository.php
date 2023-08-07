<?php

namespace App\Repositories;

use Exception;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Log;

class RegexRepository
{
    // 1 page
    static function searchTextOnPageWithoutProcedures($arrayOfPages)
    {
        try {
            $groupData = array();
            // match quebra tudo que tem depois *
            // -> Esta função procura um padrão específico em alguma string. Retorna verdadeiro se o padrão existir e falso caso contrário.
            preg_match('/(.*)(3 \- Nome da Operadora)/', $arrayOfPages[4], $matches);
            //dump($matches);
            // logo em seguida pego numero da ANS // -> lineOfEachInformation = linha de cada informação
            $lineOfEachInformation['1 - Registro ANS'] = trim($matches[1]);
            //dd($lineOfEachInformation['1 - Registro ANS']);
            preg_match('/(.*)(4 \- CNPJ da Operadora)/', $arrayOfPages[5], $matches);
            $lineOfEachInformation['3 - Nome da Operadora'] = trim($matches[1]);

            preg_match('/(.*)(5 \- Data de Emissão)/', $arrayOfPages[6], $matches);
            $lineOfEachInformation['4 - CNPJ da Operadora'] = trim($matches[1]);

            preg_match('/(44 \- Valor Liberado Geral \(R\$\)TOTAL GERAL)(.*)/', $arrayOfPages[13], $matches);
            //dump($matches);
            $lineOfEachInformation['5 - Data de Emissao'] = trim($matches[2]);

            preg_match('/(6 \- Código na Operadora)(.*)(7 \- Nome do Contratado)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['7 - Nome do Contratado'] = trim($matches[2]);

            preg_match('/(12 \- Código da Glosa do Protocolo)(.*)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['8 - Codigo CNES'] = trim($matches[2]);

            preg_match('/(8 \- Código CNES)(.*)(9 \- Número do Lote)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['9 - Numero do Lote'] = trim($matches[2]);

            preg_match('/(9 \- Número do Lote)(.*)(10 \- Nº do Protocolo \(Processo\))/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['10 - N do Protocolo (Processo)'] = trim($matches[2]);

            preg_match('/(10 \- Nº do Protocolo \(Processo\))(.*)(11- Data do Protocolo)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['11- Data do Protocolo'] = trim($matches[2]);

            $lineOfEachInformation['12 - Codigo da Glosa do Protocolo'] = '';

            preg_match('/(LOTE\/PROTOCOLOOBSERVAÇÕES)(.*)(38 \- Valor Informado do Protocolo \(R\$\))/', $arrayOfPages[13], $matches);
            $lineOfEachInformation['38 - Valor Informado do Protocolo (R$)'] = trim($matches[2]);

            preg_match('/(38 \- Valor Informado do Protocolo \(R\$\))(.*)(39 \- Valor Processado do Protocolo \(R\$\))/', $arrayOfPages[13], $matches);
            $values = preg_split('/[\s]/', trim($matches[2])); // \s quebrar por espaço - cria nova posição array
            preg_match('/()(.*)()/', trim($matches[2]), $matches);
            $lineOfEachInformation['39 - Valor Processado do Protocolo (R$)'] = trim($values[0]);

            $lineOfEachInformation['40 - Valor Liberado do Protocolo (R$)'] = trim($values[1]);

            $lineOfEachInformation['41 - Valor Glosa do Protocolo (R$)'] = trim($values[2]);

            preg_match('/(TOTAL DO PROTOCOLO)(.*)(42 \- Valor Informado Geral \(R\$\))/', $arrayOfPages[13], $matches);
            $lineOfEachInformation['42 - Valor Informado Geral (R$)'] = trim($matches[2]);

            preg_match('/(42 \- Valor Informado Geral \(R\$\))(.*)(43 - Valor Processado Geral \(R\$\))/', $arrayOfPages[13], $matches);
            $values = preg_split('/[\s]/', trim($matches[2]));
            $lineOfEachInformation['43 - Valor Processado Geral (R$)'] = trim($values[0]);

            $lineOfEachInformation['44 - Valor Liberado Geral (R$)'] = trim($values[1]);

            $lineOfEachInformation['45 - Valor Glosa Geral (R$)'] = trim($values[2]);

            array_push($groupData, $lineOfEachInformation);
            //dd($groupData);
            return $groupData;
        } catch (Exception $error) {
            Log::error($error->getMessage());
            Log::error($error->getTraceAsString());
            return $error->getMessage();
        }

    }

    static function searchTextOnPageWithProcedures($arrayOfPages)
    {
        try {
            //dump($arrayOfPages);
            //dump($arrayOfPages[5]);

            preg_match('/(DADOS DA GUIA)(.*)(13 \- Número da Guia no Prestador)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['13 - Numero da Guia no Prestador'] = trim($matches[2]);


            preg_match('/(13 \- Número da Guia no Prestador)(.*)(14 \- Número da Guia Atribuido pela Operadora)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['14 - Numero da Guia Atribuido pela Operadora'] = trim($matches[2]);


            preg_match('/(14 \- Número da Guia Atribuido pela Operadora)(.*)(15 \- Senha)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['15 - Senha'] = trim($matches[2]);


            preg_match('/(15 \- Senha)(.*)(16 \- Nome do Beneficiário)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['16 - Nome do Beneficiario'] = trim($matches[2]);

            preg_match('/(16 \- Nome do Beneficiário)(.*)(17 \- Número da Carteira)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['17 - Numero da Carteira'] = trim($matches[2]);

            preg_match('/(17 \- Número da Carteira)(.*)(18 \- Data Início do Faturamento)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['18 - Data Inicio do Faturamento'] = trim($matches[2]);

            preg_match('/(20 \- Data Fim do Faturamento)(.*)(19 \- Hora Início do Faturamento)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['19 - Hora Inicio do Faturamento'] = trim($matches[2]);

            preg_match('/(18 \- Data Início do Faturamento)(.*)(20 \- Data Fim do Faturamento)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['20 - Data Fim do Faturamento'] = trim($matches[2]);

            preg_match('/(19 \- Hora Início do Faturamento)(.*)(21 \- Hora Fim do Faturamento)/', $arrayOfPages[2], $matches);
            $lineOfEachInformation['21 - Hora Fim do Faturamento'] = trim($matches[2]);

            $string22 = '22 - Codigo da Glosa da Guia';

            $lineOfEachInformation[$string22] = "";

            $string33 =  '/(33 - Código da Glosa)(.*)/';

            // sizeof -> contar numero de elementos dentro do array
            for ($i = 0; $i < sizeof($arrayOfPages); $i++) {
                if (preg_match($string33, $arrayOfPages[$i], $matches) && strlen($arrayOfPages[$i]) > 87 && strlen($arrayOfPages[$i]) < 111) {
                    // -> A função se comporta como a função split() do PHP. Ele divide a string por expressões regulares como seus parâmetros.
                    $values = preg_split('/\s/', trim($matches[2]));
                    $lineOfEachInformation[$string22] = trim($values[0]) . " " . trim($values[2]);
                }
            }

            // VERIFICAR PROCEDIMENTOS
            $string22CDG = '/(22 \- Código da Glosa da Guia)(.*)/';
            preg_match($string22CDG, $arrayOfPages[2], $matches);
            if (strlen($matches[2]) > 10) {
                // 1 procedimento
                return self::checkAProcedure($lineOfEachInformation, $arrayOfPages, $matches);
            } else {
                // mas procedimento
                return self::checkSeveralProcedures($lineOfEachInformation, $arrayOfPages, $matches);
            }
        } catch (Exception $error) {
            Log::error($error->getMessage());
            Log::error($error->getTraceAsString());
            return $error->getMessage();
        }
    }

    static function checkAProcedure($lineOfEachInformation, $arrayOfPages, $match)
    {
        try {
            $procedureDescription = [];
            $mergedData = [];

            $procedureDescription['23 - Data de realizacao'] =  substr($match[2], 0, 10);
            $procedureDescription['24 - Tabela'] =  substr($match[2], 10, 2);
            $procedureDescription['25 - Codigo Procedimento'] =  substr($match[2], 12, 8);
            // Pegar a descrição
            preg_match('/[a-zA-Z](.*)/', $match[2], $match);
            preg_match('/\D+/', $match[0], $match);
            $procedureDescription['26 - Descrição'] = $match[0]; //27/02/20202233010021USG ABDOMEN TOTAL(ABDOMEN 142,80 1 142,80 140,00 2,80 23 - Data de

            for ($i = 2; $i < sizeof($arrayOfPages); $i++) {
                preg_match('/(.*)(23 \- Data de)/', $arrayOfPages[$i], $match);
                if ($match) {

                    if (strlen($match[1]) > 30) {
                        $values = preg_split('/\s/', $match[1]);
                        $procedureDescription['28 - Valor Informado'] =  $values[sizeof($values) - 6];
                        $procedureDescription['29 - Quant.'] =  $values[sizeof($values) - 5];
                        $procedureDescription['30 - Valor Processado'] =  $values[sizeof($values) - 4];
                        $procedureDescription['31 - Valor Liberado'] =  $values[sizeof($values) - 3];
                        $procedureDescription['32 - Valor Glosa'] =  $values[sizeof($values) - 2];
                        $procedureDescription['33 - Codigo da Glosa'] =  $arrayOfPages[sizeof($arrayOfPages) - 1];
                    } else {
                        $values = preg_split('/\s/', $match[1]);
                        $procedureDescription['28 - Valor Informado'] =  $values[1];
                        $procedureDescription['29 - Quant.'] =  $values[2];
                        $procedureDescription['30 - Valor Processado'] =  $values[3];
                        $procedureDescription['31 - Valor Liberado'] =  $values[4];
                        $procedureDescription['32 - Valor Glosa'] =  $values[5];
                        $procedureDescription['33 - Codigo da Glosa'] =  $arrayOfPages[sizeof($arrayOfPages) - 1];
                    }
                }
            }

            for ($i = 2; $i < sizeof($arrayOfPages); $i++) {
                preg_match('/(34 \- Valor Informado da Guia \(R\$\))(.*)(35 \- Valor Processado da Guia \(R\$\))/', $arrayOfPages[$i], $match);
                if ($match) {
                    $values = preg_split('/\s/', $match[2]);
                    $procedureDescription['34 - Valor Informado da Guia (R$)'] =  $arrayOfPages[sizeof($arrayOfPages) - 2];
                    $procedureDescription['35 - Valor Processado da Guia (R$)'] =  $values[1];
                    $procedureDescription['36 - Valor Liberado da Guia (R$)'] =  $values[2];
                    $procedureDescription['37 - Valor Glosa da Guia (R$)'] =  $values[3];
                }
            }

            array_push($mergedData, array_merge($lineOfEachInformation, $procedureDescription));

            return $mergedData;
        } catch (Exception $error) {
            Log::error($error->getMessage());
            Log::error($error->getTraceAsString());
            return $error->getMessage();
        }
    }

    static function checkSeveralProcedures($lineOfEachInformation, $arrayOfPages, $matches)
    {
        try {
            //dump($lineOfEachInformation); dump($arrayOfPages); dd($matches);
            $header = [
                'Date' => array(),
                'table' => array(),
                'procedure' => array(),
                'value' => array(),
                'amount' => array(),
                'amountProcedure' => array(),
                'amountReleased' => array(),
                'glossValue' => array(),
                'glossCode' => array(),
                'description' => array(),

            ];
            $arrayValues = array();
            $guideTotalValue = array();
            $filteredData = array();

            array_push($header['Date'], $matches[2]);
            for ($i = 3; $i < sizeof($arrayOfPages); $i++) {
                //dd($arrayOfPages);
                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($arrayOfPages[$i]), $matches)) {

                    array_push($header['Date'], trim($matches[0]));
                } elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{6})$/', trim($arrayOfPages[$i]), $matches)) {

                    array_push($header['Date'], substr(trim($matches[0]), 0, 10));
                    array_push($header['table'], substr(trim($matches[0]), 10, 2));
                } elseif (preg_match('/^(\d{2})$/', trim($arrayOfPages[$i]), $matches)) {

                    array_push($header['table'], trim($matches[0]));
                } elseif (preg_match('/^(\d{10})$/', trim($arrayOfPages[$i]), $matches)) {

                    array_push($header['table'], substr(trim($matches[0]), 0, 2));
                    array_push($header['procedure'], substr(trim($matches[0]), 2, 8));
                } elseif (preg_match('/^(\d{8})$/', trim($arrayOfPages[$i]), $matches)) {

                    array_push($header['procedure'], trim($matches[0]));
                } elseif (preg_match('/^(\d{8})([a-zA-Z])/', trim($arrayOfPages[$i]), $matches)) {

                    array_push($header['procedure'], trim($matches[1]));
                }

                if (preg_match('/^(\d{8})([a-zA-Z])/', trim($arrayOfPages[$i]), $matches)) {
                    preg_match('/[a-zA-Z](.*)/', $arrayOfPages[$i], $match);
                    preg_match('/\D+/', $match[0], $match);
                    $groupDescProcedures = trim($match[0]);

                    $index = 0;
                    for ($f = 0; $f < sizeof($header['Date']); $f++) {
                        $descriptionProcedure = substr($groupDescProcedures, $index, 27);
                        $index = $index + 27;
                        array_push($header['description'], $descriptionProcedure);

                    }
                }

            }
            // dd($header['description']);

            $arraySize = sizeof($arrayOfPages);
            // Defina um padrão de expressão regular para uso posterior - de 0 a 9, na posição 15 .......
            $pattern = '/([0-9]{0,15}[\.]{0,1}[0-9]{0,15})[,]{1,1}[0-9]{0,2}/';
            // Loop na matriz(array)
            for ($i = 3; $i < $arraySize; $i++) {
                $currentPage = trim($arrayOfPages[$i]);
                $isValidPage = preg_match($pattern, $currentPage) && !preg_match('/\(/', $currentPage);
                $isSingleChar = strlen($currentPage) == 1;
                if ($isValidPage || $isSingleChar) {
                    // Divida a página atual e adicione suas partes à matriz de valores - valores informado
                    $txt = preg_split('/(\s)/', $currentPage);
                    foreach ($txt as $textPart) {
                        $arrayValues[] = trim($textPart);
                    }
                }
            }
            //dd($arrayValues);
            $headerDateSize = sizeof($header['Date']);

            for ($memory = 0; $memory < sizeof($arrayValues); $memory++) {
                $currentValue = $arrayValues[$memory];

                // Verifique a condição para inserir os valores $header['value']
                if (!preg_match('/(\s)/', $currentValue) && preg_match('/(\,)/', $currentValue) && !preg_match('/([a-zA-Z])/', $currentValue)) {
                    // Use array_fill() para replicar o $currentValue para o tamanho de $header['Date']
                    $valuesToPush = array_fill(0, $headerDateSize, $currentValue);
                    $header['value'] = array_merge($header['value'], $valuesToPush);
                    break;
                }
            }

            for ($memory = 0; $memory < sizeof($arrayValues); $memory++) {
                $value = $arrayValues[$memory];
                if (!preg_match('/(\s)/', $value) && strlen($value) == 1) {
                    for ($i = 0; $i < sizeof($header['Date']); $i++) {
                        array_push($header['amount'], $value);
                        $memory++;
                    }
                    break;
                }
            }

            for ($memory = 0; $memory < sizeof($arrayValues); $memory++) {

                if (!preg_match('/(\s)/', $arrayValues[$memory])) {
                    for ($i = 0; $i < sizeof($header['Date']); $i++) {
                        array_push($header['amountProcedure'], $arrayValues[$memory]);
                        $memory++;
                    }
                    break;
                }
            }

            for ($memory = 0; $memory < sizeof($arrayValues); $memory++) {

                if (!preg_match('/(\s)/', $arrayValues[$memory])) {
                    for ($i = 0; $i < sizeof($header['Date']); $i++) {
                        array_push($header['amountReleased'], $arrayValues[$memory]);
                        $memory++;
                    }
                    break;
                }
            }

            for ($memory = 0; $memory < sizeof($arrayValues); $memory++) {

                if (!preg_match('/(\s)/', $arrayValues[$memory])) {
                    for ($i = 0; $i < sizeof($header['Date']); $i++) {
                        array_push($header['glossValue'], $arrayValues[$memory]);
                        $memory++;
                    }
                    break;
                }
            }

            for ($i = 0; $i < sizeof($arrayOfPages); $i++) {

                if (preg_match('/(Executada)(.*)/', $arrayOfPages[$i], $matches)) {

                    for ($i = 0; $i < sizeof($arrayOfPages); $i++) {
                        if (strlen($arrayOfPages[$i]) > 100) {
                            $position = $i + 3;
                            // Verifique se $position está dentro dos limites
                            if ($position >= sizeof($arrayOfPages) || empty($header['Date'])) {
                                break; // Interrompa o loop para evitar ir além do tamanho do array ou se header['Date'] estiver vazio
                            }
                            // Loop para enviar glossCode para $header['glossCode']
                            for ($j = 0; $j < sizeof($header['Date']); $j++) {
                                if ($position >= sizeof($arrayOfPages)) {
                                    break; //Quebre o loop para evitar ir além do tamanho do array
                                }
                                array_push($header['glossCode'], trim($arrayOfPages[$position]));
                                    $position++;
                                }
                                preg_match('/(34 \- Valor Informado da Guia \(R\$\))(.*)(35 \- Valor Processado da Guia \(R\$\))/', $arrayOfPages[$i], $matches);
                                if ($matches) {
                                    if ($i + 2 >= sizeof($arrayOfPages)) {
                                        break; //Quebre o loop para evitar ir além do tamanho do array
                                    }
                                    $value = $arrayOfPages[$i + 2];
                                    $valueInfo = preg_split('/\s/', $value);

                                    $values = preg_split('/\s/', $matches[2]);
                                    $guideTotalValue['34 - Valor Informado da Guia (R$)'] = substr($valueInfo[1], 0, 8);
                                    $guideTotalValue['35 - Valor Processado da Guia (R$)'] = $values[1];
                                    $guideTotalValue['36 - Valor Liberado da Guia (R$)'] = $values[2];
                                    $guideTotalValue['37 - Valor Glosa da Guia (R$)'] = $values[3];
                                }
                        } else {
                            // Verifique se a posição está dentro dos limites
                            $position = sizeof($arrayOfPages) - sizeof($header['Date']);
                            if ($position >= sizeof($arrayOfPages) || empty($header['Date'])) {
                                break;
                            }

                            for ($j = 0; $j < sizeof($header['Date']); $j++) {
                            if ($position >= sizeof($arrayOfPages)) {
                                break;
                            }
                            array_push($header['glossCode'], trim($arrayOfPages[$position]));
                            $position++;
                        }

                        // Defina os valores em $guideTotalValue para strings vazias
                        $guideTotalValue['34 - Valor Informado da Guia (R$)'] = "";
                        $guideTotalValue['35 - Valor Processado da Guia (R$)'] = "";
                        $guideTotalValue['36 - Valor Liberado da Guia (R$)'] = "";
                        $guideTotalValue['37 - Valor Glosa da Guia (R$)'] = "";
                    }
                }
            }
            }

            $lineOfEachInformationProcedure = array();
            for ($i = 0; $i < sizeof($header['Date']); $i++) {

                $lineOfEachInformationProcedure['23 - Data de realizacao'] = $header['Date'][$i];
                if(isset($header['table'][$i])) {
                    $lineOfEachInformationProcedure['24 - Tabela'] = $header['table'][$i];
                } else {
                    $lineOfEachInformationProcedure['24 - Tabela'] = "";
                }
                $lineOfEachInformationProcedure['25 - Codigo Procedimento'] = $header['procedure'][$i];

                if(isset($header['description'][$i])) {
                    $lineOfEachInformationProcedure['26 - Descrição'] = $header['description'][$i];
                } else {
                    $lineOfEachInformationProcedure['26 - Descrição'] = null;
                }

                $lineOfEachInformationProcedure['28 - Valor Informado'] = isset($header['value'][$i]) ? $header['value'][$i] : "";
                $lineOfEachInformationProcedure['29 - Quantidade'] = isset($header['amount'][$i]) ? $header['amount'][$i] : "";
                $lineOfEachInformationProcedure['30 - Valor Processado'] = isset($header['amountProcedure'][$i]) ? $header['amountProcedure'][$i] : "";
                $lineOfEachInformationProcedure['31 - Valor Liberado'] = isset($header['amountReleased'][$i]) ? $header['amountReleased'][$i] : "";
                $lineOfEachInformationProcedure['32 - Valor Glosa'] = isset($header['glossValue'][$i]) ? $header['glossValue'][$i] : "";
                $lineOfEachInformationProcedure['33 - Codigo da Glosa'] = $header['glossCode'][$i];

                array_push($filteredData,
                    array_merge(
                        $lineOfEachInformation,
                        $lineOfEachInformationProcedure,
                        $guideTotalValue
                    )
                );
            }
            //dd($filteredData);
            return $filteredData;

        } catch (Exception $error) {
            Log::error($error->getMessage());
            Log::error($error->getTraceAsString());
            return $error->getMessage();
        }

    }
}
