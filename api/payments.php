<?php

/**
 * Payments API Boilerplate
 * Owner: Nayanthi (Payments Service)
 *
 * TODO Checklist for Nayanthi:
 * - database/helper includes and method routing.
 * - Implement payment creation, retrieval, listing, status updates, and deletion.
 * - Validate incoming payloads (amount, status, references) before database operations.
 * - Ensure consistent JSON responses and proper error logging.
 */

// TODO[Nayanthi]: require_once __DIR__ . '/../config/database.php';
// TODO[Nayanthi]: require_once __DIR__ . '/../includes/functions.php';

// TODO[Nayanthi]: Handle $_SERVER['REQUEST_METHOD'] dispatching to functions below.

function createPayment(array $input): void
{
    // TODO[Nayanthi]: Validate amount/status, insert into payments table, and return the new payment details.
}

function getPayment(int $paymentId)
{
    // TODO[Nayanthi]: Fetch a single payment record or return null when missing.
}

function getAllPayments(): void
{
    // TODO[Nayanthi]: Return a list of payments ordered by payment_date (or created_at).
}

function updatePaymentStatus(array $input): void
{
    // TODO[Nayanthi]: Update the payment status field and respond with success or not-found.
}

function deletePayment(int $paymentId): void
{
    // TODO[Nayanthi]: Remove a payment entry on admin request and return the outcome.
}
