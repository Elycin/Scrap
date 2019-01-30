<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">{{ env('APP_NAME') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item {{ Route::current()->getName() == "home" ? "active" : "" }}">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item {{ Route::current()->getName() == "statistics" ? "active" : "" }}">
                <a class="nav-link" href="/statistics">Statistics</a>
            </li>
            <li class="nav-item {{ Route::current()->getName() == "about" ? "active" : "" }}">
                <a class="nav-link" href="/about">About</a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" target="_blank" href="https://getsharex.com/">ShareX</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" target="_blank" href="https://github.com/Elycin/Scrap">Scrap on Github</a>
            </li>
        </ul>
    </div>
</nav>