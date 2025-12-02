@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fa fa-plus-square"></i> Add a new Infrastructure</h4>
        <a href="{{ route('institution.edit', ['id' => $institution->id ?? null]) }}" class="btn btn-secondary">Back</a>
    </div>
    <form action="{{ route('water-point.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label>Institution Name</label>
                <input type="text" class="form-control" name="institution_name" value="{{ $institution->name_en ?? '' }}" readonly>
            </div>
            <div class="col-md-4">
                <label>Institution ID</label>
                <input type="text" class="form-control" name="institution_id" value="{{ $institution->id ?? '' }}" readonly>
            </div>
            <div class="col-md-4">
                <label>Institution Type</label>
                <input type="text" class="form-control" name="institution_type" value="{{ $institution->education_type ?? '' }}" readonly>
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Infrastructure Type</label>
                    <select name="infrastructure_type" class="form-control">
                        <option value="DTW">DTW</option>
                        <option value="STW">STW</option>
                        <option value="RWH">RWH</option>
                        <option value="RO">RO</option>
                        <option value="PWB">PWB</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Infrastructure ID</label>
                    <input type="text" class="form-control" name="infrastructure_id">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Installation Year</label>
                    <input type="text" class="form-control" name="installation_year">
                </div>
            </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label>Installed By</label>
                <input type="text" class="form-control" name="installed_by">
            </div>
            <div class="col-md-4">
                <label>Functional Status</label>
                <select name="functional_status" class="form-control">
                    <option value="Functional">Functional</option>
                    <option value="Non-Functional">Non-Functional</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Used for Drinking</label>
                <select name="used_for_drinking" class="form-control">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Installed By</label>
                    <input type="text" class="form-control" name="installed_by">
                </div>
                <div class="col-md-6">
                    <label>Functional Status</label>
                    <select name="functional_status" class="form-control">
                        <option value="Functional">Functional</option>
                        <option value="Non-Functional">Non-Functional</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Used for Drinking</label>
                    <select name="used_for_drinking" class="form-control">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Latitude</label>
                    <input type="text" class="form-control" name="latitude">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Longitude</label>
                    <input type="text" class="form-control" name="longitude">
                </div>
            </div>
        <!-- DTW/STW fields -->
        <div class="row mt-3">
            <div class="col-md-4">
                <label>Pumping mechanism</label>
                <select name="pumping_mechanism" class="form-control">
                    <option value="">Choose an option</option>
                    <option value="Hand Pump">Hand Pump</option>
                    <option value="Motorized">Motorized</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Depth (ft)</label>
                <input type="text" class="form-control" name="depth">
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Pumping mechanism</label>
                    <select name="pumping_mechanism" class="form-control">
                        <option value="">Choose an option</option>
                        <option value="Hand Pump">Hand Pump</option>
                        <option value="Motorized">Motorized</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Depth (ft)</label>
                    <input type="text" class="form-control" name="depth">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>No. of Tanks</label>
                    <input type="text" class="form-control" name="num_tanks">
                </div>
                <div class="col-md-6">
                    <label>Tank material</label>
                    <input type="text" class="form-control" name="tank_material">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Tank capacity (l)</label>
                    <input type="text" class="form-control" name="tank_capacity">
                </div>
            </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label>Catchment Area (m2)</label>
                <input type="text" class="form-control" name="catchment_area">
            </div>
            <div class="col-md-4">
                <label>Catchment Material</label>
                <input type="text" class="form-control" name="catchment_material">
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Catchment Area (m2)</label>
                    <input type="text" class="form-control" name="catchment_area">
                </div>
                <div class="col-md-6">
                    <label>Catchment Material</label>
                    <input type="text" class="form-control" name="catchment_material">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Production capacity (litres per hour)</label>
                    <input type="text" class="form-control" name="production_capacity">
                </div>
            </div>
        <!-- PWB fields -->
        <div class="row mt-3">
            <div class="col-md-4">
                <label>Water source</label>
                <input type="text" class="form-control" name="water_source">
            </div>
            <div class="col-md-4">
                <label>Monthly bill (Tk)</label>
                <input type="text" class="form-control" name="monthly_bill">
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Water source</label>
                    <input type="text" class="form-control" name="water_source">
                </div>
                <div class="col-md-6">
                    <label>Monthly bill (Tk)</label>
                    <input type="text" class="form-control" name="monthly_bill">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Onboarding Time</label>
                    <input type="text" class="form-control" name="onboarding_time" placeholder="QQ-YYYY">
                </div>
            </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <label>Reason for inactive status (if applicable)</label>
                <input type="text" class="form-control" name="inactive_reason">
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Reason for inactive status (if applicable)</label>
                    <input type="text" class="form-control" name="inactive_reason">
                </div>
                <div class="col-md-6">
                    <label>Comments</label>
                    <input type="text" class="form-control" name="comments">
                </div>
            </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-info">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection
