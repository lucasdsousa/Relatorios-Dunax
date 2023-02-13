@extends('layouts.app')

@section('content')


<p></br></p>
<!--<div class="row container mt-5 mr-auto sticky-top">
  <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>-->
  <div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>
  

<style>
  table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;    
  }
  
  th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: center;
    font-size: 14px;
  }
  
  th {
    border: 1px solid black;
    background-color: black;
    font-weight: bold;
    position: sticky;
    top: 0;
  }
  
  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
  
  h3, h2, h1 {
    text-align: center;
    font-family: Arial, sans-serif;
  }
  
  h1 {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 30px;
  }
  
  h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
  }
  
  h3 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
  }

</style>

<div class="container">
  <h1>Relatório Geral</h1>
  <h2>Mês de {{$mes}}</h2>
  <h3>Dados consolidados por Empresa, Estado e Cidade</h3>
  <br>
  <table class="table table-hover">
  
  </table>
  </div>
  <table class="table">
    
          <thead class='table-dark'>
            <tr>
            <th scope="col">Periodo</th>
            <th scope="col">Empresa</th>
            <th scope="col">Estado</th>
            <th scope="col">Cidade</th>
            <th scope="col">Clientes Faturados</th>
            <th scope="col">Clientes Ativos</th>
            <th scope="col">Total Vendido</th>
            <th scope="col">Meta Clientes por Cidade</th><!-- aqui é calculado "Meta Clientes por Cidade" calculando "Meta LT por Cidade" dividido por "Meta Cliente por Estado" -->
            <th scope="col">Meta Clientes por Estado</th>
            <th scope="col">% População Por Estado</th><!-- aqui é calculado "porcentagem de população da cidade por estado" calculando população por cidade dividido por população por estado vezes 100 -->
            <th scope="col">Meta LT por Cidade</th><!-- aqui é calculado  "Meta de Lt por Estado" multiplicado por "% população por estado", isto dividido por 100 (atenção ao arredondamento gerado pelas tabelas) -->
            <th scope="col">Meta LT por Estado</th>
            </tr>
        </thead>
        <tbody>

                @foreach($data as $d)
                    <tr>
                    <td>{{$mes}}</td>
                    <td>{{$d->Empresa}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{$clientes_ativos}}</td>
                    <td>{{$d->TotalVendido}} Litros</td>
                    <td>280</td>
                    <td>280</td>
                    <td>{{$d->perc_estado_2022}}</td>

                    @if($mes == "01/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('jan_23')->where('estado', '=', $d->Estado)->value('jan_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "02/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('fev_23')->where('estado', '=', $d->Estado)->value('fev_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "03/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('mar_23')->where('estado', '=', $d->Estado)->value('mar_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "04/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('abr_23')->where('estado', '=', $d->Estado)->value('abr_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "05/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('mai_23')->where('estado', '=', $d->Estado)->value('mai_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "06/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('jun_23')->where('estado', '=', $d->Estado)->value('jun_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "07/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('jul_23')->where('estado', '=', $d->Estado)->value('jul_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "08/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('ago_23')->where('estado', '=', $d->Estado)->value('ago_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "09/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('set_23')->where('estado', '=', $d->Estado)->value('set_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "10/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('out_23')->where('estado', '=', $d->Estado)->value('out_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "11/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('nov_23')->where('estado', '=', $d->Estado)->value('nov_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @elseif($mes == "12/2023")
                        @php
                            $meta_estado_2023 = DB::table('metas_2023')->select('dez_23')->where('estado', '=', $d->Estado)->value('dez_23');
                            $meta_lt_cidade = ($meta_estado_2023 * $d->perc_estado_2022) / 100;
                        @endphp
                        <td>{{ $meta_lt_cidade }} Litros</td>
                        <td>{{$meta_estado_2023}} Litros</td>
                    @else
                        <td>0 Litros</td>
                        <td>0 Litros</td>
                    @endif
                  </tr>
                @endforeach
        </tbody>
       <table style="background-color: lightgray;">
 
    </table>
</div>



<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>
            <h3>    Dunax </h3>
        </div>

@endsection