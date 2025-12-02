<?php

namespace App\Model\Download\TrainingAgency\Training;

use Illuminate\Http\Request;

class ParticipantDownload
{
  public function download()
  {
     $rows = \DB::select(
                \DB::raw("SELECT
                  tbl_training.TrgTitle,
                  tbl_training.TrgVenue,
                  tbl_training.TrgFrom,
                  tbl_training.TrgTo,
                  tbl_trg_participants.partcipant_name,
                  tbl_trg_participants.designation,
                  fdistrict.distname,
                  fupazila.upname,
                  funion.unname,
                  tbl_trg_participants.mobile_no,
                  tbl_trg_participants.email_address,
                  tbl_trg_participants.participant_id,
                  tbl_trg_participants.region_id,
                  tbl_training.Agency,
                  trg_title.title,
                  tbl_venue.VenueName,
                  trg_desg.desg,
                  trg_agency.agency_name
                  FROM (((tbl_trg_participants
                  LEFT JOIN fdistrict ON tbl_trg_participants.district_id = fdistrict.id)
                  LEFT JOIN fupazila ON tbl_trg_participants.upazila_id = fupazila.id)
                  LEFT JOIN funion ON tbl_trg_participants.union_id = funion.id)
                  LEFT JOIN tbl_training ON tbl_trg_participants.TrgCode = tbl_training.TrgCode
                  LEFT JOIN trg_agency ON trg_agency.id = tbl_training.Agency
                  LEFT JOIN trg_title ON trg_title.id = tbl_training.TrgTitle
                  LEFT JOIN tbl_venue ON tbl_venue.VenueID = tbl_training.TrgVenue
                  LEFT JOIN trg_desg ON trg_desg.id = tbl_trg_participants.designation
                  WHERE tbl_training.proj_id = ".auth()->user()->proj_id
                  )
              );

    \Excel::create('Training-Participant-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'Organized by/Agency',
            'Training Title',
            'Venue',
            'Partcipant name',
            'Designation',
            'District',
            'Upazila',
            'Union',
            'Mobile no',
            'Email'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->agency_name ,
            $row->title ,
            $row->VenueName ,
            $row->partcipant_name ,

            $row->desg ,
            $row->distname ,
            $row->upname ,
            $row->unname ,

            $row->mobile_no ,
            $row->email_address
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
