<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SocialTecnology;
use App\CategoryAwardEvaluationStep;
use App\Services\Admin\SocialTecnologies\SocialTecnologyService;
use PDF;

class PdfGenerateController extends Controller
{
    protected $socialTecnologyService;

    public function __construct()
    {
        $this->socialTecnologyService = new SocialTecnologyService();
    }

    public function pdfview(Request $request){

       $socialtecnology = SocialTecnology::find($request['id']);
       $socialtecnologyImages = $this->socialTecnologyService->getSocialTecnologyBase64Images($socialtecnology);

        if($request->has('download')) {
            // pass view file
           $pdf = PDF::loadView('admin.social-tecnology._form_show', [
                'socialtecnology' => $socialtecnology,
                'socialtecnologyImages' => $socialtecnologyImages
           ])
           ->setOption("encoding","UTF-8");
            // download pdf
           return $pdf->inline('userlist.pdf');
        }

    }

    public function generate_pdf_awards_winners_by_step (CategoryAwardEvaluationStep $evaluationStep)
    {
        $pdf = PDF::loadView('admin.awards.audition.pdfCategoryAwardWinnersEvaluations',compact('evaluationStep'))
        ->setOption("encoding","UTF-8");

        // dd( $pdf );
        // download pdf
        return $pdf->inline('categoryAwardWinners.pdf');
        // return view('admin.awards.audition.pdfCategoryAwardWinnersEvaluations', compact('evaluationStep'));
    }

    public function pdf_socialTecnology_from_Challenge_CategoryAward(Request $request, SocialTecnology $socialTecnology)
    {
        $pdf = PDF::loadView('admin.social-tecnology._pdf_challenge_socialTecnology', [
            'socialtecnology' => $socialTecnology,
        ])->setOption("encoding","UTF-8");

        return $pdf->inline('socialTecnology.pdf');
    }
}
