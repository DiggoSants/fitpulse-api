<x-app-layout>

    <h1>Escolha seu Plano</h1>

    @if(session('info'))
        <p>{{ session('info') }}</p>
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('enrollment.store') }}" method="POST">
        @csrf

        @forelse($plans as $plan)
        <div>
            <input type="radio" name="plan_id" value="{{ $plan->id }}" id="plan_{{ $plan->id }}"
                {{ old('plan_id') == $plan->id ? 'checked' : '' }}>
            <label for="plan_{{ $plan->id }}">
                {{ $plan->name }} —
                R$ {{ number_format($plan->price, 2, ',', '.') }} /
                {{ $plan->duration_days }} dias
            </label>
        </div>
        @empty
            <p>Nenhum plano disponível no momento.</p>
        @endforelse

        @if($plans->count())
            <button type="submit">Confirmar Matrícula</button>
        @endif

    </form>

</x-app-layout>