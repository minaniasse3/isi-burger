@extends('layouts.app')

@section('title', 'Liste des Burgers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Liste des Burgers</h1>
        <a href="{{ route('burgers.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un burger
        </a>
    </div>

    @if($burgers->isEmpty())
        <div class="alert alert-info">
            Aucun burger disponible pour le moment.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Disponibilité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($burgers as $burger)
                        <tr>
                            <td>
                                @if($burger->image)
                                    <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" class="burger-img rounded">
                                @else
                                    <div class="burger-img bg-secondary d-flex align-items-center justify-content-center rounded">
                                        <i class="fas fa-hamburger fa-2x text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $burger->name }}</td>
                            <td>{{ number_format($burger->price, 2) }} F CFA</td>
                            <td>
                                <form action="{{ route('burgers.updateStock', $burger) }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="stock" value="{{ $burger->stock }}" min="0" class="form-control form-control-sm me-2" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                @if($burger->isInStock())
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Indisponible</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('burgers.show', $burger) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('burgers.edit', $burger) }}" class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('burgers.archive', $burger) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Êtes-vous sûr de vouloir archiver ce burger?')">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('burgers.destroy', $burger) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce burger?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection 