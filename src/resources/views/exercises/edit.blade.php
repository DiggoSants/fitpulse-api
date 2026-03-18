<h1>Editar Exercício</h1>

<form action="/exercises/{{ $exercise->id }}" method="POST">

@csrf
@method('PUT')

<input type="text" name="name" value="{{ $exercise->name }}">

<br><br>

<input type="text" name="muscle_group" value="{{ $exercise->muscle_group }}">

<br><br>

<textarea name="description">{{ $exercise->description }}</textarea>

<br><br>

<button type="submit">Atualizar</button>

</form>