<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use Smalot\PdfParser\Parser;
use TCPDF;
use GuzzleHttp\Client;

class PdfParserController extends Controller
{
    //
    public function test() {


        $parser = new Parser();
        $pdf = $parser->parseFile('C:\Users\Admin\Desktop\hihi\b.pdf');
        $text = $pdf->getPages()[0]->getText();
        $tables = $pdf->getTables();
        dd($tables);

    }

    public function sign() {
        echo "<h1>test action ký số</h1>";
        // Kiểm tra file PDF từ yêu cầu người dùng
        return view("sign-pdf");
    
    }

    public function signPdf(Request $request)
    {
        // Kiểm tra file PDF từ yêu cầu người dùng
        $pdfFile = $request->file('pdf');
        if (!$pdfFile) {
            return response()->json(['error' => 'PDF file is required'], 400);
        }

        // Tạo đối tượng TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();

        // Đọc nội dung file PDF hiện tại
        $pdfContent = file_get_contents($pdfFile->getRealPath());
        $pdf->writeHTML($pdfContent);

        // Đường dẫn file .p12 và mật khẩu của nó
        $p12FilePath = 'D:\xampp\htdocs\example-app\resources\test\024700233_214454.p12';
        $p12Password = '214454';

        // Đọc nội dung file .p12
        $p12Content = file_get_contents($p12FilePath);
        // Chuyển đổi .p12 thành khóa riêng và chứng chỉ
        if (!openssl_pkcs12_read($p12Content, $certs, $p12Password)) {
            return response()->json(['error' => 'Cannot read the .p12 file or password is incorrect'], 500);
        }

        // Lấy khóa riêng và chứng chỉ từ mảng $certs
        $privateKey = $certs['pkey']; // Khóa riêng
        $certificate = $certs['cert']; // Chứng chỉ số

        // Sử dụng OpenSSL để ký dữ liệu
        openssl_sign($pdfContent, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Chèn chữ ký vào file PDF (tọa độ và kích thước tùy chỉnh)
        $signatureX = 150;
        $signatureY = 250;
        $signatureWidth = 60;
        $signatureHeight = 30;
        $pdf->Image('@' . $signature, $signatureX, $signatureY, $signatureWidth, $signatureHeight);

        // Lưu file PDF đã được ký
        $signedPdfPath = storage_path('signed_output.pdf');
        $pdf->Output($signedPdfPath, 'F');

        return response()->download($signedPdfPath, 'signed_document.pdf');
    }

    public function aicevn() {
        return view('aicevn');
    }

    public function paicevn(Request $request) {
        $list_success = $list_false = $mess = $mess_false = array();
        $code = $this->minifyChar($request->code);
        $url = "https://aicevietnam.vn/Home/PostData";
        $arr = explode(",", $code);
        $getRandomUser = new ApiController();
        $params = [
            "Phone" => $request->phone_number,
            "Provider" => "VIETEL",
            "Sub" => "TRA_TRUOC",
            "Province" => "An Giang"
        ];
        $client = new Client();
        $phase = 0;
        $picture = 0;
        foreach ($arr as $key => $a) {
            $params["Code"] = $a;
            $response = $client->request('POST', $url, [
                'json' => $params,
                'headers' => [
                    'User-Agent' => $getRandomUser->getRandomUserAgent()
                ]
            ]);
            $body = $response->getBody()->getContents();
            $res = json_decode($body, true);
            if ($res["status"] == 1) {
                $list_success[] = $a;
                $mess[] = $res;
                $mess[$key]['code'] = $a;
                $mess[$key]['picture'] = $picture;
            } else {
                $list_false[] = $a;
                $mess_false[] = $res;
                $mess_false[$key]['code'] = $a;
                $mess[$key]['picture'] = $picture;
            }
            $phase++;
            if ($phase == 11) {
                $phase = 0;
                $picture++;
                sleep(5);
            }
        }
        return response()->json(["success" => $list_success, "mess" => $mess, "false" => $list_false, "mess_false" => $mess_false]);
    }

    public function minifyChar($input) {
        // Xóa tất cả khoảng trắng trong chuỗi
        $input = str_replace(' ', '', $input);
        $input = preg_replace('/\s+/', '', $input);
        // Sử dụng hàm chunk_split để chia chuỗi thành các khối 10 ký tự và ngăn cách bằng dấu phẩy
        return rtrim(chunk_split($input, 10, ','), ',');
    }
    
}
