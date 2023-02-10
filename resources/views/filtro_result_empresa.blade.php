@extends('layouts.app')

@section('content')

<style>
  table {
    border-collapse: collapse;
  }
  
  th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: center;
  }
  
  th {
    background-color: lightgray;
  }
  
  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
</style>

<div class="container">
  <h3><strong>Relatório Geral</strong></h3>
  <br>
  <table class="table table-hover">
    <!-- O código HTML da tabela continua aqui -->
  </table>
</div>

<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>



<div>
    <table class="table">
        <thead>
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
            <th scope="col">% População por Cidade</th><!-- aqui é calculado "porcentagem de população da cidade por estado" calculando população por cidade dividido por população por estado vezes 100 -->
            <th scope="col">Meta LT por Cidade</th><!-- aqui é calculado "Meta LT por Cidade" calculando "Meta de Lt por Estado multiplicado" por "% população por estado" dividido por 100 (atenção ao arredondamento gerado pelas tabelas) -->
            <th scope="col">Meta LT por Estado</th>
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
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                    <td>280</td>
                    <td>280</td>
                    <td>{{$d->perc_cidade_2022}}</td>
                @endforeach
        </tbody>
    </table>
</div>

@endsection