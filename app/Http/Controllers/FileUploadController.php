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
        $this->username = base64_decode($this->username);
        $this->user = DB::table('users')->where('email', $this->username)->first();
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


        //get role from role_user table
        $role = DB::table('role_user')->where('user_id', $this->user->id)->first();
        if (!$role || !in_array($role->role_id, [14, 15])) {
            return view('errors.user_not_found', ['message' => 'User role not found']);
        }

        $districts = DB::table('fdistrict')
            ->whereIn('id', [41, 32, 7])
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
            'userId' => $this->user->id
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
        $request->merge([
            'institution_latitude' => trim($request->input('institution_latitude')),
            'institution_longitude' => trim($request->input('institution_longitude')),
        ]);


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
                'institution_latitude' => 'required',
                'institution_longitude' => 'required'
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
                foreach ($request->file('files') as $file) {
                    $image = Image::make($file)->resize(800, 600, function ($constraint) {
        $constraint->aspectRatio(); // Keeps the original aspect ratio
        $constraint->upsize();      // Prevents upsizing if image is smaller than target
    })->encode('jpg', 90);
                    $filename = $institution->institution_id . '.jpg';
                    $path = 'sp_satkhira_inst/' . $filename;
                    Storage::disk('mis_uploads')->put($path, $image);
                }
                $data['img9'] = $filename;
            }

            DB::table('sp_school')->where('id', $request->institution_id)->update($data);

    }


        if ($request->upload_type == 'infrastructure') {
                $infrastructure = DB::table('sp_infrastructure')->where('id', $request->infrastructure_id)->first();

        $data = [];

          if ($request->file('files')){
                foreach ($request->file('files') as $file) {
                    // Convert to jpg
                    $image = Image::make($file)->resize(800, 600, function ($constraint) {
        $constraint->aspectRatio(); // Keeps the original aspect ratio
        $constraint->upsize();      // Prevents upsizing if image is smaller than target
    })->encode('jpg', 90);
                    $filename = $infrastructure->water_id . '.jpg';
                    $path = 'sp_satkhira_infras/' . $filename;
                    \Storage::disk('mis_uploads')->put($path, $image); //will change
                }
                $data['image'] = $filename;
                DB::table('sp_infrastructure')->where('id', $request->infrastructure_id)->update($data);
          } else {
              return redirect()->back()->with('success', 'No Image Selected.');
          }

        }


        if ($request->upload_type == 'inspection') {
            $sanitaryInspection = DB::table('sp_san_inspection_v2')->where(['infrastructure_id' => $request->infrastructure_id, 'inspection_date' => $request->inspection_date ])->first();

            $uploadedImages = [];
            foreach ($request->file('files') as $key => $file) {
                // Convert to jpg
                $image = Image::make($file)->resize(800, 600, function ($constraint) {
        $constraint->aspectRatio(); // Keeps the original aspect ratio
        $constraint->upsize();      // Prevents upsizing if image is smaller than target
    })->encode('jpg', 90);
                $filename =  time() . '_' . $key . '.jpg';
                $path = 'sp_si_img/' . $filename;
                \Storage::disk('mis_uploads')->put($path, $image);

                $uploadedImages[] = 'upload/sp_si_img/'.$filename;
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
                if($role_id == '14')
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
            $inspectionDates =  \DB::table('sp_san_inspection_v2')
            ->where('infrastructure_id', $infrastructure_id)
            ->groupBy('inspection_date')
            ->get(['inspection_date']);

        return response()->json($inspectionDates);
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
