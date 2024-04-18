@extends('admin.layout.layout')
@section('style')
    <style>
        /* nav */
        .nav-link {
            color: rgb(110, 110, 110);
            font-weight: 500;
        }

        .nav-link:hover {
            color: #667eea;
        }

        .nav-pills .nav-link.active {
            color: white;
            background-color: #667eea;
            border-radius: 0.5rem 0.5rem 0 0;
            font-weight: 600;
        }

        .tab-content {
            padding-bottom: 1.3rem;
            padding-top: 0 !important;
        }
    </style>
@endsection

@section('content')
  <div class="content-wrapper">
  
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Questions by @php $user = \App\Models\User::where('id', request('userId'))->get(['first_name','last_name']); @endphp  {{ isset($user[0]) ? $user[0]->first_name." ".$user[0]->last_name : "" }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Questions</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
	
	 <section class="content">
      <div class="container-fluid">
        <div class="col-md-12 col-sm-6">
          <ul class="nav-pills nav mb-3 shadow-sm nav-fill" id="pills-tab" role="tablist">
              <li class="nav-item">
                  <a href="{{ route('posts.get', ['userId' => request('userId') ]) }}" class="nav-link " aria-selected="false">Posts</a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('question.get', ['userId' => request('userId') ]) }}" class="nav-link active" aria-selected="true">Questions <span class="badge badge-pill">{{ count($data) }}</span></a>
              </li>
          </ul>
        </div>
        <div class="row">
          <div class="col-12">
            <h4>Recent Activity</h4>
            @forelse ($data as $rows)
                <div class="post">
                  <div class="user-block">
                    <img class="img-circle img-bordered-sm" src="{{ $rows->askedByImage }}" alt="user image">
                    <span class="username">{{ $rows->askedBy }}</span>
                    <span class="description">Shared publicly {{ $rows->created_at->diffForHumans() }}</span>
                  </div>
                  <!-- /.user-block -->
                  <p>
                    <span class="float-right"> <i class="far fa-comments mr-1"></i>Answers ({{ count($rows->answers) }})</span>
                  </p>
                  <p>{{ $rows->question }}</p>
                  <p class="h5">Answers:</p>
                  <div class="">
                    @forelse($rows->answers as $answers)
                      <div class="user-block">
                        <img class="img-circle img-bordered-sm" src="{{ $answers->answeredByImage }}" alt="user image">
                        <span class="username">{{ $answers->answeredBy }}</span>
                        {{-- <span class="description">Shared publicly {{ $answers->created_at->diffForHumans() }}</span> --}}
                        <span class="description">Shared publicly {{ \Carbon\Carbon::parse($answers->created_at)->diffForHumans() }}</span>
                        
                      </div>
                      
                      <p>{{ $answers->answer }}</p>
                    @empty
                      <span class="text-danger">No Answers</span>  
                    @endforelse
                  </div>
                </div>
                @empty
                <div class="error-page">
                  <h2 class="headline text-warning">404</h2>
                  <div class="error-content">
                      <h3>
                          <i class="fas fa-exclamation-triangle text-warning"></i>
                          Oops! No Questions found.
                      </h3>
                      <p>No Questions asked by this user. Meanwhile, you may <a href="{{ route('Admin.UserIndex') }}">return back to users listing</a></p>
                  </div>
              </div>
            @endforelse
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
	
	
  
  
   </div>
   <!-- Modal -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
  @endsection


