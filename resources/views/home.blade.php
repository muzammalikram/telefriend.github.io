@extends('layouts.app')

@section('content')
 

				 
 
					<router-link to="/profile"><a>Go to UserProfile</a></router-link>
    
          <div> 
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="">
                  @csrf
                 <a class="dropdown-item" href="{{ route('logout') }}"
                 onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                  LOGOUT
              </a>
              </form>
          </div>
                 
 
          <router-view></router-view>
          
@endsection
