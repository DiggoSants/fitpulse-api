<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Enrollment;
use App\Models\Student;
use InvalidArgumentException;

class BillingService
{
    public const PAYMENT_METHODS = ['credit_card', 'pix', 'boleto'];

    public function createForEnrollment(Student $student, Enrollment $enrollment, string $paymentMethod): Billing
    {
        $paymentMethod = $this->normalizePaymentMethod($paymentMethod);
        $status = $this->statusForPaymentMethod($paymentMethod);

        $billing = Billing::create([
            'student_id'      => $student->id,
            'plan_id'         => $enrollment->plan_id,
            'enrollment_id'   => $enrollment->id,
            'payment_method'  => $paymentMethod,
            'amount'          => $enrollment->plan->price,
            'status'          => $status,
            'paid_at'         => $status === 'confirmed' ? now() : null,
        ]);

        if ($status === 'rejected') {
            $student->update(['is_defaulter' => true]);
        }

        if ($status === 'confirmed') {
            $student->update(['is_defaulter' => false]);

            if ($student->isDelinquent()) {
                $student->activate();
            }
        }

        return $billing;
    }

    public function messageForStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Boleto gerado! Aguardando compensacao.',
            'confirmed' => 'Pagamento confirmado com sucesso!',
            'rejected' => 'Pagamento recusado. Verifique os dados e tente novamente.',
            default => 'Pagamento registrado.',
        };
    }

    public function statusForPaymentMethod(string $paymentMethod): string
    {
        return match ($this->normalizePaymentMethod($paymentMethod)) {
            'boleto' => 'pending',
            'pix' => 'confirmed',
            'credit_card' => random_int(1, 10) <= 9 ? 'confirmed' : 'rejected',
        };
    }

    public function normalizePaymentMethod(string $paymentMethod): string
    {
        if (!in_array($paymentMethod, self::PAYMENT_METHODS, true)) {
            throw new InvalidArgumentException('Metodo de pagamento invalido.');
        }

        return $paymentMethod;
    }
}
