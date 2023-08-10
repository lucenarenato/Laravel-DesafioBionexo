<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use App\Jobs\StorePdfDocumentAsText;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;


class PdfCommand extends Command
{
    protected $signature = 'PdfCommand:register';

    protected $description = 'Gravar pdf no banco';

     /**
     * Header of output data.
     *
     * @var string[]
     */
    protected $header = [
        'Registro ANS',
        'Nome da Operadora,',
        'Código na Operadora',
        'Nome do Contratado',
        'Número do Lote',
        'Número do Protocolo',
        'Data do Protocolo',
        'Código  da Glosa do Protocolo',
        'Número da Guia no Prestador',
        'Número da Guia Atribuído pela Operadora',
        'Senha',
        'Nome do Beneficiário',
        'Número da Carteira',
        'Data Inicio do faturamento',
        'Hora Inicio do Faturamento',
        'Data Fim do Faturamento',
        'Código da Glosa da Guia',
        'Data de realização',
        'Tabela',
        'Código do Procedimento',
        'Descrição',
        'Grau Participação',
        'Valor Informado',
        'Quanti. Executada',
        'Valor Processado',
        'Valor Liberado',
        'Valor Glosa',
        'Código da Glosa',
        'Valor Informado da Guia',
        'Valor Processado da Guia',
        'Valor Liberado da Guia',
        'Valor Glosa da Guia',
        'Valor Informado do Protocolo',
        'Valor Processado do Protocolo',
        'Valor Liberado do Protocolo',
        'Valor Glosa do Protocolo',
        'Valor Informado Geral',
        'Valor Processado Geral',
        'Valor Liberado Geral e Valor Glosa Geral'
    ];

    /**
     * handle - Tem como objetivo executar o comando para o objetivo desse comando descrito no description
     */

    public function handle()
    {
        dispatch((new StorePdfDocumentAsText));
    }
}
