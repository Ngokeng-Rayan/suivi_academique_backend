@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">Gestion des Filières</h2>

    <!-- Bouton d'ajout -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAjout">
        Ajouter une Filière
    </button>

    <!-- Tableau listing -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Label</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($filieres as $filiere)
            <tr>
                <td>{{ $filiere->code_filiere }}</td>
                <td>{{ $filiere->label_filiere }}</td>
                <td>{{ $filiere->desc_filiere }}</td>
                <td>
                    <!-- Modifier -->
                    <button
                        class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEdit{{ $filiere->code_filiere }}">
                        Modifier
                    </button>

                    <!-- Supprimer -->
                    <button
                        class="btn btn-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDelete{{ $filiere->code_filiere }}">
                        Supprimer
                    </button>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit{{ $filiere->code_filiere }}">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('filiere.update', $filiere->code_filiere) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Modifier la Filière</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <label>Code</label>
                                <input type="text" class="form-control" name="code_filiere"
                                       value="{{ $filiere->code_filiere }}" readonly>

                                <label class="mt-3">Label</label>
                                <input type="text" class="form-control" name="label_filiere"
                                       value="{{ $filiere->label_filiere }}" required>

                                <label class="mt-3">Description</label>
                                <textarea class="form-control" name="desc_filiere" required>{{ $filiere->desc_filiere }}</textarea>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button class="btn btn-primary">Mettre à jour</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Delete -->
            <div class="modal fade" id="modalDelete{{ $filiere->code_filiere }}">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('filiere.destroy', $filiere->code_filiere) }}">
                        @csrf
                        @method('DELETE')

                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Confirmer la suppression</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                Voulez-vous vraiment supprimer la filière :
                                <strong>{{ $filiere->label_filiere }}</strong> ?
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button class="btn btn-danger">Supprimer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Ajout -->
<div class="modal fade" id="modalAjout">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('filiere.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une Filière</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Code Filière</label>
                    <input type="text" class="form-control" name="code_filiere" required>

                    <label class="mt-3">Label</label>
                    <input type="text" class="form-control" name="label_filiere" required>

                    <label class="mt-3">Description</label>
                    <textarea class="form-control" name="desc_filiere" required></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

