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

@foreach($exercises as $exercise)

<div style="border:1px solid black;padding:10px;margin:10px">

<input type="checkbox" name="exercise_id[]" value="{{ $exercise->id }}">

{{ $exercise->name }}

<br>

Séries

<input type="number" name="sets[]">

Reps

<input type="number" name="reps[]">

Descanso

<input type="number" name="rest_time[]">

</div>

@endforeach

<button type="submit">Salvar treino</button>

</form>