<!-- Modal -->
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Estudiante' : 'Create Estudiante' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    <div class="form-group">
                        <label for="run"></label>
                        <input wire:model.live="run" type="text" class="form-control" id="run" placeholder="Run">@error('run') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombres"></label>
                        <input wire:model.live="nombres" type="text" class="form-control" id="nombres" placeholder="Nombres">@error('nombres') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="apellidos"></label>
                        <input wire:model.live="apellidos" type="text" class="form-control" id="apellidos" placeholder="Apellidos">@error('apellidos') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="f_nac"></label>
                        <input wire:model.live="f_nac" type="text" class="form-control" id="f_nac" placeholder="F Nac">@error('f_nac') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="carrera_id"></label>
                        <input wire:model.live="carrera_id" type="text" class="form-control" id="carrera_id" placeholder="Carrera Id">@error('carrera_id') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="foto"></label>
                        <input wire:model.live="foto" type="text" class="form-control" id="foto" placeholder="Foto">@error('foto') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="estado"></label>
                        <input wire:model.live="estado" type="text" class="form-control" id="estado" placeholder="Estado">@error('estado') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">{{ $selected_id ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
</div>