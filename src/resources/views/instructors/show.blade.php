<x-app-layout>

    <a href="{{ route('dashboard') }}">← Voltar</a>

    <h1>{{ $instructor->user->name }}</h1>

    <p>Email: {{ $instructor->user->email }}</p>
    <p>Especialidade: {{ $instructor->specialty ?? '—' }}</p>
    <p>Cadastrado em: {{ $instructor->created_at->format('d/m/Y') }}</p>

    {{-- Código de convite --}}
    <p>Código de convite: <strong>{{ $instructor->invite_code ?? 'Não gerado' }}</strong></p>

    <form action="{{ route('instructors.regenerate-code', $instructor->id) }}" method="POST">
        @csrf
        <button type="submit">Regenerar Código</button>
    </form>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <a href="{{ route('instructors.edit', $instructor->id) }}">Editar</a>

    <form action="{{ route('instructors.destroy', $instructor->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Deletar</button>
    </form>

    <h2>Alunos vinculados</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Devedor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructor->students as $student)
            <tr>
                <td>{{ $student->id }}</td>
                <td>{{ $student->user->name }}</td>
                <td>{{ $student->user->email }}</td>
                <td>{{ $student->is_defaulter ? 'Sim' : 'Não' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">Nenhum aluno vinculado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</x-app-layout>
