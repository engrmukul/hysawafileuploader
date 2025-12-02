<?php

namespace App\Model\Search;

use App\CoApplications;
use Illuminate\Http\Request;

class CoAppSearch
{
  private $datas;
  private $request;
  private $pagination;
  private $qualifiedData;
  private $unqualifiedData;
  private $rejectedData;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->datas;
    $this->qualifiedData;
    $this->unqualifiedData;
    $this->rejectedData;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = CoApplications::where(function($query) use($request) {

        $education_search =  $request->input('education_search');
        $app_status_search = $request->input('app_status_search');
        $any_union_search = $request->input('any_union_search');
        $experience_search = $request->input('experience_search');
        $up_search = $request->input('up_search');
        $un_search = $request->input('un_search');
        $mobile_no = $request->input('mobile_no');

//      if (!empty($up_search) && $up_search != 'All') {
//        $query->where('choice1_up', $up_search);
//      }

      if (auth()->user()->email == 'motiaricar') {
            $query->where('choice1_up', "Ukhiya");
        } else if (auth()->user()->email == 'annaicar') {
          $query->where('choice1_up', "Ramu");
        } else if (auth()->user()->email == 'farukicar') {
            $query->where('choice1_up', "Teknaf");
        } else if (auth()->user()->email == 'hamidicar') {
            $query->where('choice1_up', "Cox's Bazar Sadar");
        } else {
            if (!empty($up_search) && $up_search != 'All') {
                $query->where('choice1_up', $up_search);
            }
       }


    if(!empty($mobile_no)) {

        if(strlen($mobile_no) < 10){
            $query->where('id', 'LIKE', '%'.$mobile_no.'%');
        } else {
            $query->where('mobile', 'LIKE', '%'.$mobile_no.'%');
        }
    } else {
        if(!empty($education_search))
        {
            $query->where('education', $education_search);
        }

        if(!empty($experience_search))
        {
            $query->where('experience', $experience_search);
        }

        if(!empty($un_search) && $un_search != 'All')
        {
            $query->where('choice1', $un_search);
        }

        if(!empty($education_search) || !empty($app_status_search) || !empty($experience_search) ||
            (!empty($up_search) && $up_search != 'All') || (!empty($un_search) && $un_search != 'All') || !$this->pagination){
            $query->where('is_approved', '>', 0);
        } else {
            //$query->where('is_approved','1');
            $query->where('is_approved', '>', 0);
        }

        if(!empty($any_union_search)) {
            $query->where('choice_any', $any_union_search);
        }

        if(!empty($app_status_search)) {
            if($app_status_search == "Not Qualified"){
                $query->where('app_status', '!=', 'Qualified')->where('app_status', '!=', 'Recruited');
            }else if($app_status_search == "Not Verified"){
                $query->where('app_status', NULL)
                    ->orWhere('app_status', 'not_verified')
                    ->orWhere('app_status', 'not_selected');
            } else {
                $query->where('app_status', $app_status_search);
            }
        }
    }

    })
    ->orderBy('id', 'DESC')
    ->orderBy('is_approved', 'ASC')
    ->orderBy('experience_wash', 'DESC');

    if($this->pagination){
      $this->datas = $q->paginate(20);
    }else{
      $this->datas = $q->get();
    }
      $this->qualifiedData = $q->where('app_status', 'Qualified')->get();
      $this->unqualifiedData = $q->where('app_status', 'Unqualified')->get();
      $this->rejectedData = $q->where('app_status', 'Rejected')->get();

  }

  public function get()
  {
    return $this->datas;
  }

    public function qualifiedData()
    {
        return $this->qualifiedData;
    }

    public function unqualifiedData()
    {
        return $this->unqualifiedData;
    }

    public function rejectedData()
    {
        return $this->rejectedData;
    }

}
