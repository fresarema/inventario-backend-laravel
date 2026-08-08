@section('title', __('Estudiantes'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Estudiante Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Estudiantes">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Estudiantes
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.estudiantes.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Run</th>
								<th>Nombres</th>
								<th>Apellidos</th>
								<th>F Nac</th>
								<th>Carrera Id</th>
								<th>Foto</th>
								<th>Estado</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($estudiantes as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->run }}</td>
								<td>{{ $row->nombres }}</td>
								<td>{{ $row->apellidos }}</td>
								<td>{{ $row->f_nac }}</td>
								<td>{{ $row->carrera_id }}</td>
								<td>{{ $row->foto }}</td>
								<td>{{ $row->estado }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id}})"><i class="bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Estudiante id {{$row->id}}? \nDeleted Estudiantes cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"><i class="bi-trash3-fill"></i> Delete </a></li>  
										</ul>
									</div>								
								</td>
							</tr>
							@empty
							<tr>
								<td class="text-center" colspan="100%">No data Found </td>
							</tr>
							@endforelse
						</tbody>
					</table>						
					<div class="float-end">{{ $estudiantes->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>