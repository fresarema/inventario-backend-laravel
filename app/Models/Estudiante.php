<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'estudiantes';

    protected $fillable = ['run','nombres','apellidos','f_nac','carrera_id','foto','estado'];
	
}
