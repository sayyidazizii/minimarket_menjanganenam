<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\CoreMessages;
use GuzzleHttp\Exception\BadResponseException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    

    public function postWhatsappMessages($msg, $status, $receiver){
        if($status == 1){
            $client = new \GuzzleHttp\Client();
            $token ='xrOAoiED3BBrdu7aSscudCZ1zN3GC9TxX1WP3RXrnUlwQ77W1R';
            try {
                $response = $client->request('POST', 'https://app.ruangwa.id/api/send_message', [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'number'    => $receiver,
                        'token'     => $token,
                        'message'   => $msg,
                    ]
                ]);
                $response = $response->getBody()->getContents();
            } catch (BadResponseException $exception) {
                $response = $exception->getResponse();
                $jsonBody = (string) $response->getBody();
                // return redirect()->back()->with('alert','Terjadi Masalah Pada Server Whatssapp');
            }
        }
    }

    public function getMessage($messages_id){
        $messages = CoreMessages::where('messages_id', $messages_id)
        ->first();

        return $messages['messages_text'];
    }

    public function getMessageStatus($messages_id){
        $messages = CoreMessages::where('messages_id', $messages_id)
        ->first();

        return $messages['messages_status'];
    }
}
