<?php
namespace App\Service;

class ReportHelper
{
    /**
     * @param array $invoices
     * @return array
     * @throws \Exception
     */
    public function handleInvoice(array $invoices): array {
        $handledInvoices = [];

        foreach ($invoices as $invoice) {
            if (!is_numeric($invoice['id'])) {
                $invoice['errors'][] = 'The invoice id is incorrect.';
            }

            if (!is_numeric($invoice['amount'])) {
                $invoice['errors'][] = 'The amount is incorrect.';
            }

            $invoiceDate = \DateTime::createFromFormat('Y-m-d', $invoice['date']);

            if ($invoiceDate === false) {
                $invoice['errors'][] = 'The date is incorrect.';
            }

            $invoice['price'] = '';

            if (empty($invoice['errors'])) {
                $currentDate = new \DateTime();
                $interval = $currentDate->diff($invoiceDate)->days;

                if ($interval > 30) {
                    $invoice['price'] = $invoice['amount'] * 0.5;
                } else {
                    $invoice['price'] = $invoice['amount'] * 0.3;
                }
            }

            array_push($handledInvoices, $invoice);
        }

        return $handledInvoices;
    }
}