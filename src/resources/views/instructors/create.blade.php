<x-app-layout>

    <a href="{{ route('instructors.index') }}">← Voltar</a>

    <h1>Novo Instrutor</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('instructors.store') }}" method="POST">
        @csrf

        <label>Usuário</label>
        <select name="user_id">
            <option value="">Selecione um usuário</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} — {{ $user->email }}
                </option>
            @endforeach
        </select>

        <label>Especialidade</label>
        <input type="text" name="specialty" value="{{ old('specialty') }}" placeholder="Ex: Musculação, Crossfit...">

        <button type="submit">Salvar</button>
        <a href="{{ route('instructors.index') }}">Cancelar</a>
    </form>

</x-app-layout>