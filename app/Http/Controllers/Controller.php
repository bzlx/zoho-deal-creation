<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    // native php "insert record" function from ZOHO docs https://www.zoho.com/crm/developer/docs/api/v2/insert-records.html
    // @param array @recordObject

    public function send(array $recordObject, string $module)
    {
        $requestBody = array();
        $recordArray = array();
        $curl_pointer = curl_init();
        $curl_options = array();
        $url = "https://www.zohoapis.eu/crm/v2/" . $module;

        $curl_options[CURLOPT_URL] = $url;
        $curl_options[CURLOPT_RETURNTRANSFER] = true;
        $curl_options[CURLOPT_HEADER] = 1;
        $curl_options[CURLOPT_CUSTOMREQUEST] = "POST";
        $recordArray[] = $recordObject;
        $requestBody["data"] = $recordArray;
        $curl_options[CURLOPT_POSTFIELDS] = json_encode($requestBody);
        $headersArray = array();

        $headersArray[] = "Authorization" . ":" . "Zoho-oauthtoken " . env('ZOHO_TOKEN');

        $curl_options[CURLOPT_HTTPHEADER] = $headersArray;

        curl_setopt_array($curl_pointer, $curl_options);

        $result = curl_exec($curl_pointer);
        $responseInfo = curl_getinfo($curl_pointer);
        curl_close($curl_pointer);
        list($headers, $content) = explode("\r\n\r\n", $result, 2);
        if (strpos($headers, " 100 Continue") !== false) {
            list($headers, $content) = explode("\r\n\r\n", $content, 2);
        }

        $headerArray = (explode("\r\n", $headers, 50));
        $headerMap = array();
        foreach ($headerArray as $key) {
            if (strpos($key, ":") != false) {
                $firstHalf = substr($key, 0, strpos($key, ":"));
                $secondHalf = substr($key, strpos($key, ":") + 1);
                $headerMap[$firstHalf] = trim($secondHalf);
            }
        }
        $jsonResponse = json_decode($content, true);

        return  $jsonResponse;
    }
    public function create_deal(Request $request)
    {
        $recordObject = array();
        
        $recordObject["Amount"] = $request->Ammount;
        $recordObject["Deal_Name"] = $request->Deal_Name;
        $recordObject["Expected_Revenue"] = $request->Expected_Revenue;
        // unused fields
        // $recordObject["Account_Name"]="";
        // $recordObject["Campaign_Source"]="";
        // $recordObject["Closing_Date"]="";
        // $recordObject["Contact_Name"]=;
        // $recordObject["Created_By"]=;
        // $recordObject["Owner"]=;
        // $recordObject["Description"]=;
        // $recordObject["Lead_Source"]=;
        // $recordObject["Modified_By"]=;
        // $recordObject["Next_Step"]=;
        // $recordObject["Prediction_Score"]=;
        // $recordObject["Probability"]=;
        // $recordObject["Stage"]='Negotiation/Review';
        // $recordObject["Type"]=;

        $jsonResponse = $this->send($recordObject, 'Deals');
        // if create task checkbox was checked and deal creation was successful 
        if ($request->create_task and array_key_exists('data', $jsonResponse)) {
            //$jsonResponse['data'][0]['details']['id'] id of created Deal
            $this->create_task($jsonResponse['data'][0]['details']['id']);
        }
        return view('deal', compact('jsonResponse'));
    }
    public function create_task($id)
    {
        $recordObject = array();
        $recordObject["Subject"] = "Test task for " . $id;
        $recordObject["se_module"] = 'Deals';
        $recordObject["What_Id"] = $id;
        // $recordObject["Due_Date"]="";
        // $recordObject["Status"]="";
        // $recordObject["Who_Id"]="";
        $this->send($recordObject, "Tasks");
    }
}
