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

        $cidades = DB::table('populacao_att')->select('nome_municipio')->orderBy('nome_municipio')->get();
        $estados = DB::table('populacao_att')->select('uf')->groupBy('uf')->get();
        $regioes = DB::table('populacao_att')->select('regiao')->groupBy('regiao')->get();
        $empresas = DB::table('dw_dunax')->select('Empresa')->where('situacao', '<>', 'Cancelado')->groupBy('Empresa')->get();
        $ibge_cidades = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck('IBGECidade');
        $ibge_estados = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck('IBGEEstado');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        $pop_cidade = DB::table('populacao_att')->pluck('populacao_att.pop_cidade_2022');
        //$pop_estado = DB::table('populacao_att')->where('situacao', '<>', 'Cancelado')->sum('populacao.pop_cidade_2022')->where('uf', '=', '');
        //$pop_regiao = DB::table('populacao_att')->where('situacao', '<>', 'Cancelado')->pluck();
        //$total_venda_lub_cidade = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$total_venda_lub_estado = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$total_venda_lub_regiao = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //dd($empresas);

        $regN = DB::table('populacao_att')->select('uf')->where('regiao', '=', 'N')->groupBy('uf')->get();
        $regNE = DB::table('populacao_att')->select('uf')->where('regiao', '=', 'NE')->groupBy('uf')->get();
        $regCO = DB::table('populacao_att')->select('uf')->where('regiao', '=', 'CO')->groupBy('uf')->get();
        $regSD = DB::table('populacao_att')->select('uf')->where('regiao', '=', 'SD')->groupBy('uf')->get();
        $regS = DB::table('populacao_att')->select('uf')->where('regiao', '=', 'S')->groupBy('uf')->get();

        $data = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('dw_dunax.Data, populacao_att.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes')
            ->where('dw_dunax.situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', ['2022-01-01', '2022-01-31'])
            ->groupBy('dw_dunax.Cidade')
            ->orderByRaw('dw_dunax.Data, dw_dunax.Cidade')
            ->get();

        //$data = DB::table('dw_dunax')->selectRaw("b.regiao, a.Estado, a.IBGEEstado, a.IBGECidade, a.Cidade, count(a.Cliente) as Clientes, sum(a.Quantidade * a.Volumes) as TotalVendido as a inner join populacao_att as b on a.IBGECidade = b.cod_municipio where a.Situacao <> 'Cancelado' and a.Data between '2022-01-01' and '2022-01-31' group by a.Cidade order by b.regiao, a.Cidade;")->get();
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
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('populacao_att.regiao, dw_dunax.Estado, if(populacao_att.nome_municipio not regexp dw_dunax.Cidade, populacao_att.nome_municipio, dw_Cidade) as Cidade, if(populacao_att.nome_municipio not regexp dw_dunax.Cidade, 0, count(distinct dw_dunax.Cliente)) as Clientes')
            ->where('dw_dunax.Situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        $cidades = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
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
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
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

        $estados    = DB::table('populacao_att')->select('uf')->where('regiao', '=', $regiao)->get();
        $pop_regiao = DB::table('populacao_att')->where('regiao', '=', $regiao)->sum('populacao.pop_cidade_2022');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_estado')->where('situacao', '<>', 'Cancelado')->first();
        $total_venda_lub_regiao = DB::table('dw_dunax')->join('populacao_att', 'dw_dunax.Estado', '=', 'populacao_att.uf')->selectRaw('sum(Quantidade * Volumes) as total_regiao')->where('populacao_att.regiao', '=', $regiao)->first(); //->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao_att as b on a.Estado = b.uf')->where('b.regiao', '=', $regiao)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //print_r($total_venda_lub_regiao);

        $data = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('populacao_att.regiao, populacao_att.uf, if(populacao_att.nome_municipio not regexp dw_dunax.Cidade, populacao_att.nome_municipio, dw_Cidade) as Cidade, if(populacao_att.nome_municipio not regexp dw_dunax.Cidade, 0, count(distinct dw_dunax.Cliente)) as Clientes')
            ->where('dw_dunax.situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->where('populacao_att.regiao', '=', $regiao)
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        $cidades = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cidade) as Cidades')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                            and populacao_att.regiao = "' . $regiao . '"
                                            and dw_dunax.Objeto not regexp "Arla" 
                                            and dw_dunax.Objeto not regexp "Freio" 
                                            and dw_dunax.Objeto not regexp "Aditivo"
                                            and dw_dunax.Cliente not regexp "DULUB"
                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" 
                                            and dw_dunax.Data regexp "2022-12"')->value('Cidades');

        $clientes = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                            and populacao_att.regiao = "' . $regiao . '"
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

        $regiao     = DB::table('populacao_att')->select('regiao')->where('uf', '=', $estado)->first();
        $cidades    = DB::table('populacao_att')->select('nome_municipio')->where('uf', '=', $estado)->get();
        $pop_cidade = DB::table('populacao_att')->select('populacao.pop_cidade_2022')->where('uf', '=', $estado)->get();
        $pop_estado = DB::table('populacao_att')->where('uf', '=', $estado)->sum('populacao.pop_cidade_2022');
        //$pop_regiao = DB::table('populacao_att')->where('regiao', '=', $regiao)->sum('populacao.pop_cidade_2022');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        $total_venda_lub_cidade = 0; //DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_cidade')->where('Cidade', '=', $cidades)->first();
        $total_venda_lub_estado = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_estado')->where('Estado', '=', $estado)->first();
        $total_venda_lub_regiao = 0; //DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao_att as b on a.Estado = b.uf')->where('Estado', '=', $estado)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();


        /* $data = DB::table('dw_dunax')
        ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
        ->selectRaw('dw_dunax.Data, populacao_att.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes')
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
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('populacao_att.regiao, dw_dunax.Estado, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido')
            ->where('Estado', '=', $estado)
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "2022-10"')
            //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        //dd($estado);

        $cidades = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGEEstado', '=', 'populacao_att.cod_uf')
            ->selectRaw('count(distinct dw_dunax.Cidade) as Cidades')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                and populacao_att.uf = "' . $estado . '"
                                                and dw_dunax.Objeto not regexp "Arla" 
                                                and dw_dunax.Objeto not regexp "Freio" 
                                                and dw_dunax.Objeto not regexp "Aditivo"
                                                and dw_dunax.Cliente not regexp "DULUB"
                                                and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
            //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->value('Cidades');

        $cidadesEstado = DB::table('populacao_att')->selectRaw('count(nome_municipio) as TotalCidades')->where('uf', '=', $estado)->value('TotalCidades');

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
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                    and dw_dunax.Empresa = "' . $estado . '"
                                                    and dw_dunax.Objeto not regexp "Arla" 
                                                    and dw_dunax.Objeto not regexp "Freio" 
                                                    and dw_dunax.Objeto not regexp "Aditivo"
                                                    and dw_dunax.Cliente not regexp "DULUB"
                                                    and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_tratado])
            ->value('Clientes');

        $populacao_att = DB::table('populacao_att')->select('populacao.pop_cidade_2022')->where('uf', '=', $estado)->get();

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


        return view('filtro_result_estado', compact('total_vendido', 'populacao_att', 'dataI', 'dataF', 'dataI_minus1', 'dataF_plus1', 'estado', 'cidades', 'cidadesEstado', 'clientes', 'period', 'regiao', 'mes', 'periodo',  'data',  'pop_cidade', 'pop_estado', 'total_litros_vendidos', 'total_venda_lub_cidade', 'total_venda_lub_estado', 'total_venda_lub_regiao'));
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

        //$estados    = DB::table('populacao_att')->select('uf')->where('nome_municipio', '=', $cidade)->get();
        //$regiao     = DB::table('populacao_att')->select('regiao')->where('nome_municipio', '=', $cidade)->first();
        //$pop_cidade = DB::table('populacao_att')->select('populacao.pop_cidade_2022')->where('nome_municipio', '=', $cidade)->first();
        //$total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        //$total_venda_lub_cidade = DB::table('dw_dunax')->join('populacao_att', 'dw_dunax.Cidade', '=', 'populacao_att.nome_municipio')->selectRaw('sum(Quantidade * Volumes) as total_cidade')->where('dw_dunax.cidade', '=', $cidade)->first(); //->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao_att as b on a.Estado = b.uf')->where('b.regiao', '=', $cidade)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //print_r($regiao);

        $periodo = $request->input('periodo');
        //$periodo_minus1 = date('Y-m', strtotime($periodo . ' -1 months'));
        $periodo_minus2 = date('Y-m-d', strtotime($periodo . ' -2 months'));
        $periodo_date = date('Y-m-d', strtotime($periodo));
        //dd($periodo_minus2);

        $data = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('populacao_att.regiao, dw_dunax.Estado, dw_dunax.Empresa, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido')
            ->where('Empresa', '=', $empresa)
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
            //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get();

        /* $data = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('dw_dunax.Data, populacao_att.regiao, dw_dunax.Estado, if(populacao_att.nome_municipio not regexp dw_dunax.Cidade, populacao_att.nome_municipio, dw_dunax.Cidade) as Cidade, if(populacao_att.nome_municipio not regexp dw_dunax.Cidade, 0, count(distinct dw_dunax.Cliente)) as Clientes')
            ->where('dw_dunax.situacao', '<>', 'Cancelado')
            ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->where('dw_dunax.Empresa', '=', $empresa)
            ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
            ->groupBy('dw_dunax.Cidade')
            ->orderBy('Clientes', 'desc')
            ->get(); */


        $cidades = DB::table('dw_dunax')
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
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
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
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
            ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
            ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
            ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                            and dw_dunax.Empresa = "' . $empresa . '"
                                                            and dw_dunax.Objeto not regexp "Arla" 
                                                            and dw_dunax.Objeto not regexp "Freio" 
                                                            and dw_dunax.Objeto not regexp "Aditivo"
                                                            and dw_dunax.Cliente not regexp "DULUB"
                                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
            ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_date])
            ->groupBy('IBGECidade')
            ->value('Clientes');
        //dd($clientes_ativos);

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();


        return view('filtro_result_empresa', compact('empresa', 'periodo', 'cidades', 'clientes', 'clientes_ativos', 'dataI', 'dataF', 'data', 'period', 'total_vendido'));
    }

    public function filtrar_de_vdd(Request $request)
    {
        $empresa = $request->input('empresa');
        $estado = $request->input('estado');

        //$periodo_minus1 = date('Y-m', strtotime($periodo . ' -1 months'));
        $periodo = $request->input('periodo');
        $periodo_minus2 = date('Y-m-d', strtotime($periodo . ' -2 months'));
        $periodo_date = date('Y-m-d', strtotime($periodo));

        $mes = date('m/Y', strtotime($periodo));

        if ($estado == 0 && $empresa != 0) {

            $data = DB::table('dw_dunax')
                ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
                ->selectRaw('populacao_att.regiao, dw_dunax.Estado, dw_dunax.Empresa, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, populacao_att.perc_estado_2022')
                ->where('Empresa', '=', $empresa)
                ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
                //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
                ->groupBy('dw_dunax.Cidade')
                ->orderBy('Clientes', 'desc')
                ->get();

            $porc_pop_estado = 0; //$pop_cidade / ($pop_estado * 100);

            $pop = DB::table('populacao_att')->get();
            $pop_cidade = DB::table('populacao_att')->select('pop_cidade_2022')->groupBy('cod_municipio')->value('pop_cidade_2022');
            $pop_estado = DB::table('populacao_att')->select('pop_estado_2022')->groupBy('cod_uf')->value('pop_estado_2022');


            $clientes_ativos = DB::table('dw_dunax')
                ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
                ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
                ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                            and dw_dunax.Empresa = "' . $empresa . '"
                                                            and dw_dunax.Objeto not regexp "Arla" 
                                                            and dw_dunax.Objeto not regexp "Freio" 
                                                            and dw_dunax.Objeto not regexp "Aditivo"
                                                            and dw_dunax.Cliente not regexp "DULUB"
                                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
                ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_date])
                ->groupBy('IBGECidade')
                ->value('Clientes');

            return view('filtro_result_empresa', compact('periodo', 'pop', 'pop_cidade', 'pop_estado', 'empresa', 'estado', 'mes', 'data', 'clientes_ativos'));
        } else if ($empresa == 0 && $estado != 0) {

            $data = DB::table('dw_dunax')
                ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
                ->selectRaw('populacao_att.regiao, dw_dunax.Estado, dw_dunax.Empresa, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, populacao_att.perc_estado_2022')
                ->where('Estado', '=', $estado)
                ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
                //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
                ->groupBy('dw_dunax.Cidade')
                ->orderBy('Clientes', 'desc')
                ->get();

            $jan_2023 = DB::table('metas_2023')->select('jan_23')->where('estado', '=', $estado)->value('jan_23');
            $fev_2023 = DB::table('metas_2023')->select('fev_23')->where('estado', '=', $estado)->value('fev_23');
            $mar_2023 = DB::table('metas_2023')->select('mar_23')->where('estado', '=', $estado)->value('mar_23');
            $abr_2023 = DB::table('metas_2023')->select('abr_23')->where('estado', '=', $estado)->value('abr_23');
            $mai_2023 = DB::table('metas_2023')->select('mai_23')->where('estado', '=', $estado)->value('mai_23');
            $jun_2023 = DB::table('metas_2023')->select('jun_23')->where('estado', '=', $estado)->value('jun_23');
            $jul_2023 = DB::table('metas_2023')->select('jul_23')->where('estado', '=', $estado)->value('jul_23');
            $ago_2023 = DB::table('metas_2023')->select('ago_23')->where('estado', '=', $estado)->value('ago_23');
            $set_2023 = DB::table('metas_2023')->select('set_23')->where('estado', '=', $estado)->value('set_23');
            $out_2023 = DB::table('metas_2023')->select('jan_23')->where('estado', '=', $estado)->value('out_23');
            $nov_2023 = DB::table('metas_2023')->select('nov_23')->where('estado', '=', $estado)->value('nov_23');
            $dez_2023 = DB::table('metas_2023')->select('dez_23')->where('estado', '=', $estado)->value('dez_23');

            //dd($periodo);

            $clientes_ativos = DB::table('dw_dunax')
                ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
                ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
                ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                            and dw_dunax.Estado = "' . $estado . '"
                                                            and dw_dunax.Objeto not regexp "Arla" 
                                                            and dw_dunax.Objeto not regexp "Freio" 
                                                            and dw_dunax.Objeto not regexp "Aditivo"
                                                            and dw_dunax.Cliente not regexp "DULUB"
                                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
                ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_date])
                ->groupBy('IBGECidade')
                ->value('Clientes');

            /* if(substr($periodo, 0, 4) == "2023") {
                return view('filtro_result_estado', compact('jan_2023', 'empresa', 'estado', 'mes', 'data', 'clientes_ativos'));
            } */
            //dd(substr($periodo, 0, 4));
            return view('filtro_result_estado', compact('periodo', 'jan_2023', 'empresa', 'estado', 'mes', 'data', 'clientes_ativos'));
        } else if ($empresa != 0 && $estado != 0) {

            $data = DB::table('dw_dunax')
                ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
                ->selectRaw('populacao_att.regiao, dw_dunax.Estado, dw_dunax.Empresa, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, populacao_att.perc_estado_2022')
                ->where('Estado', '=', $estado)
                ->where('Empresa', '=', $empresa)
                ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo" and dw_dunax.Situacao <> "Cancelado" and dw_dunax.Cliente not regexp "DULUB" and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol" and dw_dunax.Data regexp "' . $periodo . '"')
                //->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
                ->groupBy('dw_dunax.Cidade')
                ->orderBy('Clientes', 'desc')
                ->get();

            $jan_2023 = DB::table('metas_2023')->select('jan_23')->where('estado', '=', $estado)->get();
            $fev_2023 = DB::table('metas_2023')->select('fev_23')->where('estado', '=', $estado)->get();
            $mar_2023 = DB::table('metas_2023')->select('mar_23')->where('estado', '=', $estado)->get();
            $abr_2023 = DB::table('metas_2023')->select('abr_23')->where('estado', '=', $estado)->get();
            $mai_2023 = DB::table('metas_2023')->select('mai_23')->where('estado', '=', $estado)->get();
            $jun_2023 = DB::table('metas_2023')->select('jun_23')->where('estado', '=', $estado)->get();
            $jul_2023 = DB::table('metas_2023')->select('jul_23')->where('estado', '=', $estado)->get();
            $ago_2023 = DB::table('metas_2023')->select('ago_23')->where('estado', '=', $estado)->get();
            $set_2023 = DB::table('metas_2023')->select('set_23')->where('estado', '=', $estado)->get();
            $out_2023 = DB::table('metas_2023')->select('jan_23')->where('estado', '=', $estado)->get();
            $nov_2023 = DB::table('metas_2023')->select('nov_23')->where('estado', '=', $estado)->get();
            $dez_2023 = DB::table('metas_2023')->select('dez_23')->where('estado', '=', $estado)->get();


            $clientes_ativos = DB::table('dw_dunax')
                ->join('populacao_att', 'dw_dunax.IBGECidade', '=', 'populacao_att.cod_municipio')
                ->selectRaw('count(distinct dw_dunax.Cliente) as Clientes')
                ->whereRaw('dw_dunax.Situacao <> "Cancelado"
                                                            and dw_dunax.Estado = "' . $estado . '"
                                                            and dw_dunax.Empresa = "' . $empresa . '"
                                                            and dw_dunax.Objeto not regexp "Arla" 
                                                            and dw_dunax.Objeto not regexp "Freio" 
                                                            and dw_dunax.Objeto not regexp "Aditivo"
                                                            and dw_dunax.Cliente not regexp "DULUB"
                                                            and dw_dunax.Cliente not regexp "DUNAX" and dw_dunax.TipoDeOperacao not regexp "Devol"')
                ->whereBetween('dw_dunax.Data', [$periodo_minus2, $periodo_date])
                ->groupBy('IBGECidade')
                ->value('Clientes');

            return view('filtro_result', compact('jan_2023', 'periodo', 'empresa', 'estado', 'mes', 'data', 'clientes_ativos'));
        } else {
            return view('index');
        }
    }
}
