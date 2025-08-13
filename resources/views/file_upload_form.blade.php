@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Upload Files</h2>
        <form action="{{ route('file-upload.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="upload_type">Upload Type</label>
                <select name="upload_type" id="upload_type" class="form-control" required>
                    <option value="">Select Upload Type</option>
                    <option value="institute">Institute</option>
                    <option value="infrastructure">Infrastructure</option>
                    <option value="inspection">Sanitary Inspection</option>
                </select>
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <select name="district" id="district" class="form-control" required>
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}">{{ $district->distname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="upazila">Upazila</label>
                <select name="upazila" id="upazila" class="form-control" required>
                    <option value="">Select Upazila</option>
                </select>
            </div>
            <div class="form-group">
                <label for="union">Union</label>
                <select name="union" id="union" class="form-control" required>
                    <option value="">Select Union</option>
                </select>
            </div>
            <div class="form-group">
                <label for="institution_type">Institution Type</label>
                <select name="institution_type" id="institution_type" class="form-control" required>
                    <option value="">Select Type</option>
                    @foreach($institutionTypes as $type)
                        <option value="{{ $type->sch_type_edu }}">{{ $type->sch_type_edu }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="institution_name">Institution Name</label>
                <select name="institution_name" id="institution_name" class="form-control" required>
                    <option value="">Select Institution</option>
                </select>
            </div>

            <div class="form-group" id="infrastructure_group">
                <label for="infrastructure_name">Infrastructure Name</label>
                <select name="infrastructure_name" id="infrastructure_name" class="form-control">
                    <option value="">Select Infrastructure</option>
                </select>
            </div>
            <div class="form-group">
                <label for="files">Files</label>
                <input type="file" name="files[]" id="files" class="form-control" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Hide infrastructure_name by default
            $('#infrastructure_group').hide();

            // Show/hide infrastructure_name based on upload_type
            $('#upload_type').on('change', function() {
                if ($(this).val() === 'infrastructure' || $(this).val() === 'inspection') {
                    $('#infrastructure_group').show();
                } else {
                    $('#infrastructure_group').hide();
                    $('#infrastructure_name').val('');
                }
            });


            //IF $(this).val() === 'infrastructure' then image 1 image, $(this).val() === 'inspection' upto 3 image,  $('#files').attr('accept', 'image/*');
            $('#upload_type').on('change', function() {
                if ($(this).val() === 'inspection') {
                    $('#files').attr('accept', 'image/*');
                    $('#files').attr('multiple', true);
                    $('#files').off('change.maxImages').on('change.maxImages', function() {
                        if (this.files.length > 3) {
                            alert('You can upload a maximum of 3 images for inspection.');
                            this.value = '';
                        }
                    });                
                } else{
                    $('#files').attr('accept', 'image/*');
                    $('#files').attr('multiple', false);
                }
            });

            $('#district').on('change', function () {
                var districtId = $(this).val();
                var $upazila = $('#upazila');
                $upazila.empty();
                $upazila.append('<option value="">Loading...</option>');
                if (districtId) {
                    $.ajax({
                        url: '/get-upazilas/' + districtId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $upazila.empty();
                            $upazila.append('<option value="">Select Upazila</option>');
                            $.each(data, function (i, upazila) {
                                $upazila.append('<option value="' + upazila.id + '">' + upazila.upname + '</option>');
                            });
                        },
                        error: function () {
                            $upazila.empty();
                            $upazila.append('<option value="">Select Upazila</option>');
                        }
                    });
                } else {
                    $upazila.empty();
                    $upazila.append('<option value="">Select Upazila</option>');
                }
            });



            //onclick #upazila get unions
            $('#upazila').on('change', function () {
                var upazilaId = $(this).val();
                var $union = $('#union');
                $union.empty();
                $union.append('<option value="">Loading...</option>');
                if (upazilaId) {
                    $.ajax({
                        url: '/get-unions/' + upazilaId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $union.empty();
                            $union.append('<option value="">Select Union</option>');
                            $.each(data, function (i, union) {
                                $union.append('<option value="' + union.id + '">' + union.unname + '</option>');
                            });
                        },
                        error: function () {
                            $union.empty();
                            $union.append('<option value="">Select Union</option>');
                        }
                    });
                } else {
                    $union.empty();
                    $union.append('<option value="">Select Union</option>');
                }
            });


            //onclick #union get institutions
            $('#union').on('change', function () {
            });

            // onclick #institution_type get institutions
            $('#institution_type').on('change', function () {
                var unionId = $('#union').val();
                var institutionType = $(this).val();
                var $institution = $('#institution_name');
                $institution.empty();
                $institution.append('<option value="">Loading...</option>');
                if (unionId && institutionType) {
                    $.ajax({
                        url: '/get-institutions/' + unionId + '/' + institutionType,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $institution.empty();
                            $institution.append('<option value="">Select Institution</option>');
                            $.each(data, function (i, institution) {
                                $institution.append('<option value="' + institution.id + '">' + institution.sch_name_en + '</option>');
                            });
                        },
                        error: function () {
                            $institution.empty();
                            $institution.append('<option value="">Select Institution</option>');
                        }
                    });
                } else {
                    $institution.empty();
                    $institution.append('<option value="">Select Institution</option>');
                }
            });



            // When institution is selected, fetch infrastructures
            $('#institution_name').on('change', function () {
                var institutionId = $(this).val();
                var $infrastructure = $('#infrastructure_name');
                $infrastructure.empty();
                $infrastructure.append('<option value="">Loading...</option>');
                if (institutionId) {
                    $.ajax({
                        url: '/get-infrastructures/' + institutionId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $infrastructure.empty();
                            $infrastructure.append('<option value="">Select Infrastructure</option>');
                            $.each(data, function (i, infrastructure) {
                                $infrastructure.append('<option value="' + infrastructure.id + '">' + infrastructure.water_id + '</option>');
                            });
                        },
                        error: function () {
                            $infrastructure.empty();
                            $infrastructure.append('<option value="">Select Infrastructure</option>');
                        }
                    });
                } else {
                    $infrastructure.empty();
                    $infrastructure.append('<option value="">Select Infrastructure</option>');
                }
            });



            //onclick upload call upload method with all input data
            $('#uploadBtn').on('click', function () {
                var formData = new FormData();
                var files = $('#files')[0].files;
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
                $.ajax({
                    url: '/upload',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        alert(response.message);
                    },
                    error: function (xhr, status, error) {
                        alert('Error uploading files.');
                    }
                });
            });

        });
    </script>
@endsection