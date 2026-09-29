<?php 


   namespace App\Controllers;

   use App\Core\Controller;
   use App\Models\Employee;
   use CURLFile;


    class OcrController extends Controller
    {


        public function index()
        {
           
            $user = Employee::find('1003'); 
      
           $this->render('/tools/recog', ['user' => $user]);
            
        }


        public function gets()
        {

            if (isset($_POST['scan'])) {
                $results = $this->scan();
            }
            
             $this->render('/tools/recog',['result' => $results]);
        }


      public function scan() 
        {
            if (!isset($_FILES['file'])) {
                die("No file uploaded.");
            }

            $file = $_FILES['file'];

            if ($file['error'] !== 0) {
                die("Upload error.");
            }

            $imagePath = $file['tmp_name'];
            $originalName = $file['name'];
            $extension = strtoupper(pathinfo($originalName, PATHINFO_EXTENSION));

            $apiKey = $_ENV['OCR_API']; 

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => "https://api.ocr.space/parse/image",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'apikey' => $apiKey,
                    'language' => 'eng',
                    'isOverlayRequired' => 'false',
                    'filetype' => $extension,
                    'file' => new CURLFile($imagePath, mime_content_type($imagePath), $originalName)
                ],
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                die("Curl error: " . curl_error($ch));
            }

            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['ParsedResults'][0]['ParsedText'])) {
                $resultText = $data['ParsedResults'][0]['ParsedText'];
                return $resultText;
            } else {
                $error = $data['ErrorMessage'][0] ?? 'OCR Failed to read text.';
                return $error;
            }
        }

        

    }