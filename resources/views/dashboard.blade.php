<x-app-layout>
    <x-slot name="header">
        <h2 style="font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:3px; color:#fff; margin:0;">
            DASHBOARD
        </h2>
    </x-slot>

    <style>
        body { background: #0a0a0a !important; }
    </style>

    <div style="min-height: calc(100vh - 120px); background: #0a0a0a; padding: 40px 24px; font-family: 'Montserrat', sans-serif;">
        <div style="max-width: 1150px; margin: 0 auto;">
            <div style="
                background: rgba(255,255,255,0.03);
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 18px;
                padding: 28px 32px;
                color: rgba(255,255,255,0.85);
                font-size: 15px;
                font-weight: 500;
            ">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>

    <h2>Seu treino</h2>

<table>
<thead>
<tr>
<th>Exercício</th>
<th>Séries</th>
<th>Reps</th>
<th>Carga</th>
<th>Descanso</th>
</tr>
</thead>

<tbody>
@foreach($exercises as $item)
<tr>
<td>{{ $item->exercise->name }}</td>
<td>{{ $item->sets }}</td>
<td>{{ $item->reps }}</td>
<td>{{ $item->load_kg }} kg</td>
<td>{{ $item->rest_seconds }} s</td>
</tr>
@endforeach
</tbody>
</table>
</x-app-layout>