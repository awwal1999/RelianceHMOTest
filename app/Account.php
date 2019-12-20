<?php
namespace App;

use GuzzleHttp\Client;

class Account 
{

  public $token;

  public $client;

  public $header;

  private  $contractCode = "2957982769";

  private $apiKey = "MK_TEST_WD7TZCMQV7";
  
  private $password = "H5EQMQSHSURJNQ7UH2R78YAH6UN54ZP7";

  public  $loginEndpoint = "https://sandbox.monnify.com/api/v1/auth/login";

  public  $transactionStatusEndpoint = "https://sandbox.monnify.com/api/v1/merchant/transactions/query";
  
  public  $reserveAccountEndpoint = "https://sandbox.monnify.com/api/v1/bank-transfer/reserved-accounts";
  
  public  $deactivateAccountEndpoint = "htts://sandbox.monnify.com/api/v1/bank-transfer/reserved-accounts/";
  

  public function __construct(Client $client)
  {
    $this->client = $client;
  }

  public function authenticate()
  {
    $request = $this->client->post($this->loginEndpoint , [
      'auth' => [
        $this->apiKey,
        $this->password
      ]
    ]);

    $response = json_decode($request->getBody()->getContents(), true);

    $this->token =  $response['responseBody']['accessToken'];

    return $response;
  }

  public function reserveAccount($accountReference, $accountName, $currencyCode, $customerEmail)
  {
    $request = $this->client->request(
      'POST',
      $this->reserveAccountEndpoint,
      [
        'headers' => [
          'Authorization' => "Bearer {$this->token}"
        ],
        'json' => [
          'accountReference' => $accountReference,
          'accountName' => $accountName,
          'currencyCode' => $currencyCode,
          'contractCode' => $this->contractCode,
          'customerEmail' => $customerEmail
        ],
      ]
    );

    return json_decode($request->getBody()->getContents(), true);
  }

  public function deactivateAccount($accountNumber)
  {
    $request = $this->client->request(
      'DELETE',
      $this->deactivateAccountEndpoint . $accountNumber,
      [
        'headers' => [
          'Authorization' => "Bearer {$this->token}"
        ]
      ]
    );

    return json_decode($request->getBody()->getContents(), true);
  }

  public function transactionstatus($paymentReference= '', $transactionReference = '')
  {
    $request = $this->client->request(
      'DELETE',
      $this->transactionStatusEndpoint,
      [
        'headers' => [
          'Authorization' => "Bearer {$this->token}"
        ],
        'json' => [
          'paymentReference' => $paymentReference,
          'transactionReference' => $transactionReference,
        ]
      ]
    );

    return json_decode($request->getBody()->getContents(), true);
  }
}