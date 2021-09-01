<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $fillable = [
        'nombre',
        'ingredientes',
        'preparacion',
        'imagen',
        'categoria_id'
    ];

    //obtener la categoria mediante la clave foranea
    public function categoriaReceta(){
        //relación de uno a uno
        return $this->belongsTo(CategoriaReceta::class, 'categoria_id');
    }

    public function autorReceta(){
        //relación de uno a uno
        return $this->belongsTo(User::class, 'user_id');
    }

}
