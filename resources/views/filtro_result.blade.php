@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>

<div>
    <table class="table">
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
        </thead>
        <tbody>

                @foreach($data as $d)
            <tr>
            <td>{{$mes}}</td>
                    <td>{{$d->Empresa}}</td>
                    <td>{{$mes}}</td>
                    <td>{{$empresa}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{$clientes_ativos}}</td>
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                    <td>{{number_format($d->ClientesCidade, 2, ',', '.')}} L</td><!-- aqui é calculado "Meta Clientes por Cidade" calculando "Meta LT por Cidade" dividido por "Meta Cliente por Estado" -->
                    <td>280</td>
                    <td>{{$d->perc_cidade_2022}}</td><!-- aqui é calculado "Meta Clientes por Cidade" calculando "Meta LT por Cidade" dividido por "Meta Cliente por Estado" -->
                    <td>{{number_format($d->MetaLTCidade, 2, ',', '.')}} L</td><!-- aqui é calculado "Meta LT por Cidade" calculando "Meta de Lt por Estado multiplicado" por "% população por estado" dividido por 100 (atenção ao arredondamento gerado pelas tabelas) -->
                    <td>{{$jan_2023}}</td>
                    
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
                @endforeach
        </tbody>
    </table>
</div>

@endsection