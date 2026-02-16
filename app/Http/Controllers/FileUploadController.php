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
    protected $school_id;
    protected $upload_type;
    protected $water_id;
    protected $id;

    //add constructor and get query parameter
    public function __construct(Request $request)
    {
        $this->username = $request->query('username');
        $this->username = base64_decode($this->username);
        $this->user = DB::table('users')->where('email', $this->username)->first();
        $this->upload_type = $request->query('upload_type');
        $this->school_id = $request->query('school_id');
        $this->water_id = $request->query('water_id');
        $this->id = $request->query('id');
    }

    /**
     * Show the file upload form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {


        if (!$this->user) {
            return view('errors.user_not_found', ['message' => 'User not found']);
        }

        $institutionDetails = '';

        $sanv2 = '';

        if($this->id){
            $sanv2 = DB::table('sp_san_inspection_v2')
                ->where('id', $this->id)
                ->first();

            $infrastructure = DB::table('sp_infrastructure')
                ->where('id', $sanv2->infrastructure_id)
                ->first();

            $institutionDetails = DB::table('sp_school')
                ->where('id', $infrastructure->school_id)
                ->first();

        }

        //GET SINGLE DATA FROM sp_school TABLE BASED ON school_id
        if ($this->school_id) {
            $institutionDetails = DB::table('sp_school')
                ->where('id', $this->school_id)
                ->first();
        }


        //get role from role_user table
        $role = DB::table('role_user')->where('user_id', $this->user->id)->first();


        // if (!$role || in_array($role->role_id, [14, 15])) {
        if (!$role || in_array($role->role_id, [14])) {
            return view('errors.user_not_found', ['message' => 'User role not found']);
        }

        $districts = DB::table('fdistrict')
            ->whereIn('id', [41, 32, 7, 6])
            ->get();
        $upazilas = \DB::table('fupazila')->where('disid', $institutionDetails->distid ?? '')->get(['id', 'upname']);
        $unions = DB::table('funion')->where('upid', $institutionDetails->upid ?? '')->get(['id', 'unname']);
        $institutionTypes = DB::table('sp_school')
            ->select('sch_type_edu')
            ->groupBy('sch_type_edu')
            ->get();

       $institutions = [];
        if ($institutionDetails) {
            $institutions = DB::table('sp_school')->where('id', $institutionDetails->id)->get();
        }

        $infrastructures = [];
        if ($institutionDetails) {
            $infrastructures = DB::table('sp_infrastructure')->where('school_id', $institutionDetails->id)->get();
        }

        $imageType = '';
        $ist_inf_id = '';
        if($this->upload_type == 'institute'){
            $imageType = 'INS';
            $ist_inf_id = $institutionDetails->id;
        }
         if($this->upload_type == 'infrastructure'){
            $imageType = 'INF';
             $result = DB::table('sp_infrastructure')
                 ->where('water_id', $this->water_id)
                 ->first();
             $ist_inf_id = $result->id ?? '';
         }

         $allImages =  DB::table('sp_images')
        ->where('ist_inf_id', '=',$ist_inf_id)
        ->where('image_type','=', $imageType)
        ->get();


        return view('file_upload_form', [
            'districts' => $districts,
            'upazilas' => $upazilas,
            'unions' => $unions,
            'institutionTypes' => $institutionTypes,
            'institutions' => $institutions,
            'infrastructures' => $infrastructures,
            'userId' => $this->user->id,
            'uploadType' => empty($this->upload_type) ? 'inspection' : $this->upload_type,
            'institutionDetails' => $institutionDetails,
            'waterId' => (isset($this->water_id) && $this->upload_type == 'infrastructure') ? $this->water_id : ($sanv2->water_id ?? ''),
            'allImages' => $allImages,
            'sanv2' => $sanv2,
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
        // $request->merge([
        //     'institution_latitude' => trim($request->input('institution_latitude')),
        //     'institution_longitude' => trim($request->input('institution_longitude')),
        // ]);


        try {
            $validator = \Validator::make($request->all(), [
                'upload_type' => 'required|string',
                'district' => 'required|integer',
                'upazila' => 'required|integer',
                'union' => 'required|integer',
                'institution_type' => 'nullable|string',
                'institution_id' => 'nullable|integer',
                'infrastructure_id' => 'nullable|integer',
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:jpg,jpeg,png|max:102400',
               // 'institution_latitude' => 'required',
               // 'institution_longitude' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }



        if ($request->upload_type == 'institute') {

            $institution = DB::table('sp_school')->where('id', $request->institution_id)->first();



            $data = [
                'sch_name_en' => $request->institution_name ?? $institution->institution_name,
                // 'sch_name_bn' => $request->institution_name_1_bn ?? $institution->institution_name_1_bn,
                'lat' => $request->institution_latitude ?? $institution->institution_latitude,
                'lon' => $request->institution_longitude ?? $institution->institution_longitude,
            ];

            if ($request->hasFile('files')) {
                $prevPath = '';
                foreach ($request->file('files') as $file) {
                    $image = Image::make($file)->resize(800, 600, function ($constraint) {
                        $constraint->aspectRatio(); // Keeps the original aspect ratio
                        $constraint->upsize();      // Prevents upsizing if image is smaller than target
                    })->encode('jpg', 90);
                    $filename = $institution->institution_id . '_'.time().'.jpg';

                    //IF DISTRICT IS KHULNA THEN SAVE IN khulna_uploads
                    if ($institution->distid == 6) {
                        $path = 'SafePani_School_Baseline_Photo/' . $filename;

                        // If file exists, rename existing file with suffix _prev9 (avoid collision by timestamp if needed)
                        if (\Storage::disk('mis_khulna_uploads')->exists($path)) {
                            $pathInfo = pathinfo($path);
                            $extension = $pathInfo['extension'] ?? 'jpg';
                            $prevPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_prev9.' . $extension;

                            if (\Storage::disk('mis_khulna_uploads')->exists($prevPath)) {
                                $prevPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_prev9_' . time() . '.' . $extension;
                            }

                            \Storage::disk('mis_khulna_uploads')->move($path, $prevPath);
                        }

                        \Storage::disk('mis_khulna_uploads')->put($path, $image);
                    } else {
                        $path = 'sp_satkhira_inst/' . $filename;
                        \Storage::disk('mis_uploads')->put($path, $image);
                    }
                }
                $data['img9'] = $filename;
            }

            // preserve existing img9 into img9_prev then update img9 with new filename
            if ($prevPath) {
                $data['img9_prev'] = $prevPath;
            }
            $data['img9'] = $filename;
            DB::table('sp_school')->where('id', $request->institution_id)->update($data);

            // Save image record in sp_images table
            DB::table('sp_images')->insert([
                'ist_inf_id' => $request->institution_id,
                'image_type' => 'INS',
                'image' => $data['img9'],
                'updated_at' => now(),
            ]);

        }


        if ($request->upload_type == 'infrastructure') {
            $infrastructure = DB::table('sp_infrastructure')->where('id', $request->infrastructure_id)->first();

            // Manually get school.distid
            $school = DB::table('sp_school')->where('id', $infrastructure->school_id)->first();
            $distId = $school->distid ?? null;

            $data = [];

            if ($request->file('files')) {
                foreach ($request->file('files') as $file) {
                    // Convert to jpg
                    $image = Image::make($file)->resize(800, 600, function ($constraint) {
                        $constraint->aspectRatio(); // Keeps the original aspect ratio
                        $constraint->upsize();      // Prevents upsizing if image is smaller than target
                    })->encode('jpg', 90);
                    $filename = $infrastructure->water_id . '_'.time().'.jpg';

                    //IF DISTRICT IS KHULNA THEN SAVE IN khulna_uploads
                    if ($distId == 6) {
                        $path = 'SafePani_Waterpoints_Photo/' . $filename;
                        \Storage::disk('mis_khulna_uploads')->put($path, $image);
                    } else {
                        $path = 'sp_satkhira_infras/' . $filename;
                        \Storage::disk('mis_uploads')->put($path, $image);
                    }
                }
                $data['image'] = $filename;
                DB::table('sp_infrastructure')->where('id', $request->infrastructure_id)->update($data);

                // Save image record in sp_images table
                DB::table('sp_images')->insert([
                    'ist_inf_id' => $request->infrastructure_id,
                    'image_type' => 'INF',
                    'image' => $data['image'],
                    'updated_at' => now(),
                ]);

            } else {
                return redirect()->back()->with('success', 'No Image Selected.');
            }

        }


        if ($request->upload_type == 'inspection') {
            $sanitaryInspection = DB::table('sp_san_inspection_v2')->where(['infrastructure_id' => $request->infrastructure_id, 'inspection_date' => $request->inspection_date])->first();

            // Manually get school.distid
            $school = DB::table('sp_school')->where('id', $sanitaryInspection->school_id)->first();
            $distId = $school->distid ?? null;

            $uploadedImages = [];
            foreach ($request->file('files') as $key => $file) {
                // Convert to jpg
                $image = Image::make($file)->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio(); // Keeps the original aspect ratio
                    $constraint->upsize();      // Prevents upsizing if image is smaller than target
                })->encode('jpg', 90);
                $filename = time() . '_' . $key . '.jpg';


                 //IF DISTRICT IS KHULNA THEN SAVE IN khulna_uploads
                $path = 'sp_si_img/' . $filename;
                \Storage::disk('mis_uploads')->put($path, $image);
                $uploadedImages[] = 'upload/sp_si_img/' . $filename;

            }

            DB::table('sp_san_inspection_v2')->where('id', $sanitaryInspection->id)->update([
                'image1' => $uploadedImages[0] ?? null,
                'image2' => $uploadedImages[1] ?? null,
                'image3' => $uploadedImages[2] ?? null,
            ]);

        }

        return redirect()
            ->back()
            ->with('success', 'Files uploaded and information updated successfully.');
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
    public function getInstitutions($union_id, $institution_type, $user_id)
    {
        $role = DB::table('role_user')->where('user_id', $user_id)->first();
        $role_id = $role->role_id;
        $institutions = \DB::table('sp_school')
            ->where('unid', $union_id)
            ->where('sch_type_edu', $institution_type)
            ->where(function ($query) use ($user_id, $role_id) {
                if ($role_id == '14')
                    $query->where('created_by', $user_id);
            })
            ->get(['id', 'sch_name_en', 'lat', 'lon', 'img9']);

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


    public function getInspectionDate($infrastructure_id)
    {
        $inspectionDates = \DB::table('sp_san_inspection_v2')
            ->where('infrastructure_id', $infrastructure_id)
            ->groupBy('inspection_date')
            ->get(['inspection_date']);

        $allImages =  DB::table('sp_images')
            ->where('ist_inf_id', '=',$infrastructure_id)
            ->where('image_type','=', 'INF')
            ->get(['image']);

        $data = [
            'inspection_dates' => $inspectionDates,
            'all_images' => $allImages,
        ];

        return response()->json($data);
    }

    public function getInspactionImages($infrastructure_id, $inspaction_date)
    {
        $inspectionImages = \DB::table('sp_san_inspection_v2')
            ->where('infrastructure_id', $infrastructure_id)
            ->where('inspection_date', $inspaction_date)
            ->first(['image1', 'image2', 'image3']);

        if (!$inspectionImages) {
            return response()->json(['error' => 'No inspection images found'], 404);
        }

        return response()->json($inspectionImages);
    }

}
