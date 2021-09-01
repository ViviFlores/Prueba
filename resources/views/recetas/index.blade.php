@extends('layouts.app')

@section('botones')
    @include('ui.navegacion')
@endsection

@section('content')
<h2 class="text-center mb-3">Administra tus recetas</h2>
<div class="col-md-10 mx-auto bg-white p-3">
    <table class="table">
        <thead class="bg-primary text-light">
            <tr>
                <th scole="col">Nombre</th>
                <th scole="col">Categoría</th>
                <th scole="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userRecetas as $userReceta)
            <tr>
                <td>{{$userReceta->nombre}}</td>
                <td>{{$userReceta->categoriaReceta->nombre}}</td>
                <td>
                    <a href="{{route('recetas.show', ['receta'=>$userReceta->id])}}" class="btn btn-success d-block">Ver</a>
                    <a href="{{route('recetas.edit', ['receta'=>$userReceta->id])}}" class="btn btn-dark d-block mt-1">Editar</a>
                    <eliminar-receta receta-id={{$userReceta->id}}></eliminar-receta>

                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@endsection
