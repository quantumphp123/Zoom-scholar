@extends('admin.layout.layout')
@section('content')
  <div class="content-wrapper">
  
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
	
	@if (session('success'))
	<div class="card-body">
	<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5>{{ Session::get('success') }}</h5>
	<?php Session::forget('success');?>
	</div>
    </div>
	@endif
	
	
	
	 <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
		  
		
            <div class="card">
              <div class="card-header">
                {{-- <h3 class="card-title"><a href=""><button type="button" class="btn btn-block bg-gradient-primary float-right">Add User</button></a></h3> --}}
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                 <thead>
                  <tr>
                     <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Posts</th>
                     <th>Status</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; ?>
                    @foreach($data as $rows)
                    <tr>
                      <td>{{$i}}</td>
                      <td class="text-capitalize"> {{$rows->first_name}} {{$rows->last_name}}</td>
                      <td class="text-capitalize"> 
                       
                          {{$rows->email}}  
                          
                          
                      </td>
                      <td> {{$rows->phone}} </td>

                      <td>
                      <a href="{{ route('posts.get', ['userId' => $rows->id]) }}">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 520"><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z"/></svg>
                        
                      </a>
                      </td>
                      <td>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input status-input"
                                    id="status<?php echo $rows->id; ?>" <?php if ($rows->status == '1') {
                                        echo 'checked';
                                    } ?>
                                    value="{{ $rows->status }}">
                                <label class="custom-control-label"
                                    for="status<?php echo $rows->id; ?>"></label>
                            </div>
                        </div>
                    </td>
				
				
                      <td>
                            <a href="{{ route('delete-user', $rows->id) }}"  onclick="return confirm('WARNING\nData will be Deleted Permanently.\nAre You Sure?')">
                                <button class="btn btn-danger" type="button">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </a>
                         
                            
                            <button type="button" class="btn btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="{{ '#userDetailModal' . $rows->id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                      </td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                  
                  </tbody>
                  <tfoot>
                   <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Posts</th>
                     <th>Status</th>
                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
			
			
			
			
			
			
			
			
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
	
	
  
  
   </div>
   <!-- Modal -->
   @foreach ($data as $rows)
   <div class="modal fade" id="{{ 'userDetailModal' . $rows->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
       aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
           <div class="modal-content">
               <div class="modal-header">
                   <h1 class="modal-title fs-5" id="exampleModalLabel">User Details</h1>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                   <tr>
                      <th>Fields</th>
                     <th>Value</th>
                   </tr>
                   </thead>
                   <tbody>
                    <tr>
                      <td class="text-capitalize">Profile Image</td>
                      <td class="text-capitalize"> <img src="{{ $rows->profile_image }}" alt="profile_image" height="50px" width="50px" style="border-radius:3px;"></td>
                     </tr>     
                    <tr>
                      <td class="text-capitalize">NAME</td>
                      <td class="text-capitalize"> {{$rows->first_name}} {{$rows->last_name}}</td>
                     </tr>     
                    <tr>
                      <td class="text-capitalize">Email</td>
                      <td class="text-capitalize"> {{$rows->email}}</td>
                     </tr>     
                    <tr>
                      <td class="text-capitalize">Contact</td>
                      <td class="text-capitalize"> {{$rows->phone}}</td>
                     </tr>     
                    <tr>
                      <td class="text-capitalize">Date of Birth</td>
                      <td class="text-capitalize"> {{$rows->dob}}</td>
                     </tr>     
                    <tr>
                      <td class="text-capitalize">Status</td>
                      <td class="text-capitalize">
                        @if ($rows->status == 1)
                            Active
                        @else
                            Inactive
                        @endif
                      </td>
                     </tr>     
                   
                   </tbody>
                   <tfoot>
                    <tr>
                      <th>Fields</th>
                     <th>Value</th>
                   </tr>
                   </tfoot>
                 </table>
               </div>
           </div>
       </div>
   </div>
@endforeach
   <script>
    let statusCollect = document.querySelectorAll(".status-input")
    Array.from(statusCollect).forEach(statusInput => {
        statusInput.addEventListener('change', (e) => {
            let id = e.target.id
            let status = e.target.value
            $.ajax({
                url: "{{ route('change-user-status') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id.substring(6),
                    "status": status
                },
                success: function(response) {
                    e.target.setAttribute('value', response.status)
                    console.log("SUCCESS: " + JSON.stringify(response))
                },
                error: function(error) {
                    console.log("ERROR: " + error)
                }
            })
        })
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
  @endsection


