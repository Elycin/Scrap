@extends("layouts.framework")

@section("title", "Scrap | Home")

@section("body")
    <div class="jumbotron">
        <h1 class="display-5">Welcome to the {{ env('APP_NAME') }}.</h1>
        <p class="lead">
            Scrap is high performance file upload solution for ShareX that allow users to rapidly upload files and provide them while using as little disk utilization
            as possible.
            <br>
            This instance is privately owned and maintained by the webmaster of this domain.
        </p>
        <hr class="my-4">
        <div class="row">
            <div class="col">
                <a class="btn btn-outline-dark btn-lg" href="/login" role="button">Log in</a>
                @if(env('ALLOW_REGISTRATION') == "true")
                    <a class="btn btn-outline-dark btn-lg" href="/register" role="button">Create Account</a>
                @endif
            </div>
        </div>
        <div class="mt-1"></div>
        <div class="row">
            <div class="col">
                <a class="btn btn-outline-dark btn-lg" href="/configure" role="button">Configure ShareX</a>
            </div>
        </div>
    </div>
@endsection