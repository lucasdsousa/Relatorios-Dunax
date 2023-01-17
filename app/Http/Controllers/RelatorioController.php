<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DatePeriod;
use DateInterval;

class RelatorioController extends Controller
{
    public function index(Request $request)
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
                    ->selectRaw('dw_dunax.Data, populacao.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, dw_dunax.Empresa')
                    ->where('dw_dunax.situacao', '<>', 'Cancelado')
                    ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo"')
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

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI), $interval, new DateTime($dataF));
        $totais = 0;

        /* foreach($period as $p){
            //print_r($p->format("Y-m") . "<br>");
            $totais = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalMes')->whereRaw('Data regexp "'. $p->format("Y-m") .'"')->get();
            //print_r($totais);
        } */

        $data = DB::table('dw_dunax')
                    ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
                    ->selectRaw('populacao.regiao, dw_dunax.Estado, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, dw_dunax.Empresa')
                    ->where('dw_dunax.situacao', '<>', 'Cancelado')
                    ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo"')
                    ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
                    ->groupBy('dw_dunax.Cidade')
                    ->get();

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();
        //print_r($total_vendido->TotalVendido);

        return view('filtro_result', compact('data', 'period', 'total_vendido'));
        
    }
    public function filtrarEstado(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $estado = $request->input('estado');
        $mes    = $request->input('mes');
        
        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI), $interval, new DateTime($dataF));

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
        ->selectRaw('dw_dunax.Data, populacao.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, dw_dunax.Empresa')
        ->where('dw_dunax.situacao', '<>', 'Cancelado')
        ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo"')
        ->where('dw_dunax.Estado', '=', $estado)
        ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
        ->groupBy('dw_dunax.Cidade')
        ->orderByRaw('dw_dunax.Data, dw_dunax.Cidade')
        ->get(); */

        $data = DB::table('dw_dunax')
                    ->selectRaw('Estado, Cidade, count(distinct Cliente) as Clientes, sum(Quantidade * Volumes) as TotalVendido')
                    ->where('Estado', '=', $estado)
                    ->whereRaw('Objeto not regexp "Arla" and Objeto not regexp "Freio" and Objeto not regexp "Aditivo" and Situacao <> "Cancelado"')
                    ->whereBetween('Data', [$dataI, $dataF_plus1])
                    ->groupBy('Cidade')
                    ->get();

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->where('Situacao', '<>', 'Cancelado')->first();


        return view('filtro_result_estado', compact('estado', 'period', 'regiao', 'mes',  'data', 'total_vendido',  'pop_cidade', 'pop_estado', 'total_litros_vendidos', 'total_venda_lub_cidade', 'total_venda_lub_estado', 'total_venda_lub_regiao'));
    }

    public function filtrarRegiao(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $regiao = $request->input('regiao');
        $mes    = $request->input('mes');
        
        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI), $interval, new DateTime($dataF));

        $estados    = DB::table('populacao')->select('uf')->where('regiao', '=', $regiao)->get();
        $pop_regiao = DB::table('populacao')->where('regiao', '=', $regiao)->sum('populacao_estimada');
        $total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total_estado')->where('situacao', '<>', 'Cancelado')->first();
        $total_venda_lub_regiao = DB::table('dw_dunax')->join('populacao', 'dw_dunax.Estado', '=', 'populacao.uf')->selectRaw('sum(Quantidade * Volumes) as total_regiao')->where('populacao.regiao', '=', $regiao)->first(); //->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao as b on a.Estado = b.uf')->where('b.regiao', '=', $regiao)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //print_r($total_venda_lub_regiao);

        $data = DB::table('dw_dunax')
        ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
        ->selectRaw('populacao.regiao, populacao.uf, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, dw_dunax.Empresa')
        ->where('dw_dunax.situacao', '<>', 'Cancelado')
        ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo"')
        ->where('populacao.regiao', '=', $regiao)
        ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
        ->groupBy('dw_dunax.Cidade')
        ->get();

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();


        return view('filtro_result_regiao', compact('estados', 'period', 'regiao', 'mes',  'data', 'total_vendido', 'pop_regiao', 'total_litros_vendidos', 'total_venda_lub_regiao'));
    }

    public function filtrarEmpresa(Request $request)
    {
        ini_set('memory_limit', '2056M');

        $empresa = $request->input('empresa');
        
        $dataI = $request->input('dataInicial');
        $dataF = $request->input('dataFinal');

        $dataF_plus1 = date_format(date_add(date_create($dataF), date_interval_create_from_date_string("1 day")), 'Y-m-d');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(new DateTime($dataI), $interval, new DateTime($dataF));
        //$mes    = $request->input('mes');

        //$estados    = DB::table('populacao')->select('uf')->where('nome_municipio', '=', $cidade)->get();
        //$regiao     = DB::table('populacao')->select('regiao')->where('nome_municipio', '=', $cidade)->first();
        //$pop_cidade = DB::table('populacao')->select('populacao_estimada')->where('nome_municipio', '=', $cidade)->first();
        //$total_litros_vendidos = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as total')->where('situacao', '<>', 'Cancelado')->first();
        //$total_venda_lub_cidade = DB::table('dw_dunax')->join('populacao', 'dw_dunax.Cidade', '=', 'populacao.nome_municipio')->selectRaw('sum(Quantidade * Volumes) as total_cidade')->where('dw_dunax.cidade', '=', $cidade)->first(); //->selectRaw('sum(Quantidade * Volumes) as total_regiao from dw_dunax as a join populacao as b on a.Estado = b.uf')->where('b.regiao', '=', $cidade)->first();
        //$consumo_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();
        //$venda_per_capita = DB::table('dw_dunax')->where('situacao', '<>', 'Cancelado')->pluck();

        //print_r($regiao);

        $data = DB::table('dw_dunax')
        ->join('populacao', 'dw_dunax.IBGECidade', '=', 'populacao.cod_municipio')
        ->selectRaw('dw_dunax.Data, populacao.regiao, dw_dunax.Estado, dw_dunax.IBGEEstado, dw_dunax.IBGECidade, dw_dunax.Cidade, count(distinct dw_dunax.Cliente) as Clientes, sum(dw_dunax.Quantidade * dw_dunax.Volumes) as TotalVendido, dw_dunax.Empresa')
        ->where('dw_dunax.situacao', '<>', 'Cancelado')
        ->whereRaw('dw_dunax.Objeto not regexp "Arla" and dw_dunax.Objeto not regexp "Freio" and dw_dunax.Objeto not regexp "Aditivo"')
        ->where('dw_dunax.Empresa', '=', $empresa)
        ->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])
        ->groupBy('dw_dunax.Cidade')
        ->orderByRaw('populacao.regiao, dw_dunax.Cidade')
        ->get();

        $total_vendido = DB::table('dw_dunax')->selectRaw('sum(Quantidade * Volumes) as TotalVendido')->whereBetween('dw_dunax.Data', [$dataI, $dataF_plus1])->first();


        return view('filtro_result_empresa', compact('data', 'period', 'total_vendido'));
    }
}
