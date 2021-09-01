<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except'=>'show']);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function show(Perfil $perfil)
    {
        return view('perfiles.show')->with('perfil', $perfil);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function edit(Perfil $perfil)
    {
        //policy
        $this->authorize('view', $perfil);
        return view('perfiles.edit')->with('perfil', $perfil);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Perfil  $perfil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perfil $perfil)
    {

        //policy
        $this->authorize('update', $perfil);

        //dd($request['imagen']);
        //Validar
        $data = request()->validate([
            'nombre' => 'required',
            'url' => 'required',
            'biografia' => 'required'
        ]);

        //Verificar si el usuario sube una imagen
        if ($request['imagen']) {
            $ruta_imagen = $request['imagen']->store('upload-perfiles', 'public');
            $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(600, 600);
            $img->save();

            //arreglo de imagenes
            $array_image = ['imagen' => $ruta_imagen];
        }

        // dd($data);    verificamos la información enviada desde el html
        //Actualizar tabla users
        auth()->user()->name = $data['nombre'];
        auth()->user()->url = $data['url'];
        auth()->user()->save();

        //elimnar url y name de data
        unset($data['nombre']);
        unset($data['url']);

        //Guardar información tabla perfils
        auth()->user()->userPerfil()->update(
            array_merge( //dos parámetros de tipo array(arreglo)
                $data,
                $array_image ?? []  //almacenando la imagen
            )

        );

        //Redireccionar
        return redirect()->action([RecetaController::class, 'index']);
    }

}
