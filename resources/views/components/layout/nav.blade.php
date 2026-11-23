    <nav class="border-b border-border px-6">
    <div class="max-w-7x1 mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/ideas">idea</a>
        </div>
        <div class="flex gap-5">
            @auth
                <a href="/profile/edit">edit</a>

                <form method="post" action="/logout">
                    @method('DELETE')
                    @csrf

                    <button>
                        log out
                    </button>
                </form>

            @endauth

            @guest
                <a href="/login">login</a>
                <a href="/register" class="btn">register</a>
            @endguest

        </div>
    </div>
</nav>
