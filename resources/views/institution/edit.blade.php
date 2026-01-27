@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Institution Details</h3>
    <form action="{{ route('institution.update') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>Type of Institution : Ownership</label>
                <select name="ownership" class="form-control">
                    <option value="Government" {{ (isset($institution->ownership) && $institution->ownership==='Government') ? 'selected' : '' }}>Government</option>
                    <option value="Private" {{ (isset($institution->ownership) && $institution->ownership==='Private') ? 'selected' : '' }}>Private</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Type of Institution : Education</label>
                <select name="education_type" class="form-control">
                    <option value="Primary School" {{ (isset($institution->education_type) && $institution->education_type==='Primary School') ? 'selected' : '' }}>Primary School</option>
                    <option value="High School" {{ (isset($institution->education_type) && $institution->education_type==='High School') ? 'selected' : '' }}>High School</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Type of Institution : Gender</label>
                <select name="gender_type" class="form-control">
                    <option value="Boys" {{ (isset($institution->gender_type) && $institution->gender_type==='Boys') ? 'selected' : '' }}>Boys</option>
                    <option value="Girls" {{ (isset($institution->gender_type) && $institution->gender_type==='Girls') ? 'selected' : '' }}>Girls</option>
                    <option value="Co-Ed" {{ (isset($institution->gender_type) && $institution->gender_type==='Co-Ed') ? 'selected' : '' }}>Co-Ed</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Institution Name (English)</label>
                <input type="text" name="name_en" class="form-control" value="{{ $institution->name_en ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Institution Name (Bangla)</label>
                <input type="text" name="name_bn" class="form-control" value="{{ $institution->name_bn ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Establishment Year</label>
                <input type="text" name="establishment_year" class="form-control" value="{{ $institution->establishment_year ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Total Waterpoints</label>
                <input type="number" name="total_waterpoints" class="form-control" value="{{ $institution->total_waterpoints ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Drinking Sources</label>
                <input type="number" name="drinking_sources" class="form-control" value="{{ $institution->drinking_sources ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Full Functional Sources</label>
                <input type="number" name="full_functional_sources" class="form-control" value="{{ $institution->full_functional_sources ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Boy Students</label>
                <input type="number" name="boy_students" class="form-control" value="{{ $institution->boy_students ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Girl Students</label>
                <input type="number" name="girl_students" class="form-control" value="{{ $institution->girl_students ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Disable Boys</label>
                <input type="number" name="disable_boys" class="form-control" value="{{ $institution->disable_boys ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Disable Girls</label>
                <input type="number" name="disable_girls" class="form-control" value="{{ $institution->disable_girls ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Total Students</label>
                <input type="number" name="total_students" class="form-control" value="{{ $institution->total_students ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Assessment Date</label>
                <input type="date" name="assessment_date" class="form-control" value="{{ $institution->assessment_date ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Number of Nearby Families use this </label>
                <input type="text" name="nearby_family_school" class="form-control" value="{{ $institution->nearby_family_school ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Average Monthly Patients</label>
                <input type="text" name="average_monthly_particular" class="form-control" value="{{ $institution->average_monthly_particular ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Patients in Catchment Area</label>
                <input type="text" name="catchment_area" class="form-control" value="{{ $institution->catchment_area ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Male Staff</label>
                <input type="number" name="male_staff" class="form-control" value="{{ $institution->male_staff ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Female Staff</label>
                <input type="number" name="female_staff" class="form-control" value="{{ $institution->female_staff ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <!-- <div class="col-md-6">
                <label>Total Staff</label>
                <input type="number" name="total_staff" class="form-control" value="{{ $institution->total_staff ?? '' }}">
            </div> -->
            <div class="col-md-6">
                <label>Name of Respondent</label>
                <input type="text" name="respondent_name" class="form-control" value="{{ $institution->respondent_name ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Designation of Respondent</label>
                <input type="text" name="respondent_designation" class="form-control" value="{{ $institution->respondent_designation ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Mobile of Respondent</label>
                <input type="text" name="respondent_mobile" class="form-control" value="{{ $institution->respondent_mobile ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Name of Headmaster/CHCP</label>
                <input type="text" name="headmaster_name" class="form-control" value="{{ $institution->headmaster_name ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Mobile of Headmaster/CHCP</label>
                <input type="text" name="headmaster_mobile" class="form-control" value="{{ $institution->headmaster_mobile ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Name of SMC President</label>
                <input type="text" name="smc_president_name" class="form-control" value="{{ $institution->smc_president_name ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Mobile of SMC President</label>
                <input type="text" name="smc_president_mobile" class="form-control" value="{{ $institution->smc_president_mobile ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Village (English)</label>
                <input type="text" name="village_en" class="form-control" value="{{ $institution->village_en ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Village (Bangla)</label>
                <input type="text" name="village_bn" class="form-control" value="{{ $institution->village_bn ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" value="{{ $institution->latitude ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" value="{{ $institution->longitude ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="{{ $institution->contact_email ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Onboarding Time</label>
                <input type="text" name="onboarding_time" class="form-control" placeholder="QQ-YYYY" value="{{ $institution->onboarding_time ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Last updated on</label>
                <input type="text" name="last_updated_on" class="form-control" placeholder="QX-YYYY" value="{{ $institution->last_updated_on ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Comments</label>
                <input type="text" name="comments" class="form-control" value="{{ $institution->comments ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="assessment_submitted" id="assessment_submitted" {{ !empty($institution->assessment_submitted) ? 'checked' : '' }}>
                    <label class="form-check-label" for="assessment_submitted">
                        Check if technical assessment data 2025 submitted
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-info">Submit Data</button>
            </div>
        </div>
    </form>

    <!-- Infrastructure Details Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h5 class="text-center">INFRASTRUCTURE DETAILS:</h5>
        </div>
    </div>
    @php
        // Example: $infrastructures = [ ... ];
        $infrastructures = isset($infrastructures) ? $infrastructures : [
            (object)['id'=>'SA_DH_PR_PS_03_DTW_01','type'=>'Deep Tubewell','managed'=>'Yes'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_02','type'=>'Deep Tubewell','managed'=>'Yes'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_03','type'=>'Deep Tubewell','managed'=>'No'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_04','type'=>'Deep Tubewell','managed'=>'No'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_05','type'=>'Deep Tubewell','managed'=>'No'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_06','type'=>'Deep Tubewell','managed'=>'No'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_07','type'=>'Deep Tubewell','managed'=>'No'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_08','type'=>'Deep Tubewell','managed'=>'No'],
            (object)['id'=>'SA_DH_PR_PS_03_DTW_09','type'=>'Deep Tubewell','managed'=>'No'],
        ];
    @endphp
    @foreach($infrastructures as $i => $infra)
    <div class="row mt-4 border-bottom pb-3">
        <div class="col-md-12 mb-2">
            <b>Infrastructure {{ $i+1 }}</b>
        </div>
        <div class="col-md-4">
            <label>Infrastructure ID</label>
            <input type="text" class="form-control" value="{{ $infra->id }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Infrastructure Type</label>
            <input type="text" class="form-control" value="{{ $infra->type }}" readonly>
        </div>
        <div class="col-md-4">
            <label>Main drinking water source?</label>
            <input type="text" class="form-control" value="{{ $infra->managed }}" readonly>
        </div>
        <div class="col-md-12 text-right mt-2">
            <a href="{{ route('water-point.edit', ['id' => $infra->id]) }}" class="btn btn-warning btn-sm">Edit Form</a>
        </div>
    </div>
    @endforeach
    <!-- <div class="row mt-4">
        <div class="col-md-12 text-center">
            <a href="{{ route('water-point.create', ['institution_id' => $institution->id ?? null]) }}" class="btn btn-info">★ ADD A NEW INFRASTRUCTURE</a>
        </div>
    </div> -->
</div>
@endsection
