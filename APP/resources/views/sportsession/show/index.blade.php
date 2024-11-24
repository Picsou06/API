<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        color: #333;
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .btn-primary {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    li {
        background-color: #fff;
        margin: 10px 0;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    li a {
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
    }
    li a:hover {
        color: #0056b3;
    }
</style>
<div class="container">
    @if($sessions->isEmpty())
        <p>Aucune session encore existante.</p>
        <a href="{{ route('request-session.index') }}" class="btn btn-primary">Demander une session</a>
    @else
        <ul>
            @foreach($sessions as $session)
                <li><a href="{{ route('session.show', $session->id) }}">{{ $session->name }}</a></li>
            @endforeach
        </ul>
    @endif
</div>
@php
    $sessions = $sessions->sortBy('date');
@endphp