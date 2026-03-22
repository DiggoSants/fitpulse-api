<h1>Criar Treino</h1>

<form action="/workout/store" method="POST">

    @csrf

    <label>Aluno</label>

    <br><br>

    <input type="text" name="name" placeholder="Nome do treino">

    <br><br>

    <h3>Exercícios</h3>

    @foreach($exercises as $exercise)
    <div style="margin-bottom:10px; border-bottom:1px solid #ccc; padding:10px;">

        <label>
            <input type="checkbox" name="exercise_id[]" value="{{ $exercise->id }}">
            <strong>{{ $exercise->name }}</strong>
        </label>

        <br>

        <input type="number" name="sets[{{ $exercise->id }}]" placeholder="Séries">
        <input type="number" name="reps[{{ $exercise->id }}]" placeholder="Reps">
        <input type="number" name="rest_time[{{ $exercise->id }}]" placeholder="Descanso">

    </div>
    @endforeach

    <button type="submit">Salvar treino</button>

</form>