# Customize Invoice ID Sequence in InvoiceController Store Method

## Completed Steps

-   [x] Add 'invoice_sequence' to request validation in store method (nullable integer, min 1)
-   [x] Modify store method to use custom sequence if provided, else auto-generate
-   [x] Add uniqueness check for custom sequence within project
-   [x] Update generateInvoiceId method to accept sequence as parameter
-   [x] Update nextInvoiceId method to handle optional invoice_sequence
-   [x] Create validateSequence endpoint to check sequence availability

## Followup Steps

-   [ ] Test the store method with custom sequences like 001, 002, 004
-   [ ] Test the validateSequence endpoint
