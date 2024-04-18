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
            <h1>Posts by @php $user = \App\Models\User::where('id', request('userId'))->get(['first_name','last_name']); @endphp  {{ isset($user[0]) ? $user[0]->first_name." ".$user[0]->last_name : "" }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Posts</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
	
	 <section class="content">
      <div class="container-fluid">
        <ul class="nav-pills nav mb-3 shadow-sm nav-fill" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('posts.get', ['userId' => request('userId') ]) }}" class="nav-link active" aria-selected="true">Posts
                    <span class="badge badge-pill">{{ count($data) }}</span>
                </a>
                
            </li>
            <li class="nav-item">
                <a href="{{ route('question.get', ['userId' => request('userId') ]) }}" class="nav-link">Questions</a>
            </li>
        </ul>
        <div class="row">
            @forelse($data as $rows)
            <div class="col-lg-4  col-sm-12 mb-2 d-flex align-items-stretch">
                <div class="card">
                    <img class="card-img-top" 
                        src="{{ $rows->image }}" 
                        alt="Post Image"
                        loading="lazy"
                        style="width: 100%;max-height: 50vh;object-fit: contain;"
                    />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $rows->title }}</h5>
                        <div class="card-text">
                            {{ $rows->description }}
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $rows->created_at->diffForHumans() }}
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            @empty
            <!-- <span class="text-danger justify-content-center">No Posts found!</span> -->
            <div class="error-page">
                <h2 class="headline text-warning">404</h2>
                <div class="error-content">
                    <h3>
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Oops! No Posts found.
                    </h3>
                    <p>No Posts uploaded by this user. Meanwhile, you may <a href="{{ route('Admin.UserIndex') }}">return back to users listing</a></p>
                </div>
            </div>
            @endforelse
          <!-- /.col -->
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


