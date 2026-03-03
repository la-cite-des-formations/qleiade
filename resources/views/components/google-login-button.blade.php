<div class="mt-8">
    <div class="relative flex items-center justify-center">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-200 dark:border-white/10"></div>
        </div>
        <div
            class="relative bg-white px-4 text-xs font-medium uppercase tracking-widest text-gray-500 dark:bg-gray-900 dark:text-gray-400">
            Ou
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('auth.google.redirect') }}"
            class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 fi-color-gray fi-btn-color-gray fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-white dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 text-gray-950 dark:text-white rounded-lg w-full">
            <svg class="fi-btn-icon h-5 w-5 shrink-0" viewBox="0 0 48 48">
                <path fill="#EA4335"
                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                <path fill="#4285F4"
                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                <path fill="#FBBC05"
                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                <path fill="#34A853"
                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                <path fill="none" d="M0 0h48v48H0z" />
            </svg>
            <span>Se connecter avec Google</span>
        </a>
    </div>
</div>
