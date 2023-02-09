<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DatePeriod;
use DateInterval;

class RelatorioController extends Controller
{
    public function index()
    {
        $totais = DB::table('totais')->get();
        return view('index', compact('totais'));
    }

    public function personalizado(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $cidades = DB::table('populacao')->select('nome_municipio')->orderBy('nome_municipio')->get();
        $estados = DB::table('populacao')->select('uf')->groupBy('uf')->get();
        $regioes = DB::table('populacao')->select('regiao')->groupBy('regiao')->get();
        $empresas = DB::table('dw_dunax')->select('Empresa')->where('situacao', '<>', 'Cancelado')->groupBy('Empresa')->get();
        $ibge_cidades = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck('IBGECidade');
        $ibge_estados = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck('IBGEEstado');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        $pop_cidade = DB::table('populacao')->pluck('populacao_estimada');
        //$pop_estado = DB::table('populacao')->where('situacao', '<>', 'Cancelado')->sum('populacao_estimada')->where('uf', '=', '');
        //$pop_regiao = DB::table('populacao')->where('situacao', '<>', 'Cancelado')->pluck();
        //$total_venda_lub_cidade = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$total_venda_lub_estado = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$total_venda_lub_regiao = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //dd($empresas);

        $regN = DB::table('populacao')->select('uf')->where('regiao', '=', 'N')->groupBy('uf')->get();
        $regNE = DB::table('populacao')->select('uf')->where('regiao', '=', 'NE')->groupBy('uf')->get();
        $regCO = DB::table('populacao')->select('uf')->where('regiao', '=', 'CO')->groupBy('uf')->get();
        $regSD = DB::table('populacao')->select('uf')->where('regiao', '=', 'SD')->groupBy('uf')->get();
        $regS = DB::table('populacao')->select('uf')->where('regiao', '=', 'S')->groupBy('uf')->get();

        $data = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('dw_dunax.Data, populacao.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes')
            ->where('dw_dunax.situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', ['2022-01-01', '2022-01-31'])
            ->groupBy('dw_dunax.Cidade')
            ->orderByRaw('dw_dunax.Data, dw_dunax.Cidade')
            ->get();

        //$data = DB::table('dw_dunax')->selectRaw("b.regiao, a.Estado, a.IBGEEstado, a.IBGECidade, a.Cidade, count(a.Cliente) as Clientes, sum(a.Quantidade * a.Volumes) as TotalVendido as a inner join populacao as b on a.IBGECidade = b.cod_municipio where a.Situacao <> 'Cancelado' and a.Data between '2022-01-01' and '2022-01-31' group by a.Cidade order by b.regiao, a.Cidade;")->get();
        //dd($data);


        return view('filtrar', compact('regN', 'regNE', 'regCO', 'regSD', 'regS', 'estados', 'cidades', 'regioes', 'empresas', 'total_litros_vendidos', 'data'));
    }

    public function filtrar(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');
        $dataI_minus1 = date_format(date_add(date_create($dataI), date_interval_create_from_date_string("1 day - 2 days")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI_minus1), $interval, new DateTime($dataF_plus1));
        $totais = 0;

        /* foreach($period as $p){
            //print_r($p->format("Y-m") . "<br>");
            $totais = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalMes')->whereRaw('Data regexp "'. $p->format("Y-m") .'"')->get();
            //print_r($totais);
        } */
        
        $periodo = $request->input('periodo');
        
        $data = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('populacao.regiao, dw_dunax.Estado, if(populacao.nome_municipio not regexp dw_dunax.Cidade, populacao.nome_municipio, dw_Cidade) as Cidade, if(populacao.nome_municipio not regexp dw_dunax.Cidade, 0, count(distinct dw_dunax.Cliente)) as Clientes')
            ->where('dw_dunax.Situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        $cidades = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cidade) as Cidades')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                and dw_dunax.Objeto not regexp "Arla" 
                                                and dw_dunax.Objeto not regexp "Freio" 
                                                and dw_dunax.Objeto not regexp "Aditivo"
                                                and dw_dunax.Cliente not regexp "DULUB"
                                                and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->value('Cidades');

        $clientes = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                and dw_dunax.Objeto not regexp "Arla" 
                                                and dw_dunax.Objeto not regexp "Freio" 
                                                and dw_dunax.Objeto not regexp "Aditivo"
                                                and dw_dunax.Cliente not regexp "DULUB"
                                                and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->value('Clientes');

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();
        //print_r($total_vendido->TotalVendido);

        return view('filtro_result', compact('data', 'dataI', 'dataF', 'period', 'cidades', 'clientes', 'total_vendido'));
    }

    public function filtrarRegiao(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $regiao = $request->input('regiao');
        $mes    = $request->input('mes');

        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');
        $dataI_minus1 = date_format(date_add(date_create($dataI), date_interval_create_from_date_string("1 day - 2 days")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI_minus1), $interval, new DateTime($dataF_plus1));

        $estados    = DB::table('populacao')->select('uf')->where('regiao', '=', $regiao)->get();
        $pop_regiao = DB::table('populacao')->where('regiao', '=', $regiao)->sum('populacao_estimada');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_estado')->where('situacao', '<>', 'Cancelado')->first();
        $total_venda_lub_regiao = DB::table('dw_dunax')->join('populacao', 'dw_dunax.Estado', '=', 'populacao.uf')->selectRaw('sum(Quantidade * Volumes) as total_regiao')->where('populacao.regiao', '=', $regiao)->first(); //->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao as b on a.Estado = b.uf')->where('b.regiao', '=', $regiao)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //print_r($total_venda_lub_regiao);

        $data = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('populacao.regiao, populacao.uf, if(populacao.nome_municipio not regexp dw_dunax.Cidade, populacao.nome_municipio, dw_Cidade) as Cidade, if(populacao.nome_municipio not regexp dw_dunax.Cidade, 0, count(distinct dw_dunax.Cliente)) as Clientes')
            ->where('dw_dunax.situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->where('populacao.regiao', '=', $regiao)
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        $cidades = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cidade) as Cidades')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                            and populacao.regiao = "' . $regiao . '"
                                            and dw_dunax.Objeto not regexp "Arla" 
                                            and dw_dunax.Objeto not regexp "Freio" 
                                            and dw_dunax.Objeto not regexp "Aditivo"
                                            and dw_dunax.Cliente not regexp "DULUB"
                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" 
                                            and dw_dunax.Data regexp "2022-12"')->value('Cidades');

        $clientes = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                            and populacao.regiao = "' . $regiao . '"
                                            and dw_dunax.Objeto not regexp "Arla" 
                                            and dw_dunax.Objeto not regexp "Freio" 
                                            and dw_dunax.Objeto not regexp "Aditivo"
                                            and dw_dunax.Cliente not regexp "DULUB"
                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" 
                                            and dw_dunax.Data regexp "2022-12"')->value('Clientes');

        //$total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();


        return view('filtro_result_regiao', compact('estados', 'dataI', 'dataF', 'cidades', 'clientes', 'period', 'regiao', 'mes',  'data', 'pop_regiao', 'total_litros_vendidos', 'total_venda_lub_regiao'));
    }

    public function filtrarEstado(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $estado = $request->input('estado');
        $mes    = $request->input('mes');

        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');
        $dataI_minus1 = date_format(date_add(date_create($dataI), date_interval_create_from_date_string("1 day - 2 days")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI_minus1), $interval, new DateTime($dataF_plus1));

        $regiao     = DB::table('populacao')->select('regiao')->where('uf', '=', $estado)->first();
        $cidades    = DB::table('populacao')->select('nome_municipio')->where('uf', '=', $estado)->get();
        $pop_cidade = DB::table('populacao')->select('populacao_estimada')->where('uf', '=', $estado)->get();
        $pop_estado = DB::table('populacao')->where('uf', '=', $estado)->sum('populacao_estimada');
        //$pop_regiao = DB::table('populacao')->where('regiao', '=', $regiao)->sum('populacao_estimada');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        $total_venda_lub_cidade = 0; //DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_cidade')->where('Cidade', '=', $cidades)->first();
        $total_venda_lub_estado = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_estado')->where('Estado', '=', $estado)->first();
        $total_venda_lub_regiao = 0; //DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao as b on a.Estado = b.uf')->where('Estado', '=', $estado)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();


        /* $data = DB::table('dw_dunax')
        ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
        ->selectRaw('dw_dunax.Data, populacao.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes')
        ->where('dw_dunax.situacao', '<>', 'Cancelado')
        ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo"')
        ->where('dw_dunax.Estado', '=', $estado)
        ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
        ->groupBy('dw_dunax.Cidade')
        ->orderByRaw('dw_dunax.Data, dw_dunax.Cidade')
        ->get(); */

        $periodo = $request->input('periodo');
        $periodo_minus2 = date('Y-m-d', strtotime($periodo . ' -2 months'));
        $periodo_tratado = date('Y-m-d', strtotime($periodo));
        
        //dd($periodo);

        $data = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('populacao.regiao, dw_dunax.Estado, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, populacao.populacao_estimada')
            ->where('Estado', '=', $estado)
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "2022-10"')
            //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        //dd($estado);

        $cidades = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGEEstado', '=', 'populacao.cod_uf')
            ->selectRaw('count(distinct dw_dunax.Cidade) as Cidades')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                and populacao.uf = "' . $estado . '"
                                                and dw_dunax.Objeto not regexp "Arla" 
                                                and dw_dunax.Objeto not regexp "Freio" 
                                                and dw_dunax.Objeto not regexp "Aditivo"
                                                and dw_dunax.Cliente not regexp "DULUB"
                                                and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
            //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->value('Cidades');

        $cidadesEstado = DB::table('populacao')->selectRaw('count(nome_municipio) as TotalCidades')->where('uf', '=', $estado)->value('TotalCidades');

        $clientes = DB::table('dw_dunax')
            ->selectRaw('count(distinct Cliente) as Clientes')
            ->whereRaw('Situacao <> "Cancelado"
                                                and Estado = "' . $estado . '"
                                                and Objeto not regexp "Arla" 
                                                and Objeto not regexp "Freio" 
                                                and Objeto not regexp "Aditivo"
                                                and dw_dunax.Cliente not regexp "DULUB"
                                                and dw_dunax.Cliente not regexp "DUNAX"
                                                and dw_dunax.TipoDeOperacao not regexp "Devol"
                                                and dw_dunax.Data regexp "' . $periodo . '"')
            //->whereBetween('Data', [$dataI, $dataF_plus1])
            ->value('Clientes');
        
        $clientes_ativos = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                    and dw_dunax.Empresa = "' . $empresa . '"
                                                    and dw_dunax.Objeto not regexp "Arla" 
                                                    and dw_dunax.Objeto not regexp "Freio" 
                                                    and dw_dunax.Objeto not regexp "Aditivo"
                                                    and dw_dunax.Cliente not regexp "DULUB"
                                                    and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_tratado])
            ->value('Clientes');

        $populacao = DB::table('populacao')->select('populacao_estimada')->where('uf', '=', $estado)->get();

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')
                                                ->whereRaw('Situacao <> "Cancelado"
                                                    and Estado = "' . $estado . '"
                                                    and Objeto not regexp "Arla" 
                                                    and Objeto not regexp "Freio" 
                                                    and Objeto not regexp "Aditivo"
                                                    and dw_dunax.Cliente not regexp "DULUB"
                                                    and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"
                                                    and dw_dunax.Data regexp ' . $periodo)
                                                ->value('TotalVendido');


        return view('filtro_result_estado', compact('total_vendido', 'populacao', 'dataI', 'dataF', 'dataI_minus1', 'dataF_plus1', 'estado', 'cidades', 'cidadesEstado', 'clientes', 'period', 'regiao', 'mes', 'periodo',  'data',  'pop_cidade', 'pop_estado', 'total_litros_vendidos', 'total_venda_lub_cidade', 'total_venda_lub_estado', 'total_venda_lub_regiao'));
    }

    public function filtrarEmpresa(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $empresa = $request->input('empresa');

        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');
        $dataI_minus1 = date_format(date_add(date_create($dataI), date_interval_create_from_date_string("1 day - 2 days")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI_minus1), $interval, new DateTime($dataF_plus1));
        //$mes    = $request->input('mes');

        //$estados    = DB::table('populacao')->select('uf')->where('nome_municipio', '=', $cidade)->get();
        //$regiao     = DB::table('populacao')->select('regiao')->where('nome_municipio', '=', $cidade)->first();
        //$pop_cidade = DB::table('populacao')->select('populacao_estimada')->where('nome_municipio', '=', $cidade)->first();
        //$total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        //$total_venda_lub_cidade = DB::table('dw_dunax')->join('populacao', 'dw_dunax.Cidade', '=', 'populacao.nome_municipio')->selectRaw('sum(Quantidade * Volumes) as total_cidade')->where('dw_dunax.cidade', '=', $cidade)->first(); //->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao as b on a.Estado = b.uf')->where('b.regiao', '=', $cidade)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //print_r($regiao);

        $periodo = $request->input('periodo');
        //$periodo_minus1 = date('Y-m', strtotime($periodo . ' -1 months'));
        $periodo_minus2 = date('Y-m-d', strtotime($periodo . ' -2 months'));
        $periodo_tratado = date('Y-m-d', strtotime($periodo));
        //dd($periodo_minus2);

        $data = DB::table('dw_dunax')
        ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
        ->selectRaw('populacao.regiao, dw_dunax.Estado, dw_dunax.Empresa, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, populacao.populacao_estimada')
        ->where('Empresa', '=', $empresa)
        ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
        //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
        ->groupBy('dw_dunax.Cidade')
        ->orderBy('Clientes', 'desc')
        ->get();

        /* $data = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('dw_dunax.Data, populacao.regiao, dw_dunax.Estado, if(populacao.nome_municipio not regexp dw_dunax.Cidade, populacao.nome_municipio, dw_dunax.Cidade) as Cidade, if(populacao.nome_municipio not regexp dw_dunax.Cidade, 0, count(distinct dw_dunax.Cliente)) as Clientes')
            ->where('dw_dunax.situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->where('dw_dunax.Empresa', '=', $empresa)
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get(); */


        $cidades = DB::table('dw_dunax')
            ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cidade) as Cidades')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                    and dw_dunax.Empresa = "' . $empresa . '"
                                                    and dw_dunax.Objeto not regexp "Arla" 
                                                    and dw_dunax.Objeto not regexp "Freio" 
                                                    and dw_dunax.Objeto not regexp "Aditivo"
                                                    and dw_dunax.Cliente not regexp "DULUB"
                                                    and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->value('Cidades');

            $clientes = DB::table('dw_dunax')
                ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
                ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
                ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                        and dw_dunax.Empresa = "' . $empresa . '"
                                                        and dw_dunax.Objeto not regexp "Arla" 
                                                        and dw_dunax.Objeto not regexp "Freio" 
                                                        and dw_dunax.Objeto not regexp "Aditivo"
                                                        and dw_dunax.Cliente not regexp "DULUB"
                                                        and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
                ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
                ->value('Clientes');

            $clientes_ativos = DB::table('dw_dunax')
                    ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
                    ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
                    ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                            and dw_dunax.Empresa = "' . $empresa . '"
                                                            and dw_dunax.Objeto not regexp "Arla" 
                                                            and dw_dunax.Objeto not regexp "Freio" 
                                                            and dw_dunax.Objeto not regexp "Aditivo"
                                                            and dw_dunax.Cliente not regexp "DULUB"
                                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
                    ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_tratado])
                    ->value('Clientes');
            //dd($clientes_ativos);

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();


        return view('filtro_result_empresa', compact('empresa', 'periodo', 'cidades', 'clientes', 'dataI', 'dataF', 'data', 'period', 'total_vendido'));
    }
}
