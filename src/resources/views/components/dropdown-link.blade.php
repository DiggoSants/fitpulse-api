<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 
    transition duration-150 ease-in-out focus:outline-none']) }}
   style="color:rgba(255,255,255,0.85);"
   onmouseover="this.style.background='rgba(255,255,255,0.08)'"
   onmouseout="this.style.background='transparent'"
>{{ $slot }}</a>