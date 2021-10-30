@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <h2>Hello {{ auth()->user()->name }}</h2>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @isset($hobbies)
                        @if($hobbies->count() > 0)
                            <h3>Your Hobbies:</h3>
                        @endif

                        <ul class="list-group">
                            @foreach($hobbies as $hobby)
                                <li class="list-group-item">
                                    <a title="Show Details" href="/hobby/{{ $hobby->id }}">{{ $hobby->name }}</a>
                                    @auth
                                    <a href="/hobby/{{ $hobby->id }}/edit" class="btn btn-sm btn-light ml-2">
                                        <i class="fas fa-edit"></i> Edit Hobby
                                    </a>
                                    @endauth

                                    @auth
                                    <form action="/hobby/{{ $hobby->id }}" method="post" class="float-right">
                                        @csrf
                                        @method('DELETE')
                                        <input type="submit" value="Delete" class="btn btn-sm btn-outline-danger">
                                    </form>
                                    @endauth
                                    
                                    <span class="float-right mx-2">{{ $hobby->created_at->diffForHumans() }}</span>
                                    <br>
                                    @foreach($hobby->tags as $tag)
                                    <a href="/hobby/tag/{{ $tag->id }}">
                                        <span class="badge badge-{{ $tag->style }}">{{ $tag->name }}</span>
                                    </a>
                                    @endforeach
                                </li>
                            @endforeach
                        </ul>
                    @endisset

                    <a class="btn btn-success btn-sm mt-3" href="/hobby/create"><i class="fas fa-plus-circle"></i> Create New Hobby</a>


                    <!-- {{ __('You are logged in!') }} -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




