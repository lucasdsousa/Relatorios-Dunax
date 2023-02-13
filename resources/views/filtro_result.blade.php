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
                    <td>null</td>
                    @if($mes == "01/2023")
                        <td>{{$jan_2023}} Litros</td>
                    @else
                        <td>0</td>
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