<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class FileUploadController extends Controller
{
    protected $username;
    protected $user;

    //add constructor and get query parameter
    public function __construct(Request $request)
    {
        $this->username = $request->query('username');
        //base64 decode
        $this->username = 'tala4';// base64_decode($this->username);
        //get user details using username
        $this->user = DB::table('users')->where('email', $this->username)->first();
    }

    /**
     * Show the file upload form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        $districts = DB::table('fdistrict')
            ->whereIn('id', [6, 7, 41])
            ->get();
        $upazilas = [];
        $unions = [];
        $institutionTypes = DB::table('sp_school')
            ->select('sch_type_edu')
            ->groupBy('sch_type_edu')
            ->get();
        $institutions = [];

        return view('file_upload_form', [
            'districts' => $districts,
            'upazilas' => $upazilas,
            'unions' => $unions,
            'institutionTypes' => $institutionTypes,
            'institutions' => $institutions,
        ]);
    }

    /**
     * Handle multiple file uploads.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'upload_type' => 'required|string',
            'district' => 'required|integer',
            'upazila' => 'required|integer',
            'union' => 'required|integer',
            'institution_type' => 'nullable|string',
            'institution_name' => 'nullable|integer',
            'infrastructure_name' => 'nullable|integer',
            'files' => 'required|array',
            'files.*' => 'file|image|max:10240', // max 10MB per file
        ]);


        if ($request->upload_type == 'institute') {
            $institution = DB::table('sp_school')->where('id', $request->institution_id)->first();



            foreach ($request->file('files') as $file) {
                // Convert to jpg
                $image = Image::make($file)->encode('jpg', 90);
                $filename = $institution->institution_id . '.jpg';
                $path = 'uploads/' . $filename;
                \Storage::disk('public')->put($path, $image); //will change
            }



            DB::table('sp_school')->where('id', $request->institution_id)->update([
                'sch_name_en' => $request->institution_name_1 ?? $institution->institution_name_1,
                'sch_name_bn' => $request->institution_name_1_bn ?? $institution->institution_name_1_bn,
                'latitude' => $request->institution_latitude ?? $institution->institution_latitude,
                'longitude' => $request->institution_longitude ?? $institution->institution_longitude,
                'img9' => $filename
            ]);
        }
        if ($request->upload_type == 'infrastructure') {
            $infrastructure = DB::table('sp_infrastructure')->where('id', $request->infrastructure_id)->first();


            foreach ($request->file('files') as $file) {
                // Convert to jpg
                $image = Image::make($file)->encode('jpg', 90);
                $filename = $infrastructure->water_id . '.jpg';
                $path = 'uploads/' . $filename;
                \Storage::disk('public')->put($path, $image); //will change
            }

            DB::table('sp_infrastructure')->where('id', $request->infrastructure_id)->update([
                'image' => $filename,
            ]);

        }
        if ($request->upload_type == 'inspection') {
            $sanitaryInspection = DB::table('sp_san_inspection_v2')->where('id', $request->sanitary_inspection_id)->first();

            $uploadedImages = [];
            foreach ($request->file('files') as $key => $file) {
                // Convert to jpg
                $image = Image::make($file)->encode('jpg', 90);
                $filename = "upload/sp_si_img/" . time() . '_' . $key . '.jpg';
                $path = 'uploads/' . $filename;
                \Storage::disk('public')->put($path, $image);

                $uploadedImages[] = $filename;
            }

            DB::table('sp_sanitary_inspection')->where('id', $request->sanitary_inspection_id)->update([
                'image1' => $uploadedImages[0] ?? null,
                'image2' => $uploadedImages[1] ?? null,
                'image3' => $uploadedImages[2] ?? null,
            ]);

        }

        return response()->json([
            'message' => 'Files uploaded and converted to JPG successfully.',
            //'files' => $uploadedFiles,
        ]);
    }


    //get upazila by district id
    public function getUpazilas($district_id)
    {
        $upazilas = \DB::table('fupazila')->where('disid', $district_id)->get(['id', 'upname']);
        return response()->json($upazilas);
    }

    //for union
    public function getUnions($upazila_id)
    {
        $unions = \DB::table('funion')->where('upid', $upazila_id)->get(['id', 'unname']);
        return response()->json($unions);
    }

    //for institutions
    public function getInstitutions($union_id, $institution_type)
    {
        $institutions = \DB::table('sp_school')
            ->where('unid', $union_id)
            ->where('sch_type_edu', $institution_type)
            ->where('created_by', $this->user->id)
            ->get();

        return response()->json($institutions);
    }


    //get getInfrastructures
    public function getInfrastructures($institution_id)
    {
        $infrastructures = \DB::table('sp_infrastructure')
            ->where('school_id', $institution_id)
            ->get(['id', 'water_id']);

        return response()->json($infrastructures);
    }

}
