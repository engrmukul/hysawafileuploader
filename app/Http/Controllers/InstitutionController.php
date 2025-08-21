<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InstitutionController extends Controller
{
    protected $institutionId;
    protected $username;
    protected $user;

    //add constructor and get query parameter
    public function __construct(Request $request)
    {
        $this->institutionId = $request->query('id');
        $this->username = $request->query('username');
        $this->username = base64_decode($this->username);
        $this->user = DB::table('users')->where('email', $this->username)->first();
    }

    public function edit(Request $request)
    {
        // Example: fetch institution by id from request or session (replace with real logic)
        // $institution = [
        //     'ownership' => 'Government',
        //     'education_type' => 'Primary School',
        //     'gender_type' => 'Girls',
        //     'name_en' => '6 Jelepara govt primary school',
        //     'name_bn' => '6 Jelepara govt primary school',
        //     'establishment_year' => '1940',
        //     'total_waterpoints' => 6,
        //     'drinking_sources' => 3,
        //     'full_functional_sources' => 5,
        //     'boy_students' => 221,
        //     'girl_students' => 224,
        //     'disable_boys' => 5,
        //     'disable_girls' => 2,
        //     'total_students' => 445,
        //     'male_staff' => 2,
        //     'female_staff' => 8,
        //     'total_staff' => 10,
        //     'respondent_name' => 'Fatima Khatun',
        //     'respondent_designation' => 'assistant teacher',
        //     'respondent_mobile' => '01922622416',
        //     'headmaster_name' => 'Sudip Kumar Ghosh',
        //     'headmaster_mobile' => '01724003999',
        //     'smc_president_name' => '',
        //     'smc_president_mobile' => '',
        //     'village_en' => 'Jelepara',
        //     'village_bn' => '',
        //     'latitude' => '22.587915',
        //     'longitude' => '88.99996166666666',
        //     'contact_email' => '',
        //     'onboarding_time' => '',
        //     'last_updated_on' => 'Q3-2025',
        //     'comments' => '',
        //     'assessment_submitted' => true,
        // ];
        //get institution by id
        $institution = DB::table('sp_school')->where('id', $this->institutionId)->first();
        return view('institution.edit', compact('institution'));
    }

    public function update(Request $request)
    {
        // TODO: Implement logic to update the institution
        // Validate and update logic here
        return redirect()->route('institution.edit')->with('success', 'Institution updated successfully.');
    }
}
