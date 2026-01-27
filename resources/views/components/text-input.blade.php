@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#232f3e] focus:ring-[#232f3e] rounded shadow-sm bg-white transition duration-150 py-2']) }}>