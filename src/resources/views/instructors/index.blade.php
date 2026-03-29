<x-app-layout>

    <a href="{{ route('instructors.create') }}">Novo Instrutor</a>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Especialidade</th>
                <th>Alunos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructors as $instructor)
            <tr>
                <td>{{ $instructor->id }}</td>
                <td>{{ $instructor->user->name }}</td>
                <td>{{ $instructor->user->email }}</td>
                <td>{{ $instructor->specialty ?? '—' }}</td>
                <td>{{ $instructor->students->count() }}</td>
                <td>
                    <a href="{{ route('instructors.show', $instructor->id) }}">Ver</a>
                    <a href="{{ route('instructors.edit', $instructor->id) }}">Editar</a>
                    <form action="{{ route('instructors.destroy', $instructor->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Deletar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Nenhum instrutor cadastrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</x-app-layout>