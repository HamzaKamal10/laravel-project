<nav class="border-b border-border bg-background px-6">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between">
        <div>
            <a href="/" class="text-xl font-bold tracking-tight">Idea</a>
        </div>
        <div class="flex items-center gap-x-5">
            @guest
                <a href="{{ route('login') }}" class="text-sm font-medium hover:underline">Sign In</a>
                <a href="{{ route('register') }}" class="btn">Register</a>
            @else
                {{-- يرسل تسجيل الخروج كطلب POST محمي بدلاً من استخدام رابط GET. --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-outlined">Log Out</button>
                </form>
            @endguest
        </div>
    </div>
</nav>