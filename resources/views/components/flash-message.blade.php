@props(['message'])
        {{-- サーバーから何かをもらうときは、sessionを用いる --}}
        {{-- 逆に投稿するときはpostになる --}}
@if ($message)
    <div class="bg-blue-100 border-blue-500 text-blue-700 border-l-4 p-4 my-2">
        {{ session('notice') }}
    </div>
@endif
