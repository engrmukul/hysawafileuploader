        
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fa fa-edit"></i> Edit Infrastructure Details <span class="badge bg-success">ID: {{ $infrastructure->id ?? '' }}</span> <span class="badge bg-info">Active</span></h4>
        <a href="{{ route('institution.edit', ['id' => $infrastructure->institution_id ?? null]) }}" class="btn btn-secondary">Back</a>
    </div>
    <form action="{{ route('water-point.update', $infrastructure->id ?? '') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>Installation Year</label>
                <input type="text" name="installation_year" class="form-control" value="{{ $infrastructure->installation_year ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Installed By</label>
                <input type="text" name="installed_by" class="form-control" value="{{ $infrastructure->installed_by ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Infrastructure Type</label>
                <select name="infrastructure_type" class="form-control">
                    <option value="DTW" {{ (isset($infrastructure->infrastructure_type) && $infrastructure->infrastructure_type=='DTW') ? 'selected' : '' }}>DTW</option>
                    <option value="STW" {{ (isset($infrastructure->infrastructure_type) && $infrastructure->infrastructure_type=='STW') ? 'selected' : '' }}>STW</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Functional Status</label>
                <select name="functional_status" class="form-control">
                    <option value="Functional" {{ (isset($infrastructure->functional_status) && $infrastructure->functional_status=='Functional') ? 'selected' : '' }}>Functional</option>
                    <option value="Non-Functional" {{ (isset($infrastructure->functional_status) && $infrastructure->functional_status=='Non-Functional') ? 'selected' : '' }}>Non-Functional</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Used for Drinking</label>
                <select name="used_for_drinking" class="form-control">
                    <option value="Yes" {{ (isset($infrastructure->used_for_drinking) && $infrastructure->used_for_drinking=='Yes') ? 'selected' : '' }}>Yes</option>
                    <option value="No" {{ (isset($infrastructure->used_for_drinking) && $infrastructure->used_for_drinking=='No') ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" value="{{ $infrastructure->latitude ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" value="{{ $infrastructure->longitude ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Pumping mechanism</label>
                <select name="pumping_mechanism" class="form-control">
                    <option value="">Choose an option</option>
                    <option value="Hand Pump" {{ (isset($infrastructure->pumping_mechanism) && $infrastructure->pumping_mechanism=='Hand Pump') ? 'selected' : '' }}>Hand Pump</option>
                    <option value="Motorized" {{ (isset($infrastructure->pumping_mechanism) && $infrastructure->pumping_mechanism=='Motorized') ? 'selected' : '' }}>Motorized</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Depth (ft)</label>
                <input type="text" name="depth" class="form-control" value="{{ $infrastructure->depth ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Onboarding Time</label>
                <input type="text" name="onboarding_time" class="form-control" placeholder="QQ-YYYY" value="{{ $infrastructure->onboarding_time ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3">
                <label>Functional Status</label>
                <select name="functional_status" class="form-control">
                    <option value="Functional" {{ (isset($infrastructure->functional_status) && $infrastructure->functional_status=='Functional') ? 'selected' : '' }}>Functional</option>
                    <option value="Non-Functional" {{ (isset($infrastructure->functional_status) && $infrastructure->functional_status=='Non-Functional') ? 'selected' : '' }}>Non-Functional</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Used for Drinking</label>
                <select name="used_for_drinking" class="form-control">
                    <option value="Yes" {{ (isset($infrastructure->used_for_drinking) && $infrastructure->used_for_drinking=='Yes') ? 'selected' : '' }}>Yes</option>
                    <option value="No" {{ (isset($infrastructure->used_for_drinking) && $infrastructure->used_for_drinking=='No') ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" value="{{ $infrastructure->latitude ?? '' }}">
            </div>
            <div class="col-md-3">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" value="{{ $infrastructure->longitude ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3">
                <label>Pumping mechanism</label>
                <select name="pumping_mechanism" class="form-control">
                    <option value="">Choose an option</option>
                    <option value="Hand Pump" {{ (isset($infrastructure->pumping_mechanism) && $infrastructure->pumping_mechanism=='Hand Pump') ? 'selected' : '' }}>Hand Pump</option>
                    <option value="Motorized" {{ (isset($infrastructure->pumping_mechanism) && $infrastructure->pumping_mechanism=='Motorized') ? 'selected' : '' }}>Motorized</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Depth (ft)</label>
                <input type="text" name="depth" class="form-control" value="{{ $infrastructure->depth ?? '' }}">
            </div>
            <div class="col-md-3">
                <label>Onboarding Time</label>
                <input type="text" name="onboarding_time" class="form-control" placeholder="QQ-YYYY" value="{{ $infrastructure->onboarding_time ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <label>Reason for Inactive status (if applicable)</label>
                <input type="text" name="inactive_reason" class="form-control" value="{{ $infrastructure->inactive_reason ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="renovation_required" id="renovation_required" {{ !empty($infrastructure->renovation_required) ? 'checked' : '' }}>
                    <label class="form-check-label text-success" for="renovation_required">
                        Check if renovation is required
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="major_om_required" id="major_om_required" {{ !empty($infrastructure->major_om_required) ? 'checked' : '' }}>
                    <label class="form-check-label text-success" for="major_om_required">
                        Check if major O&M is required
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <label>O&M / Renovation Requirements</label>
                <input type="text" name="om_renovation_requirements" class="form-control" value="{{ $infrastructure->om_renovation_requirements ?? '' }}">
            </div>
        </div>

        <!-- Technical Assessment Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="p-3 mb-3" style="background:#ffe5b4; border-radius:6px;">
                    <h5 class="mb-3" style="background:#f7b84b; color:#fff; padding:8px 16px; border-radius:4px;">Technical Assessment</h5>
                    <div class="mb-3 p-3" style="background:#f5f5f5; border-radius:6px;">
                        <label class="mb-2" style="font-weight:600; color:#19706a;">Does this water point need any repair/renovation?</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="needs_repair" id="needs_repair_yes" value="Yes" {{ (isset($infrastructure->needs_repair) && $infrastructure->needs_repair=='Yes') ? 'checked' : '' }}>
                            <label class="form-check-label" for="needs_repair_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="needs_repair" id="needs_repair_no" value="No" {{ (isset($infrastructure->needs_repair) && $infrastructure->needs_repair=='No') ? 'checked' : '' }}>
                            <label class="form-check-label" for="needs_repair_no">No</label>
                        </div>
                    </div>
                    <div class="mb-3 p-3" style="background:#f5f5f5; border-radius:6px;">
                        <label class="mb-2" style="font-weight:600; color:#19706a;">What types of repair/renovation needed?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="repair_types[]" id="repair_tap_stand" value="Tap stand with platform" {{ (isset($infrastructure->repair_types) && is_array($infrastructure->repair_types) && in_array('Tap stand with platform', $infrastructure->repair_types)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="repair_tap_stand">Tap stand with platform</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="repair_types[]" id="repair_pipeline" value="Connecting Pipeline" {{ (isset($infrastructure->repair_types) && is_array($infrastructure->repair_types) && in_array('Connecting Pipeline', $infrastructure->repair_types)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="repair_pipeline">Connecting Pipeline</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="repair_types[]" id="repair_drainage" value="Drainage system" {{ (isset($infrastructure->repair_types) && is_array($infrastructure->repair_types) && in_array('Drainage system', $infrastructure->repair_types)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="repair_drainage">Drainage system</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="repair_types[]" id="repair_tap" value="Tap" {{ (isset($infrastructure->repair_types) && is_array($infrastructure->repair_types) && in_array('Tap', $infrastructure->repair_types)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="repair_tap">Tap</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="p-3 mb-3" style="background:#e3f2fd; border-radius:6px;">
                    <h5 class="mb-3" style="background:#1976d2; color:#fff; padding:8px 16px; border-radius:4px;">Water Point Assessment</h5>
                    <div class="mb-3 p-3" style="background:#f5f5f5; border-radius:6px;">
                        <label class="mb-2" style="font-weight:600; color:#19706a;">Is the water point functional?</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_functional" id="is_functional_yes" value="Yes" {{ (isset($infrastructure->is_functional) && $infrastructure->is_functional=='Yes') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_functional_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_functional" id="is_functional_no" value="No" {{ (isset($infrastructure->is_functional) && $infrastructure->is_functional=='No') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_functional_no">No</label>
                        </div>
                        <div class="mt-2">
                            <label>If not, why?</label>
                            <input type="text" name="not_functional_reason" class="form-control" value="{{ $infrastructure->not_functional_reason ?? '' }}">
                        </div>
                    </div>
                    <div class="mb-3 p-3" style="background:#f5f5f5; border-radius:6px;">
                        <label class="mb-2" style="font-weight:600; color:#19706a;">Is the water point accessible to all?</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_accessible" id="is_accessible_yes" value="Yes" {{ (isset($infrastructure->is_accessible) && $infrastructure->is_accessible=='Yes') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_accessible_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_accessible" id="is_accessible_no" value="No" {{ (isset($infrastructure->is_accessible) && $infrastructure->is_accessible=='No') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_accessible_no">No</label>
                        </div>
                        <div class="mt-2">
                            <label>If not, why?</label>
                            <input type="text" name="not_accessible_reason" class="form-control" value="{{ $infrastructure->not_accessible_reason ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label>Is the platform damaged?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="platform_damaged" id="platform_damaged_yes" value="Yes" {{ (isset($infrastructure->platform_damaged) && $infrastructure->platform_damaged=='Yes') ? 'checked' : '' }}>
                    <label class="form-check-label" for="platform_damaged_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="platform_damaged" id="platform_damaged_no" value="No" {{ (isset($infrastructure->platform_damaged) && $infrastructure->platform_damaged=='No') ? 'checked' : '' }}>
                    <label class="form-check-label" for="platform_damaged_no">No</label>
                </div>
            </div>
            <div class="col-md-4">
                <label>Is the drainage system working?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="drainage_working" id="drainage_working_yes" value="Yes" {{ (isset($infrastructure->drainage_working) && $infrastructure->drainage_working=='Yes') ? 'checked' : '' }}>
                    <label class="form-check-label" for="drainage_working_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="drainage_working" id="drainage_working_no" value="No" {{ (isset($infrastructure->drainage_working) && $infrastructure->drainage_working=='No') ? 'checked' : '' }}>
                    <label class="form-check-label" for="drainage_working_no">No</label>
                </div>
            </div>
            <div class="col-md-4">
                <label>Is the tap leaking?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tap_leaking" id="tap_leaking_yes" value="Yes" {{ (isset($infrastructure->tap_leaking) && $infrastructure->tap_leaking=='Yes') ? 'checked' : '' }}>
                    <label class="form-check-label" for="tap_leaking_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tap_leaking" id="tap_leaking_no" value="No" {{ (isset($infrastructure->tap_leaking) && $infrastructure->tap_leaking=='No') ? 'checked' : '' }}>
                    <label class="form-check-label" for="tap_leaking_no">No</label>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <label>Comments</label>
                <input type="text" name="comments" class="form-control" value="{{ $infrastructure->comments ?? '' }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="assessment_submitted" id="assessment_submitted" {{ !empty($infrastructure->assessment_submitted) ? 'checked' : '' }}>
                    <label class="form-check-label" for="assessment_submitted">
                        Check if technical assessment data 2025 submitted
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-info">Update Data</button>
            </div>
        </div>
    </form>
</div>
@endsection
