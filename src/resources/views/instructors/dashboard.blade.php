<x-app-layout>

    <h1>Dashboard do Instrutor</h1>

    @if(session('success'))
    <p>{{ session('success') }}</p>
    @endif

    @if(Auth::user()->isManager())

    {{-- VISÃO DO GERENTE: todos os instrutores e seus alunos --}}
    @forelse($instructors as $instructor)

    <h2>{{ $instructor->user->name }} — {{ $instructor->specialty ?? 'Sem especialidade' }}</h2>
    <p>Código de convite: <strong>{{ $instructor->invite_code ?? 'Não gerado' }}</strong></p>

    <form action="{{ route('instructors.regenerate-code', $instructor->id) }}" method="POST">
        @csrf
        <button type="submit">Regenerar Código</button>
    </form>

    @forelse($instructor->students as $student)

    <h3>Aluno: {{ $student->user->name }}</h3>
    <p>Email: {{ $student->user->email }}</p>
    <p>Devedor: {{ $student->is_defaulter ? 'Sim' : 'Não' }}</p>

    <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}">
        + Criar treino para este aluno
    </a>

    <h4>Treinos</h4>
    @forelse($student->workouts as $workout)

    <p><strong>{{ $workout->name }}</strong></p>

    <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}">
        Editar treino
    </a>

    <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        <button type="submit">Deletar treino</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Exercício</th>
                <th>Grupo muscular</th>
                <th>Séries</th>
                <th>Reps</th>
                <th>Descanso (s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workout->workoutExercises as $we)
            <tr>
                <td>{{ $we->exercise->name }}</td>
                <td>{{ $we->exercise->muscle_group ?? '—' }}</td>
                <td>{{ $we->sets }}</td>
                <td>{{ $we->reps }}</td>
                <td>{{ $we->rest_time ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @empty
    <p>Nenhum treino cadastrado para este aluno.</p>
    @endforelse

    @empty
    <p>Nenhum aluno vinculado a este instrutor.</p>
    @endforelse

    @empty
    <p>Nenhum instrutor cadastrado.</p>
    @endforelse

    @else

    {{-- VISÃO DO INSTRUTOR: seus alunos + seu próprio código --}}
    <h2>Meu Código de Convite</h2>
    <p><strong>{{ $instructor->invite_code ?? 'Não gerado' }}</strong></p>
    <p>Passe este código para seus alunos na hora da matrícula.</p>

    <form action="{{ route('instructors.regenerate-code', $instructor->id) }}" method="POST">
        @csrf
        <button type="submit">Regenerar Código</button>
    </form>

    <h2>Meus Alunos</h2>

    @forelse($instructor->students as $student)

    <h3>{{ $student->user->name }}</h3>
    <p>Email: {{ $student->user->email }}</p>
    <p>Devedor: {{ $student->is_defaulter ? 'Sim' : 'Não' }}</p>

    <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}">
        + Criar treino para este aluno
    </a>

    <h4>Treinos</h4>
    @forelse($student->workouts as $workout)

    <p><strong>{{ $workout->name }}</strong></p>

    <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}">
        Editar treino
    </a>

    <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        <button type="submit">Deletar treino</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Exercício</th>
                <th>Grupo muscular</th>
                <th>Séries</th>
                <th>Reps</th>
                <th>Descanso (s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workout->workoutExercises as $we)
            <tr>
                <td>{{ $we->exercise->name }}</td>
                <td>{{ $we->exercise->muscle_group ?? '—' }}</td>
                <td>{{ $we->sets }}</td>
                <td>{{ $we->reps }}</td>
                <td>{{ $we->rest_time ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @empty
    <p>Nenhum treino cadastrado para este aluno.</p>
    @endforelse

    @empty
    <p>Nenhum aluno vinculado a você.</p>
    @endforelse

    @endif

</x-app-layout>