@extends('layouts.app')

@section('title', 'Burgers Archivés')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Burgers Archivés</h1>
        <a href="{{ route('burgers.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Retour aux burgers actifs
        </a>
    </div>

    @if($burgers->isEmpty())
        <div class="alert alert-info">
            Aucun burger archivé pour le moment.
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
                            <td>{{ $burger->stock }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('burgers.show', $burger) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('burgers.restore', $burger) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-undo"></i>
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