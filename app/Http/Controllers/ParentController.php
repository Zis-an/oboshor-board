<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class ParentController extends Controller
{
    function handleException($exception, $isAjax = false)
    {
        Log::emergency('File: ' . $exception->getFile() . ' Line: ' . $exception->getLine() . ' Message: ' . $exception->getMessage());

        if ($isAjax) {
            return $this->respondWithError($exception->getMessage());
        }

        return null;

    }

    /**
     * Respond the data.
     *
     * @param array $array
     * @param int $statusCode
     * @return JsonResponse
     */
    public function respondWithArray(array $array, int $statusCode = 200)
    {
        return response()->json($array, $statusCode);
    }

    /**
     * Respond the message.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */

    public function respondWithMessage(string $message, int $statusCode)
    {
        if ($statusCode == 200) {
            $status = 'success';
        } else if (in_array($statusCode, [401, 403, 404, 419, 422])) {
            $status = 'failed';
        } else {
            $status = 'error';
        }

        return $this->respondWithArray([
            'status' => $status,
            'message' => $message,
        ]);
    }

    /**
     *
     * @param $message
     * @return JsonResponse
     */

    function respondWithSuccess($message): JsonResponse
    {
        return $this->respondWithMessage($message, 200);
    }

    /**
     * Respond the error message.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function respondWithError(string $message, int $statusCode = 500)
    {
        return $this->respondWithMessage($message, $statusCode);
    }

    /**
     * @param array $errors
     * @return JsonResponse
     */

    public function validationError(array $errors): JsonResponse
    {
        $message = implode(',', $errors);
        return $this->respondWithMessage($message, 422);
    }

    function handleExport($view, $type, $data, $fileName = 'report', $orientation = 'P')
    {

        if ($type == 'pdf') {

            $html = view($view, $data);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();

            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
                        'solaimanlipi' => [
                            'R' => 'fonts/SolaimanLipi.ttf',
                            'I' => 'fonts/SolaimanLipi.ttf',
                            'useOTL' => 0xFF,
                            'useKashida' => 75
                        ]
                    ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'orientation' => $orientation,
                'format' => 'Legal',
                'setAutoBottomMargin' => 'stretch'
            ]);

            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');

            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . date('d/m/Y H:i A', strtotime(now())) . '</div>');

            $mpdf->WriteHTML($html);

            $newFileName = $fileName . '_' . now()->format('Y-m-d H:i:s');

            return $mpdf->Output($newFileName . '.pdf', 'I');

        }
        if ($type == 'excel') {
            $newFileName = $fileName . '_' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport($view, $data), $newFileName);
        }

        return null;

    }

}
