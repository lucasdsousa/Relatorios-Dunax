@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <a href="/Personalizado" class="btn btn-primary mb-5">Relatório Personalizado</a>

</div>

<div class="container">
    <h3><strong>Relatório Geral</strong></h3>
    <br>
    <table class="table table-hover">
    <thead>
        <tr>
            <th scope="col">Cidade</th>
            <th scope="col">Região</th>
            <th scope="col">Estado</th>
            <th scope="col">Clientes Jan 2022</th>
            <th scope="col">Clientes Fev 2022</th>
            <th scope="col">Clientes Mar 2022</th>
            <th scope="col">Clientes Abr 2022</th>
            <th scope="col">Clientes Mai 2022</th>
            <th scope="col">Clientes Jun 2022</th>
            <th scope="col">Clientes Jul 2022</th>
            <th scope="col">Clientes Ago 2022</th>
            <th scope="col">Clientes Set 2022</th>
            <th scope="col">Clientes Out 2022</th>
            <th scope="col">Clientes Nov 2022</th>
            <th scope="col">Clientes Dez 2022</th>
            <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($totais as $t)
            <tr>
                <th scope="row">{{ $t->Cidade }}</th>
                <td>{{ $t->Regiao }}</td>
                <td>{{ $t->Estado }}</td>
                <td>{{ $t->jan2022 }}</td>
                <td>{{ $t->fev2022 }}</td>
                <td>{{ $t->mar2022 }}</td>
                <td>{{ $t->abr2022 }}</td>
                <td>{{ $t->mai2022 }}</td>
                <td>{{ $t->jun2022 }}</td>
                <td>{{ $t->jul2022 }}</td>
                <td>{{ $t->ago2022 }}</td>
                <td>{{ $t->set2022 }}</td>
                <td>{{ $t->out2022 }}</td>
                <td>{{ $t->nov2022 }}</td>
                <td>{{ $t->dez2022 }}</td>
                <td>{{ $t->total }}</td>
            </tr>
        @endforeach
    </tbody>
    </table>
</div>


@endsection