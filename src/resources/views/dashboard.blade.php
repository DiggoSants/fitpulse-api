<x-app-layout>

<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<h2 class="text-2xl font-bold mb-6">
Seu Treino
</h2>

@if($exercises->isEmpty())

<p>Nenhum treino encontrado.</p>

@else

<table class="w-full border">

<thead>
<tr>
<th>Exercício</th>
<th>Séries</th>
<th>Repetições</th>
<th>Descanso</th>
</tr>
</thead>

<tbody>

@foreach($exercises as $exercise)

<tr>
<td>{{ $exercise->exercise->name }}</td>
<td>{{ $exercise->sets }}</td>
<td>{{ $exercise->reps }}</td>
<td>{{ $exercise->rest }}s</td>
</tr>

@endforeach

</tbody>

</table>

@endif

</div>
</div>

</x-app-layout>