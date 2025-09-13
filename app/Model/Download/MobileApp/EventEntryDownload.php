<?php

namespace App\Model\Download\MobileApp;

use Illuminate\Http\Request;

class EventEntryDownload
{
    private $mobEntry;
    private $request;

    public function __construct($mobEntry)
    {
        $this->mobEntry = $mobEntry;
    }

    public function download()
    {
        $rows = $this->mobEntry;

    if(!count($rows))
    {
        return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    \Excel::create('Mobile-App_Report-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'Entry ID',
            'Entry Type',
            'Event Type',
            'Location',
            'CDF No',
            'Male Participants',
            'Female Participants',
            'Disabled Participants',
            'Longitude',
            'Lattitude',
            'User',
            'District',
            'Upazila',
            'Union',
            'Entry Time',
            'Upload Time',
            'Image 1',
            'Image 2',
            'Image 3',
            'Comments'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
//          $type_input = $loc_input = $cdf_input = $male_input = $female_input = $disabled_input = $comments_input = '';
          if($row->type == 'Event') {
//              $jval = json_decode(json_decode($row->answers, TRUE), TRUE);
//
//              for ($i = 0; $i < sizeof($jval); $i++) {
//                  switch ($jval[$i]['tag']) {
//                      case "Type":
//                          $type_input = $jval[$i]['answer'];
//                          break;
//                      case "Location":
//                          $loc_input = $jval[$i]['answer'];
//                          break;
//                      case "CDF No/School No":
//                          $cdf_input = $jval[$i]['answer'];
//                          break;
//                      case "Nos of Men/Boy":
//                          $male_input = $jval[$i]['answer'];
//                          break;
//                      case "Nos of Women/Girl":
//                          $female_input = $jval[$i]['answer'];
//                          break;
//                      case "Nos of Disabled":
//                          $disabled_input = $jval[$i]['answer'];
//                          break;
//                      case "Comments":
//                          $comments_input = $jval[$i]['answer'];
//                          break;
//                      default:
//                  }
//              }


          }

          $sheet->row($rowIndex, [
            $row->id,
            $row->type ,
            $row->events['ev_name'] ,
            $row->events['ev_loc'] ,
            $row->events['ev_cdf'] ,
            $row->events['ev_male'] ,
            $row->events['ev_female'] ,
            $row->events['ev_disable'] ,
            $row->longitude ,
            $row->latitude ,
            $row->user->email." (".$row->user->name.")",
            $row->union->upazila->district->distname ,
            $row->union->upazila->upname ,
            $row->union->unname ,
            $row->inserted_at ,
            $row->created_at,
            $row->image1,
            $row->image2,
            $row->image3,
            $row->events['comments'] ,
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
