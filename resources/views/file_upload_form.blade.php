@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form action="{{ route('file-upload.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_id"></label>
                        <input type="hidden" name="user_id" id="user_id" class="form-control" value="{{ $userId }}">
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
                        <select name="institution_id" id="institution_name" class="form-control" required>
                            <option value="">Select Institution</option>
                        </select>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="form-group" id="institution_name_1_group">
                        <label for="institution_name">Institution English Name</label>
                        <input type="text" name="institution_name" id="institution_name_1" class="form-control" required>
                    </div>
                    <!-- <div class="form-group" id="institution_name_1_bn_group">
                        <label for="institution_name_1_bn">Institution Bangla Name</label>
                        <input type="text" name="institution_name_1_bn" id="institution_name_1_bn" class="form-control" required>
                    </div> -->
                    <div class="form-group" id="institution_latitude_group">
                        <label for="institution_latitude">Latitude</label>
                        <input type="text" name="institution_latitude" id="institution_latitude" class="form-control"
                            required>
                    </div>
                    <div class="form-group" id="institution_longitude_group">
                        <label for="institution_longitude">Longitude</label>
                        <input type="text" name="institution_longitude" id="institution_longitude" class="form-control"
                            required>
                    </div>
                    <div class="form-group" id="infrastructure_group">
                        <label for="infrastructure_name">Infrastructure Name</label>
                        <select name="infrastructure_id" id="infrastructure_name" class="form-control">
                            <option value="">Select Infrastructure</option>
                        </select>
                    </div>

                    <div class="form-group" id="inspaction_date_section">
                        <label for="inspaction_date">Inspaction Date</label>
                        <select name="inspection_date" id="inspaction_date" class="form-control">
                            <option value="">Select Date</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="files">Files</label>
                        <input type="file" name="files[]" id="files" class="form-control" multiple>
                        <div id="file-preview" class="mt-2 row"></div>
                        <div id="previous-image-preview" class="mt-2 row"></div>
                    </div>
                </div>

                <style>
                    .preview-img {
                        max-width: 100px;
                        max-height: 100px;
                        margin: 5px;
                        position: relative;
                    }

                    .remove-img-btn {
                        position: absolute;
                        top: 0;
                        right: 0;
                        background: rgba(255, 0, 0, 0.7);
                        color: #fff;
                        border: none;
                        border-radius: 50%;
                        width: 24px;
                        height: 24px;
                        cursor: pointer;
                        z-index: 2;
                    }

                    .preview-img-wrapper {
                        display: inline-block;
                        position: relative;
                    }
                </style>
            </div>

            <!-- Submit Button Full Width -->
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Image preview and delete
            let fileInput = document.getElementById('files');
            let previewDiv = document.getElementById('file-preview');
            let filesArray = [];

            fileInput.addEventListener('change', function (e) {
                filesArray = Array.from(fileInput.files);
                renderPreviews();
            });

            function renderPreviews() {
                previewDiv.innerHTML = '';
                filesArray.forEach(function (file, idx) {
                    if (file.type.startsWith('image/')) {
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            let wrapper = document.createElement('div');
                            wrapper.className = 'preview-img-wrapper';
                            let img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-img';
                            img.style.width = '100px';
                            img.style.height = '100px';
                            img.style.objectFit = 'cover';
                            let btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'remove-img-btn';
                            btn.innerHTML = '&times;';
                            btn.onclick = function () {
                                filesArray.splice(idx, 1);
                                updateFileList();
                                renderPreviews();
                            };
                            wrapper.appendChild(img);
                            wrapper.appendChild(btn);
                            previewDiv.appendChild(wrapper);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            function updateFileList() {
                // Create a new DataTransfer to update the file input
                let dt = new DataTransfer();
                filesArray.forEach(function (file) {
                    dt.items.add(file);
                });
                fileInput.files = dt.files;
            }
            // Hide infrastructure_name by default
            $('#infrastructure_group').hide();


            //default institution_name_1, institution_latitude, institution_longitude hide
            $('#institution_name_1_group').hide();
            // $('#institution_name_1_bn_group').hide();
            $('#institution_latitude_group').hide();
            $('#institution_longitude_group').hide();
            $('#inspaction_date_section').hide();
            // Show/hide infrastructure_name based on upload_type
            $('#upload_type').on('change', function () {


                if ($(this).val() === 'infrastructure' || $(this).val() === 'inspection') {

                    if ($(this).val() === 'inspection') {
                        $('#inspaction_date_section').show();
                    } else {
                        $('#inspaction_date_section').hide();
                    }

                    $('#infrastructure_group').show();
                    $('#institution_name_1_group').hide();
                    // $('#institution_name_1_bn_group').hide();
                    $('#institution_latitude_group').hide();
                    $('#institution_longitude_group').hide();
                    $('#institution_name_1').val('');
                    $('#institution_name_1_bn').val('');
                    $('#institution_latitude').val('');
                    $('#institution_longitude').val('');

                } else {
                    $('#infrastructure_group').hide();
                    $('#infrastructure_name').val('');
                    $('#institution_name_1_group').show();
                    // $('#institution_name_1_bn_group').show();
                    $('#institution_latitude_group').show();
                    $('#institution_longitude_group').show();
                    $('#inspaction_date_section').hide();
                }
            });


            //IF $(this).val() === 'infrastructure' then image 1 image, $(this).val() === 'inspection' upto 3 image,  $('#files').attr('accept', 'image/*');
            $('#upload_type').on('change', function () {
                if ($(this).val() === 'inspection') {
                    $('#files').attr('accept', 'image/*');
                    $('#files').attr('multiple', true);
                    $('#files').off('change.maxImages').on('change.maxImages', function () {
                        if (this.files.length > 3) {
                            alert('You can upload a maximum of 3 images for inspection.');
                            this.value = '';
                        }
                    });
                } else {
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


            let allInstitutions = [];
            // onclick #institution_type get institutions
            $('#institution_type').on('change', function () {
                var unionId = $('#union').val();
                var userId = $('#user_id').val(); // Assuming you have a hidden input with user ID
                var institutionType = $(this).val();
                var $institution = $('#institution_name');
                $institution.empty();
                $institution.append('<option value="">Loading...</option>');
                if (unionId && institutionType) {
                    $.ajax({
                        url: '/get-institutions/' + unionId + '/' + institutionType + '/' + userId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            allInstitutions = data;
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
                var $prevImg = $('#previous-image-preview');
                $infrastructure.empty();
                $infrastructure.append('<option value="">Loading...</option>');
                $prevImg.empty();
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

                            //find selected institution
                            var selectedInstitution = allInstitutions.find(function (institution) {
                                return institution.id == institutionId;
                            });

                            console.log(selectedInstitution);

                            $('#institution_name_1').val(selectedInstitution.sch_name_en);
                            $('#institution_latitude').val(selectedInstitution.lat);
                            $('#institution_longitude').val(selectedInstitution.lon);

                            // Show previous image if exists
                            if (selectedInstitution && selectedInstitution.img9) {
                                var imgUrl = "{{ Storage::disk('mis_uploads')->url('sp_satkhira_inst') }}/" + selectedInstitution.img9;
                                var imgTag = '<img src="' + imgUrl + '" class="preview-img" style="width:100px;height:100px;object-fit:cover;">';
                                $prevImg.html(imgTag);
                            } else {
                                $prevImg.html('<span class="text-muted">No previous image found.</span>');
                            }
                        },
                        error: function () {
                            $infrastructure.empty();
                            $infrastructure.append('<option value="">Select Infrastructure</option>');
                            $prevImg.html('<span class="text-danger">Error loading previous image.</span>');
                        }
                    });
                } else {
                    $infrastructure.empty();
                    $infrastructure.append('<option value="">Select Infrastructure</option>');
                    $prevImg.empty();
                }
            });


            //onclick #infrastructure_name get date
            $('#infrastructure_name').on('change', function () {
                var $prevImg = $('#previous-image-preview');
                var infrastructure_Id = $(this).val();
                var selectedInfrastructureImage = $(this).find("option:selected").text()+'.jpg';
                var $inspaction_date = $('#inspaction_date');
                $inspaction_date.empty();
                $inspaction_date.append('<option value="">Loading...</option>');
                 $prevImg.empty();
                if (infrastructure_Id) {
                    $.ajax({
                        url: '/get-inspaction-dates/' + infrastructure_Id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $inspaction_date.empty();
                            $inspaction_date.append('<option value="">Select Inspaction Date</option>');
                            $.each(data, function (i, inspactionInfo) {
                                $inspaction_date.append('<option value="' + inspactionInfo.inspection_date + '">' + inspactionInfo.inspection_date + '</option>');
                            });

                            var imgUrl = "{{ Storage::disk('mis_uploads')->url('sp_satkhira_infras') }}/" + selectedInfrastructureImage;
                            var imgTag = '<img src="' + imgUrl + '" class="preview-img" style="width:100px;height:100px;object-fit:cover;">';
                            $prevImg.html(imgTag);
                        },
                        error: function () {
                            $inspaction_date.empty();
                            $inspaction_date.append('<option value="">Select Inspaction Date</option>');
                        }
                    });
                } else {
                    $inspaction_date.empty();
                    $inspaction_date.append('<option value="">Select Inspaction Date</option>');
                }
            });




            //onchange inspaction_date get inspaction images and preview in #previous-image-preview
            $('#inspaction_date').on('change', function () {
                var $prevImg = $('#previous-image-preview');
                var inspaction_date = $(this).val();
                var infrastructure_id = $('#infrastructure_name').val();
                $prevImg.empty();
                if (inspaction_date) {
                    $.ajax({
                        url: '/get-inspaction-images/' + infrastructure_id + '/' + inspaction_date,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            var images = Object.values(data);
                            if (images.length) {
                                $.each(images, function (i, image) {
                                    var imgUrl = ("{{ Storage::disk('mis_uploads')->url('/') }}" + image).replace('upload/upload', 'upload');
                                    var imgTag = '<img src="' + imgUrl + '" class="preview-img" style="width:100px;height:100px;object-fit:cover;">';
                                    $prevImg.append(imgTag);
                                });
                            } else {
                                $prevImg.html('<span class="text-muted">No images found.</span>');
                            }
                        },
                        error: function () {
                            $prevImg.html('<span class="text-danger">Error loading images.</span>');
                        }
                    });
                } else {
                    $prevImg.empty();
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