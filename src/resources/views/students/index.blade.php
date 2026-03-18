<h1>Students</h1>

<a href="/students/create">Novo</a>

<table border="1">

<tr>
<th>ID</th>
<th>User</th>
<th>Instructor</th>
<th>RFID</th>
<th>Devedor</th>
<th>Ações</th>
</tr>

@foreach($students as $student)

<tr>

<td>{{ $student->id }}</td>
<td>{{ $student->user_id }}</td>
<td>{{ $student->instructor_id }}</td>
<td>{{ $student->rfid_tag }}</td>
<td>{{ $student->is_defaulter }}</td>

<td>

<a href="/students/{{ $student->id }}/edit">Editar</a>

<form action="/students/{{ $student->id }}" method="POST">
@csrf
@method('DELETE')
<button type="submit">Excluir</button>
</form>

</td>

</tr>

@endforeach

</table>