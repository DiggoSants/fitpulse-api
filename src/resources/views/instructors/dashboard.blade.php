<x-app-layout>

    <h1>Dashboard do Instrutor</h1>

    @if(Auth::user()->isManager())
        @forelse($instructors as $instructor)

            <h2>{{ $instructor->user->name }} — {{ $instructor->specialty ?? 'Sem especialidade' }}</h2>

            @forelse($instructor->students as $student)

                <h3>Aluno: {{ $student->user->name }}</h3>
                <p>Email: {{ $student->user->email }}</p>
                <p>Devedor: {{ $student->is_defaulter ? 'Sim' : 'Não' }}</p>

                <h4>Treinos</h4>
                @forelse($student->workouts as $workout)

                    <p><strong>{{ $workout->name }}</strong></p>

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
    
        <h2>Meus Alunos</h2>

        @forelse($instructor->students as $student)

            <h3>{{ $student->user->name }}</h3>
            <p>Email: {{ $student->user->email }}</p>
            <p>Devedor: {{ $student->is_defaulter ? 'Sim' : 'Não' }}</p>

            <h4>Treinos</h4>
            @forelse($student->workouts as $workout)

                <p><strong>{{ $workout->name }}</strong></p>

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