<x-app-layout>

    <h1>Matrícula</h1>

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

        {{-- Código do instrutor --}}
        <label>Código do Instrutor</label>
        <input type="text" name="invite_code" value="{{ old('invite_code') }}"
            placeholder="Ex: A3BX92KL" maxlength="8"
            style="text-transform:uppercase;">
        @error('invite_code')
            <span>{{ $message }}</span>
        @enderror

        {{-- Escolha do plano --}}
        <label>Plano</label>
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

        @error('plan_id')
            <span>{{ $message }}</span>
        @enderror

        @if($plans->count())
            <button type="submit">Confirmar Matrícula</button>
        @endif

    </form>

</x-app-layout>