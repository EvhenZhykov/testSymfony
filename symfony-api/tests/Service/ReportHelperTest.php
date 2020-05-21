<?php
namespace App\Tests\Service;

use App\Service\ReportHelper;
use PHPUnit\Framework\TestCase;

class ReportHelperTest extends TestCase
{
    public function testHandleInvoices() {
        $invoices = [
            [
                'id' => 1,
                'amount' => 100,
                'date' => '2019-05-20'
            ],
            [
                'id' => 2,
                'amount' => 100,
                'date' => date('Y-m-d', strtotime('-10 days'))
            ],
            [
                'id' => 3,
                'amount' => 'test',
                'date' => '2019-05-10'
            ],
            [
                'id' => 4,
                'amount' => 300,
                'date' => 'date'
            ],
            [
                'id' => 'B',
                'amount' => 400,
                'date' => '2019-05-01'
            ]
        ];

        $equalInvoices = [
            [
                'id' => 1,
                'amount' => 100,
                'date' => '2019-05-20',
                'price' => 50
            ],
            [
                'id' => 2,
                'amount' => 100,
                'date' => date('Y-m-d', strtotime('-10 days')),
                'price' => 30
            ],
            [
                'id' => 3,
                'amount' => 'test',
                'date' => '2019-05-10',
                'price' => '',
                'errors' => [
                    'The amount is incorrect.'
                ]
            ],
            [
                'id' => 4,
                'amount' => 300,
                'date' => 'date',
                'price' => '',
                'errors' => [
                    'The date is incorrect.'
                ]
            ],
            [
                'id' => 'B',
                'amount' => 400,
                'date' => '2019-05-01',
                'price' => '',
                'errors' => [
                    'The invoice id is incorrect.'
                ]
            ],
        ];

        $reportHelper = new ReportHelper();
        $handledInvoices = $reportHelper->handleInvoice($invoices);

        $this->assertEquals($equalInvoices, $handledInvoices);
    }

}