<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use Illuminate\Http\Request;
use App\Models\CategoriaReceta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class RecetaController extends Controller
{

    //constructor
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$usuario=Auth::user();
        $userRecetas = Auth::user()->userRecetas;
        return view('recetas.index')->with('userRecetas', $userRecetas);
                                    //->with('usuario',$usuario);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //sin modelo
        //$categorias=DB::table('categorias_recetas')->get()->pluck('nombre','id');

        //con modelo
        $categorias = CategoriaReceta::all(['id', 'nombre']);
        return view('recetas.create')->with('categorias', $categorias);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'nombre' => 'required|min:6',
            'categoria' => 'required',
            'ingredientes' => 'required',
            'preparacion' => 'required',
            'imagen' => 'required|image',
        ]);

        $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');
        $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(1000, 550);
        $img->save();

        //insertar sin modelo
        /*DB::table('recetas')->insert([
            'nombre'=>$data['nombre'],
            'ingredientes'=>$data['ingredientes'],
            'preparacion'=>$data['preparacion'],
            'imagen'=>$ruta_imagen,
            'user_id'=>Auth::user()->id,
            'categoria_id'=>$data['categoria'],
        ]);*/

        //insertar con el modelo
        Auth::user()->userRecetas()->create([
            'nombre' => $data['nombre'],
            'ingredientes' => $data['ingredientes'],
            'preparacion' => $data['preparacion'],
            'imagen' => $ruta_imagen,
            'categoria_id' => $data['categoria'],
        ]);

        //Redireccionar
        return redirect()->action([RecetaController::class, 'index']);

        //dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function show(Receta $receta)
    {
        return view('recetas.show')->with('receta', $receta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function edit(Receta $receta)
    {
        //verificación del policy
        $this->authorize('view', $receta);
        $categorias = CategoriaReceta::all(['id', 'nombre']);
        return view('recetas.edit')->with('categorias', $categorias)
            ->with('receta', $receta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receta $receta)
    {

        //verificación del policy
        $this->authorize('update', $receta);
        $data = $request->validate([
            'nombre' => 'required|min:6',
            'categoria' => 'required',
            'ingredientes' => 'required',
            'preparacion' => 'required',
        ]);

        //Asignar valores
        $receta->nombre = $data['nombre'];
        $receta->categoria_id = $data['categoria'];
        $receta->ingredientes = $data['ingredientes'];
        $receta->preparacion = $data['preparacion'];

        //Nueva imagen
        if (request('imagen')) {
            //guardar la imagen en nuestro store
            $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');
            //despues aplicamos el estilo
            $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(1000, 550);
            $img->save();
            $receta->imagen=$ruta_imagen;
        }
        
        //guardar información
        $receta->save();

        //Redireccionar
        return redirect()->action([RecetaController::class, 'index']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receta $receta)
    {
        //verificación del policy
        $this->authorize('delete', $receta);
        //return "desde eliminar";

        //agregar metodo para eliminar
        $receta->delete();
        return redirect()->action([RecetaController::class, 'index']);
    }
}
