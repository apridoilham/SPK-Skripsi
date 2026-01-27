<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-red-700 hover:to-red-800 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-300 shadow-lg shadow-red-500/30 hover:scale-[1.02]']) }}>
    {{ $slot }}
</button>
