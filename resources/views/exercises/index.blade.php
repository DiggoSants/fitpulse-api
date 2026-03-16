<h1>Exercícios</h1>

<a href="/exercises/create">Novo Exercício</a>

<table border="1">
<tr>
    <th>Nome</th>
    <th>Grupo Muscular</th>
    <th>Ações</th>
</tr>

@foreach($exercises as $exercise)

<tr>
    <td>{{ $exercise->name }}</td>
    <td>{{ $exercise->muscle_group }}</td>

    <td>
        <a href="/exercises/{{ $exercise->id }}/edit">Editar</a>

        <form action="/exercises/{{ $exercise->id }}" method="POST">
            @csrf
            @method('DELETE')

            <button type="submit">Deletar</button>
        </form>
    </td>
</tr>

@endforeach

</table>