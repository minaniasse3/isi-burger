@extends('layouts.app')

@section('title', $burger->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $burger->name }}</h1>
        <div>
            <a href="{{ route('burgers.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <a href="{{ route('burgers.edit', $burger) }}" class="btn btn-warning text-white">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($burger->image)
                        <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center rounded mb-3" style="height: 300px;">
                            <i class="fas fa-hamburger fa-5x text-white"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Informations du burger</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Prix :</th>
                                <td>{{ number_format($burger->price, 2) }} F CFA</td>
                            </tr>
                            <tr>
                                <th>Stock :</th>
                                <td>{{ $burger->stock }}</td>
                            </tr>
                            <tr>
                                <th>Disponibilité :</th>
                                <td>
                                    @if($burger->isInStock())
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-danger">Indisponible</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Statut :</th>
                                <td>
                                    @if($burger->is_archived)
                                        <span class="badge bg-secondary">Archivé</span>
                                    @else
                                        <span class="badge bg-primary">Actif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description :</th>
                                <td>{{ $burger->description ?: 'Aucune description disponible' }}</td>
                            </tr>
                            <tr>
                                <th>Créé le :</th>
                                <td>{{ $burger->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Dernière mise à jour :</th>
                                <td>{{ $burger->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <div class="d-flex justify-content-between">
                    @if($burger->is_archived)
                        <form action="{{ route('burgers.restore', $burger) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-undo me-1"></i> Restaurer
                            </button>
                        </form>
                    @else
                        <form action="{{ route('burgers.archive', $burger) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary" onclick="return confirm('Êtes-vous sûr de vouloir archiver ce burger?')">
                                <i class="fas fa-archive me-1"></i> Archiver
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('burgers.destroy', $burger) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce burger?')">
                            <i class="fas fa-trash me-1"></i> Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 