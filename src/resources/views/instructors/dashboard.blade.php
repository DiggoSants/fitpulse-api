<x-app-layout>
<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if(session('success'))
    <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    {{-- VISÃO DO GERENTE --}}
    @if(Auth::user()->isManager())

        <div class="dash-hero">
            <div class="dash-hero__ring"></div>
            <div class="dash-hero__inner">
                <div>
                    <div class="dash-hero__eyebrow">Gerenciamento</div>
                    <h2 class="dash-hero__title">Painel Geral</h2>
                    <p class="dash-hero__sub">Visão completa de instrutores e alunos</p>
                </div>
                <div class="dash-hero__right">
                    <span class="dash-hero__pulse">
                        <span class="dash-hero__pulse-dot"></span>
                        GERENTE
                    </span>
                    <a href="{{ route('instructors.create') }}" class="btn-save">
                        + Novo Instrutor
                    </a>
                </div>
            </div>
        </div>

        @forelse($instructors as $instructor)
        <div class="inst-section">

            <div class="inst-section__header">
                <div class="inst-avatar-sm">{{ strtoupper(substr($instructor->user->name, 0, 2)) }}</div>
                <h3 class="inst-section__name">{{ $instructor->user->name }}</h3>
                <span class="inst-section__count">{{ $instructor->students->count() }} aluno(s)</span>

                <span style="font-size:12px; opacity:.7;">
                    Código: <strong>{{ $instructor->invite_code ?? '—' }}</strong>
                </span>

                <form action="{{ route('instructors.regenerate-code', $instructor->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-ghost" style="font-size:11px;">Novo código</button>
                </form>

                <a href="{{ route('instructors.edit', $instructor->id) }}" class="btn-ghost">Editar</a>
            </div>

            <div class="students-grid">
                @forelse($instructor->students as $student)

                <div class="student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}">
                    <div class="student-card__header">
                        <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                        <div style="flex:1;">
                            <p class="student-card__name">{{ $student->user->name }}</p>
                            <p class="student-card__email">{{ $student->user->email }}</p>
                        </div>
                        <span class="badge-devedor {{ $student->is_defaulter ? 'badge-devedor--sim' : 'badge-devedor--nao' }}">
                            {{ $student->is_defaulter ? 'Devedor' : 'Em dia' }}
                        </span>
                    </div>
                    <div style="padding:10px 20px;">
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="btn-save">
                            + Criar treino
                        </a>
                    </div>
                </div>

                @empty
                <div class="inst-empty">Nenhum aluno vinculado.</div>
                @endforelse
            </div>

        </div>
        @empty
        <div class="inst-empty">Nenhum instrutor cadastrado.</div>
        @endforelse

    {{-- VISÃO DO INSTRUTOR --}}
    @else

        <div class="dash-hero">
            <div class="dash-hero__ring"></div>
            <div class="dash-hero__inner">
                <div>
                    <div class="dash-hero__eyebrow">Bem-vindo</div>
                    <h2 class="dash-hero__title">Meus Alunos</h2>
                </div>
                <div class="dash-hero__right">
                    <span class="dash-hero__pulse">INSTRUTOR</span>
                </div>
            </div>
        </div>

        {{-- Código de convite estilizado --}}
        <div style="margin: 20px 0; display:inline-flex; align-items:center; gap:20px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:18px 24px; flex-wrap:wrap;">
           <div>
             <p style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.10em; margin:0 0 6px;">Seu código de convite</p>
              <p style="font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:4px; color:#fff; margin:0; line-height:1;">{{ $instructor->invite_code ?? '—' }}</p>
           </div>
          <form action="{{ route('instructors.regenerate-code', $instructor->id) }}" method="POST" style="margin:0;">
          @csrf
         <button type="submit" class="btn-ghost">Regenerar código</button>
          </form>
        </div>

        {{-- Grid de alunos --}}
        <div class="students-grid">
            @forelse($instructor->students as $student)

            <div class="student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}">
                <div class="student-card__header">
                    <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                    <div style="flex:1;">
                        <p class="student-card__name">{{ $student->user->name }}</p>
                        <p class="student-card__email">{{ $student->user->email }}</p>
                    </div>
                    <span class="badge-devedor {{ $student->is_defaulter ? 'badge-devedor--sim' : 'badge-devedor--nao' }}">
                        {{ $student->is_defaulter ? 'Devedor' : 'Em dia' }}
                    </span>
                </div>
                <div style="padding:10px 20px;">
                    <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="btn-save">
                        + Criar treino
                    </a>
                </div>
            </div>

            @empty
            <div class="inst-empty" style="grid-column:1/-1;">Nenhum aluno vinculado ainda.</div>
            @endforelse
        </div>

    @endif

</div>
</div>
</x-app-layout>