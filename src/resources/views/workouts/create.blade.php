<h1>Criar Treino</h1>

<form action="/workout/store" method="POST">

    @csrf

    <label>Aluno</label>

    <select name="student_id">

        @foreach($students as $student)

        <option value="{{ $student->id }}">
            Aluno {{ $student->id }}
        </option>

        @endforeach

    </select>

    <br><br>

    <input type="text" name="name" placeholder="Nome do treino">

    <br><br>

    <h3>Exercícios</h3>

    @foreach($exercises as $index => $exercise)
    <div style="margin-bottom:10px;">

        <input type="checkbox" name="exercise_id[{{ $index }}]" value="{{ $exercise->id }}">

        <strong>{{ $exercise->name }}</strong>

        <input type="number" name="sets[{{ $index }}]" placeholder="Séries" required>
        <input type="number" name="reps[{{ $index }}]" placeholder="Reps" required>
        <input type="number" name="rest_time[{{ $index }}]" placeholder="Descanso">

    </div>
    @endforeach

    <button type="submit">Salvar treino</button>

</form>