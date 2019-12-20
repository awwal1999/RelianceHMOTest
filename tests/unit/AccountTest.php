<?php

namespace Tests\Unit;

use App\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase 
{
  protected $account;

  protected $mockHandler;

  protected $response;

  protected function setUp(): void 
  {
    $this->mockHandler = new MockHandler();

    $httpClient = new Client([
      'handler' => $this->mockHandler,
    ]);

    $this->account = new Account($httpClient);

    $this->response = json_encode([
      'requestSuccessful' => true,
      'responseMessage' => 'success',
      'responseCode' => '0',
      "responseBody" => [
       "accessToken" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
       "expiresIn" => 3599
      ]
    ]);
  }
  
  /** @test */
  public function a_user_can_be_authenticated() {
    $this->mockHandler
         ->append(new Response(200, [], $this->response));

    $response = $this->account->authenticate();

    $this->assertNotEmpty($response);
    $this->assertTrue($response['requestSuccessful']);
    $this->assertArrayHasKey("accessToken", $response['responseBody']);
  }

  /** @test */
  public function an_account_can_be_reserved()
  {
    $this->mockHandler
         ->append(new Response(200, [], $this->accountResevedResponse()));
    $response = $this->account->reserveAccount('test1', 'Test Reserved Account', 'NGN', 'test@tester.com');

    $this->assertNotEmpty($response);
    $this->assertTrue($response['requestSuccessful']);
  }

  /** @test */
  public function an_account_can_be_deactivated()
  {
    $this->mockHandler
         ->append(new Response(200, [], $this->accountResevedResponse()));
    $response = $this->account->deactivateAccount('9900725554');

    $this->assertNotEmpty($response);
    $this->assertTrue($response['requestSuccessful']);
  }

  /** @test */
  public function transaction_status_can_be_seen()
  {
    $this->mockHandler
      ->append(new Response(200, [], $this->transactionStatusResponse()));
    $response = $this->account->deactivateAccount('9900725554');

    $this->assertNotEmpty($response);
    $this->assertTrue($response['requestSuccessful']);
  }

  public function transactionStatusResponse()
  {
    return json_encode([
      'requestSuccessful' => true,
      'responseMessage' => 'success',
      'responseCode' => '0',
      "responseBody" => [
        "paymentMethod" => "ACCOUNT_TRANSFER",
        "createdOn" => "2019-08-09T18:52:45.000+0000",
        "amount" => 100.00,
        "currencyCode" => "NGN",
        "customerName" => "Test Reserved Account",
        "customerEmail" => "test@tester.com",
        "paymentDescription" => "Test Reserved Account",
        "paymentStatus" => "PAID",
        "transactionReference" => "MNFY|20190809123429|000000",
        "paymentReference" => "reference12345"
      ]
    ]);
  }

  public function accountResevedResponse()
  {
    return json_encode([
      'requestSuccessful' => true,
      'responseMessage' => 'success',
      'responseCode' => '0',
      "responseBody" => [
        "contractCode" => "797854529434",
        "accountReference" => "abc123",
        "accountName" => "Test Reserved Account",
        "currencyCode" => "NGN",
        "customerEmail" => "test@tester.com",
        "accountNumber" => "9900910565",
        "bankName" => "Providus Bank",
        "bankCode" => "101",
        "reservationReference" => "E9Y49CFNYAVHFGSCKJ6N",
        "status" => "ACTIVE",
        "createdOn" => "2019-08-11 23:00:43.816",
        "incomeSplitConfig" => [
            [
                "subAccountCode" => "MFY_SUB_319452883228",
                "feePercentage" => 10.5,
                "feeBearer" => true,
                "splitPercentage" => 20
            ]
        ]
      ]
    ]);
  }

  public function accountdeactivatedResponse()
  {
    return json_encode([
      'requestSuccessful' => true,
      'responseMessage' => 'success',
      'responseCode' => '0',
      "responseBody" => [
        "contractCode" => "797854529434",
        "accountReference" => "reference12345#",
        "accountName" => "Test Reserved Account",
        "currencyCode" => "NGN",
        "customerEmail" => "test@tester.com",
        "accountNumber" => "9900725554",
        "bankName" => "Providus Bank",
        "bankCode" => "101",
        "reservationReference" => "NRF72EMEBCGNN6WUKD35",
        "status" => "ACTIVE",
        "createdOn" => "2019-08-07 17:05:50.0"
      ]
    ]);
  }
}