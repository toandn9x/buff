<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class ApiController extends Controller
{
    //
    public function banks() {
        $base_uri = "https://api.vietqr.io/v2/";
        $client = new Client(['base_uri' => $base_uri]);
        $response = $client->request('GET', 'banks');
        $body = $response->getBody(); // Lấy body trực tiếp dưới dạng stream
        return response()->stream(function () use ($body) {
            echo $body;
        }, 200, ['Content-Type' => 'application/json']);
    }

    public function getByTaxcode(Request $request)
    {
        $taxcode = $request->tax;
        $urlToken = 'https://masothue.com/Ajax/Token';
        $ch = curl_init($urlToken);
        curl_setopt($ch, CURLOPT_POST, 1);
        $agent = 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Mobile Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tokenResponse = curl_exec($ch);
        curl_close($ch);
        $tokenResponse = json_decode($tokenResponse);
        $token = $tokenResponse->token;

        $urlSearch = 'https://masothue.com/Search?q=' . $taxcode . '&type=auto&token=' . $token . '&force-search=1';
        $ch = curl_init($urlSearch);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $searchHtml = curl_exec($ch);
        curl_close($ch);
        try {
            $dom = new \DomDocument();
            @$dom->loadHTML($searchHtml);
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
        $xpath = new \DOMXpath($dom);

        // $tables = $xpath->query('//table[contains(@class, "table-taxinfo")]');

        // foreach ($tables as $table) {
        //     // Tìm tất cả các thẻ <td> trong bảng
        //     $tds = $xpath->query('.//td', $table);

        //     // Lặp qua các thẻ <td> và in ra giá trị của chúng
        //     foreach ($tds as $td) {
        //         $info[] = $td->nodeValue;
        //     }
        // }

        $info = [];
        $info['taxcode'] = $this->parseToArray($xpath, "//td[@itemprop='taxID']")[0];
        $info['company'] = $this->parseToArray($xpath, "//th[@itemprop='name']")[0];
        $info['address'] = $this->parseToArray($xpath, "//td[@itemprop='address']")[0];
        $info['name'] = $this->parseToArray($xpath, "//span[@itemprop='name']")[2];
        return response()->json($info);
    }

    function parseToArray($xpath, $query)
    {
        $elements = $xpath->query($query);

        if (!is_null($elements)) {
            $resultarray = array();
            foreach ($elements as $element) {
                $nodes = $element->childNodes;
                foreach ($nodes as $node) {
                    $resultarray[] = $node->nodeValue;
                }
            }
            return $resultarray;
        }
    }


    public function buff(Request $request) {
        $link_fb = urlencode($request->fb);
        $link_toptop = urlencode($request->top);
        $link_login = "https://app.mualike.net/login";
        $link_buff = "https://app.mualike.net/order";
        $account = [["user" => "toandn99", "pass" => "toandn99"], ["user" => "locdybala11@gmail.com", "pass" => "Ngodinhloc1110*"], ["user" => "hihi", "pass" => "hehe"]];
        $randomIp = $this->generateRandomIPv4();
        if (!empty($link_fb) || !empty($link_toptop)) {
            foreach ($account as $key => $a) {
                //login fist time to get token
                $client = new Client();
                $jar = new CookieJar();
                $jar->clear();
                if ($jar->getCookieByName("XSRF-TOKEN")) {
                    // Xóa cookie
                    $jar->removeCookie("XSRF-TOKEN");
                
                }
                if ($jar->getCookieByName("app_mua_like_session")) {
                    // Xóa cookie
                    $jar->removeCookie("app_mua_like_session");
                   
                }
                if ($jar->getCookieByName("remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d")) {
                    // Xóa cookie
                    $jar->removeCookie("remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d");
                  
                }
                $body = $pattern = $cookies = $token = $body_2 = $body_3 = $token_2 = NULL;
                $response = $client->request('POST', $link_login, [
                    'proxy' => $this->getRandomProxy(),
                    'cookies' => $jar,
                    'form_params' => [
                        '_token' => '',
                        'name' => $a["user"],
                        'password' => $a["pass"],
                        'remember' => 'on'
                    ],
                    // 'curl' => [
                    //     CURLOPT_INTERFACE => $randomIp, // Đặt địa chỉ IP ngẫu nhiên cho mỗi request
                    // ]
                ]);
                $body = $response->getBody()->getContents();
                $pattern = '/name="_token"\s+value="([^"]+)"/';
                preg_match($pattern, $body, $matches);
                if (isset($matches[1])) {
                    $token = $matches[1];
                } else {
                    // Nếu không tìm thấy kết quả, bỏ qua vòng lặp
                    $message[] = ["status" => 0, "user" => $a["user"], "pass" => $a["pass"], "status" => "Lỗi! Tài khoản không tồn tạiii"];
                    continue; 
                }
                $cookies = $jar->getIterator();
                // login lan 2 voi token
                $response = $client->request('POST', $link_login, [
                    'cookies' => $jar,
                    'form_params' => [
                        '_token' => $token,
                        'name' => $a["user"],
                        'password' => $a["pass"],
                        'remember' => 'on'
                    ]
                
                ]);
                $body_2 = $response->getBody()->getContents();  
                preg_match($pattern, $body_2, $matches);
                if (isset($matches[1])) {
                    $token_2 = $matches[1];
                } else {
                    // Nếu không tìm thấy kết quả, bỏ qua vòng lặp
                    $message[] = ["status" => 0, "user" => $a["user"], "pass" => $a["pass"], "status" => "Lỗi! Tài khoản không tồn tạiii"];
                    continue; 
                }
                $cookies = $jar->getIterator();

        
            
        
                if (!empty($token_2)) {
                    // call api buff 50 like
                    $form_params = ["_token" => $token_2, "post_link" => $link_fb, "server_id" => 806, "number_seeding" => 20];
                    $response_fb = $client->request('POST', $link_buff, [
                        'cookies' => $jar,
                        'form_params' => $form_params,
                        'headers' => [
                            'origin' => 'https://app.mualike.net',
                            'referer' => "https://app.mualike.net/app-tang-like-facebook-mien-phi",
                            'dnt' => 1,
                            'content-type' => "application/x-www-form-urlencoded; charset=UTF-8",
                            'User-Agent' => $this->getRandomUserAgent(),
                        ]
                    ]);
                    $body_3 = $response_fb->getBody()->getContents();
                    $message[] = ["user" => $a["user"], "pass" => $a["pass"], "res_fb" => $body_3];
                    // call api buff 50 like toptop
                    $form_params = ["_token" => $token_2, "post_link" => $link_toptop, "server_id" => 668, "number_seeding" => 50];
                    $response_fb = $client->request('POST', $link_buff, [
                        'cookies' => $jar,
                        'form_params' => $form_params,
                        'headers' => [
                            'origin' => 'https://app.mualike.net',
                            'referer' => "https://app.mualike.net/app-tang-tim-tiktok-mien-phi",
                            'dnt' => 1,
                            'content-type' => "application/x-www-form-urlencoded; charset=UTF-8",
                            'User-Agent' => $this->getRandomUserAgent(),
                        ],
                    ]);
                    $body_3 = $response_fb->getBody()->getContents();
                    $message[] = ["user" => $a["user"], "pass" => $a["pass"], "res_tiktok" => $body_3];
                    // Xóa tất cả cookie
                    $jar = new CookieJar();
                }
                else {
                    return response()->json(["status" => 0, "message" => "token not found!"]);
                }
    
    
    
            }
            return response()->json(["status" => 1, "message" => $message], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        }
        else return response()->json(["status" => 0, "message" => "Link not found!"]);
    
    }

    function generateRandomIPv4() {
        // Sinh các phần của địa chỉ IP
        $part1 = 192;
        $part2 = 168;
        $part3 = rand(0, 255);
        $part4 = rand(1, 254); // Tránh không sử dụng 0 và 255
        
        // Tạo địa chỉ IP
        $ipAddress = sprintf('%d.%d.%d.%d', $part1, $part2, $part3, $part4);
        return $ipAddress;
    }

    function getRandomUserAgent() {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:79.0) Gecko/20100101 Firefox/79.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 10; SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.101 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-A920F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:78.0) Gecko/20100101 Firefox/78.0',
            'Mozilla/5.0 (X11; CrOS x86_64 12871.102.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.141 Safari/537.36',
            'Mozilla/5.0 (Linux; Android 11; SM-G970U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'Mozilla/5.0 (Linux; U; Android 4.2.2; en-us; SM-T210R Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko',
            'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-N960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; U; Android 2.3.6; en-us; GT-S5830 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/16A366 Safari/604.1',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:77.0) Gecko/20100101 Firefox/77.0',
            'Mozilla/5.0 (Linux; Android 10; SM-N986U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.101 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; Trident/7.0; AS; rv:11.0) like Gecko',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:86.0) Gecko/20100101 Firefox/86.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 13_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 10; SM-A207F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.99 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G973U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0',
            'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Mobile Safari/537.36',
            'Mozilla/5.0 (iPad; CPU OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_2_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
            'Mozilla/5.0 (Linux; Android 10; SM-N986B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Safari/605.1.15',
            'Mozilla/5.0 (Linux; Android 10; SM-N975U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36',
            'Mozilla/5.0 (Linux; Android 11; SM-G998B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.105 Mobile Safari/537.36'
        ];
    
        // Lấy ngẫu nhiên một User-Agent từ mảng
        return $userAgents[array_rand($userAgents)];
    }

    function getRandomProxy() {
        $proxy = [
            '160.86.242.23:8080',
            '52.53.221.181:3128'
        ];
    
        // Lấy ngẫu nhiên một User-Agent từ mảng
        return $proxy[array_rand($proxy)];
    }








    public function buffv2(Request $request) {
        $link = $request->fb;
        $list_sever = ["https://100like.vn/api/auth/fb/liketrial", "https://abclike.xyz/post.php"];
        foreach ($list_sever as $key => $value) {
            $client = new Client();
            $jar = new CookieJar();
            if ($key == 0) {
                // register
                $random = $this->randomString(20);
                $user_name = "ahihihihi" . $random;
                $password = "ahihihihi" . $random;
                $response = $client->request('POST', "https://100like.vn/api/auth/register?username=$user_name&password=$password");
                $body = $response->getBody()->getContents();  
              
                // login
                $response = $client->request('POST', "https://100like.vn/api/auth/login?username=$user_name&password=$password", [
                    'cookies' => $jar,
                ]);
                $cookies = $jar->getIterator();
                $auth = $response->getHeaderLine('Authorization');
                if (!empty($auth)) {
                    $auth = "Bearer " . $auth;
                }
                else {
                    $mess[] = ["key" => 0, "message" => "Login false, không lấy được thông tin auth. site = " . $value];
                    continue;
                }
                // end login
                $arr_param = [
                    "amount" => 20,
                    "link" => $link,
                    "disable" => false
                ];
                try {
                    $response = $client->request('POST', $value, [
                        'cookies' => $jar,
                        'json' => $arr_param,
                        'headers' => [
                            "Authorization" => $auth,
                            "DNT" => 1,
                            "Origin" => "https://100like.vn",
                            "Referer" => "https://100like.vn/fb/liketrial",
                            'User-Agent' => $this->getRandomUserAgent()
                        ],
                    ]);
                } catch (Exception $e) {
                    $body = $e->getResponse()->getBody()->getContents();
                }
            
                $body = $response->getBody()->getContents();  
                $data = json_decode($body, true);
                $decodedMessage = html_entity_decode($data['messages'], ENT_QUOTES, 'UTF-8');
                $mess[] = ["key" => 0, "message" => $decodedMessage];
            }
            if ($key == 1) {
                
                // get uid
                $response = $client->request('POST', "https://app.likeqq.vn/api/get-uid", [
                    'json' => ["link" => $link],
                ]);
                $body = $response->getBody()->getContents();
                $res = json_decode($body, true);
                // echo "<pre>";
                // print_r($res);
                // echo "</pre>";
                $uid = $res["data"]["objectId"];
                $full_link = $res["data"]["objectUrl"];
                
                $arr_param_2 = [
                    "id" => $uid,
                    "linkfull" => $full_link
                ];
                
                $response = $client->request('POST', $value, [
                    'form_params' => $arr_param_2,
                    'headers' => [
                        'User-Agent' => $this->getRandomUserAgent(),
                        'Accept' => 'text/html',
                        'X-Requested-With' => 'XMLHttpRequest', // Cần thiết nếu request là AJAX
                    ]
                ]);
                $body = $response->getBody()->getContents();
                $body = response($body)->header('Content-Type', 'text/html');
                $mess[] = ["key" => 1, "message" => $body];
                
            }
            $jar->clear();
        }
        return response()->json(["status" => 1, "mess" => $mess]);
    }

    function randomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Tập hợp có ký tự đặc biệt
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1); // Lấy ngẫu nhiên chỉ mục từ tập hợp
            $randomString .= $characters[$randomIndex]; // Thêm ký tự ngẫu nhiên vào chuỗi
        }
        
        return $randomString; // Trả về chuỗi ngẫu nhiên
    }
}
