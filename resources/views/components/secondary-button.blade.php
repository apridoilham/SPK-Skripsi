<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-xl font-bold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#232f3e] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-300 hover:border-[#232f3e]']) }}>
    {{ $slot }}
</button>
