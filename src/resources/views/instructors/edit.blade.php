<x-app-layout>

    <a href="{{ route('instructors.index') }}">← Voltar</a>

    <h1>Editar Instrutor</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('instructors.update', $instructor->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Usuário não pode ser trocado, apenas exibido --}}
        <label>Usuário</label>
        <input type="text" value="{{ $instructor->user->name }}" disabled>

        <label>Especialidade</label>
        <input type="text" name="specialty" value="{{ old('specialty', $instructor->specialty) }}" placeholder="Ex: Musculação, Crossfit...">

        <button type="submit">Atualizar</button>
        <a href="{{ route('instructors.index') }}">Cancelar</a>
    </form>

</x-app-layout>