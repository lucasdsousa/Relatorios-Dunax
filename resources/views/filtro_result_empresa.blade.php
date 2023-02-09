@extends('layouts.app')

@section('content')

<div class="row container mt-5">
    <a href="/" class="btn btn-outline-danger mb-2 col-md-2">Voltar</a>
</div>



<div>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">Periodo</th>
            <th scope="col">Regiao</th>
            <th scope="col">Estado</th>
            <th scope="col">Cidade</th>
            <th scope="col">Clientes</th>
            <th scope="col">Total Vendido</th>
            <th scope="col">Empresa</th>
        </thead>
        <tbody>
                @foreach($data as $d)
            <tr>
                    <td>{{$periodo}}</td>
                    <td>{{$d->regiao}}</td>
                    <td>{{$d->Estado}}</td>
                    <td>{{$d->Cidade}}</td>
                    <td>{{$d->Clientes}}</td>
                    <td>{{number_format($d->TotalVendido, 2, ',', '.')}} L</td>
                    <td>{{$d->Empresa}}</td>
                @endforeach
        </tbody>
    </table>
</div>

@endsection