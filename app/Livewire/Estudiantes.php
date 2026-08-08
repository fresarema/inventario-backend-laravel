<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use Livewire\Attributes\Computed;

class Estudiantes extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $run, $nombres, $apellidos, $f_nac, $carrera_id, $foto, $estado;

    #[Computed]
	public function filteredEstudiantes()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return Estudiante::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('run', 'LIKE', $keyWord)
						->orWhere('nombres', 'LIKE', $keyWord)
						->orWhere('apellidos', 'LIKE', $keyWord)
						->orWhere('f_nac', 'LIKE', $keyWord)
						->orWhere('carrera_id', 'LIKE', $keyWord)
						->orWhere('foto', 'LIKE', $keyWord)
						->orWhere('estado', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
	{
		return view('livewire.estudiantes.view', [
			'estudiantes' => $this->filteredEstudiantes,
		]);
	}
	
    public function cancel()
    {
        $this->reset();
    }

    public function save()
    {
        $this->validate([
		'run' => 'required',
		'nombres' => 'required',
		'apellidos' => 'required',
		'f_nac' => 'required',
		'carrera_id' => 'required',
		'foto' => 'required',
		'estado' => 'required',
        ]);

        Flight::updateOrCreate(
			['id' => $this->selected_id],
			[
				'run' => $this-> run,
				'nombres' => $this-> nombres,
				'apellidos' => $this-> apellidos,
				'f_nac' => $this-> f_nac,
				'carrera_id' => $this-> carrera_id,
				'foto' => $this-> foto,
				'estado' => $this-> estado
			]
		);

        $message = $this->selected_id ? 'Estudiante Successfully updated.' : 'Estudiante Successfully created.';
		$this->dispatch('closeModal');
        $this->reset();
		session()->flash('message', $message);
    }

    public function edit($id)
    {
        $this->selected_id = $id;
		$this->fill(Estudiante::findOrFail($id)->toArray());
    }

    public function destroy($id)
    {
        if ($id) {
            Estudiante::where('id', $id)->delete();
        }
    }
}