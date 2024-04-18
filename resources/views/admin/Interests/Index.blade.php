@extends('admin.layout.layout')
@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Interests</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Interests</li>
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
                    <?php Session::forget('success'); ?>
                </div>
            </div>
        @endif



        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">


                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><button type="button" class="btn btn-primary mt-3"
                                        data-bs-toggle="modal" data-bs-target="#addInterestModal">
                                        Add Interest
                                    </button>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($data as $rows)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td class="text-capitalize"> {{ $rows->name }} </td>
                                                <td>

                                                    <img class="img-thumbnail" src="{{ $rows->image }}"
                                                        style="height:60px;width:80px;" />


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
                                                    <a href="{{ route('delete-interest', $rows->id) }}"
                                                        onclick="return confirm('Are You Sure?')">
                                                        <button class="btn btn-danger" type="button">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </a>
                                                    <button type="button" class="btn btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="{{ '#editInterestModal' . $rows->id }}">
                                                        Edit
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
                                            <th>Image</th>
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
        <div class="modal fade" id="{{ 'editInterestModal' . $rows->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Interest</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('edit-interest') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $rows->id }}">
                            <div class="mb-3">
                                <label for="addInterestName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="addInterestName" name="name"
                                    aria-describedby="emailHelp" value="{{ $rows->name }}">
                            </div>
                            <div class="mb-3">
                                <label for="addElementImg" class="form-label">Image</label>
                                <input type="file" class="form-control" id="addElementImg" name="image"
                                    aria-describedby="emailHelp">
                            </div>
                            <button class="btn btn-primary btn-block">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- Add Interest Modal -->
        <div class="modal fade" id="addInterestModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Interest</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('add-interest') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="addInterestName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="addInterestName" name="name"
                                    aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                                <label for="addElementImg" class="form-label">Image</label>
                                <input type="file" class="form-control" id="addElementImg" name="image"
                                    aria-describedby="emailHelp">
                            </div>
                            <button class="btn btn-primary btn-block">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <script>
        let statusCollect = document.querySelectorAll(".status-input")
        Array.from(statusCollect).forEach(statusInput => {
            statusInput.addEventListener('change', (e) => {
                let id = e.target.id
                let status = e.target.value
                $.ajax({
                    url: "{{ route('change-interest-status') }}",
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
