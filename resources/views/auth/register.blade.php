<x-layout.layout>
    <div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-bold tracking-tight">Register an account</h1>
                <p class="text-muted-foreground mt-1 text-sm">Start tracking your ideas today.</p>
            </div>
            
            <form action="/register" method="POST" class="mt-10 space-y-4">
                @csrf
                <x-form.field name="name" label="Name" />
                <x-form.field name="email" label="Email" type="email" />
                <x-form.field name="password" label="Password" type="password" />

                <button type="submit" class="mt-2 h-10 w-full bg-primary text-white rounded-md font-medium hover:opacity-90 transition-opacity">
                    Create Account
                </button>
            </form>
        </div>
    </div>
</x-layout.layout>