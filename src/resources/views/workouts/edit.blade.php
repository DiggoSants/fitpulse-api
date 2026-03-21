<h2>Editar Treino</h2>

<form action="{{ route('workout.update', $workout->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Aluno:</label>
    <select name="student_id">
        @foreach($students as $student)
            <option value="{{ $student->id }}"
                {{ $student->id == $workout->student_id ? 'selected' : '' }}>
                Aluno {{ $student->id }}
            </option>
        @endforeach
    </select>

    <br><br>

    <label>Nome do treino:</label>
    <input type="text" name="name" value="{{ $workout->name }}">

    <br><br>

    <h3>Exercícios</h3>

    @foreach($exercises as $index => $exercise)
        @php
            $we = $workout->workoutExercises->firstWhere('exercise_id', $exercise->id);
        @endphp

        <div style="margin-bottom:10px;">

            <input type="checkbox" name="exercise_id[{{ $index }}]" value="{{ $exercise->id }}"
                {{ $we ? 'checked' : '' }}>

            <strong>{{ $exercise->name }}</strong>

            <input type="number" name="sets[{{ $index }}]"
                value="{{ $we->sets ?? '' }}" placeholder="Séries">

            <input type="number" name="reps[{{ $index }}]"
                value="{{ $we->reps ?? '' }}" placeholder="Reps">

            <input type="number" name="rest_time[{{ $index }}]"
                value="{{ $we->rest_time ?? '' }}" placeholder="Descanso">

        </div>
    @endforeach

    <button type="submit">Atualizar</button>
</form>