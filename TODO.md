# TODO List

## InvoiceController Destroy Method Fix

-   [x] Adjust parameter id in destroy method to handle invoice_id with '/' using where clause instead of findOrFail
-   [x] Add logic to remove invoice_id from retention records without deleting the entire retention data (set invoice_id to null)
-   [ ] Test the destroy method to ensure it works correctly and doesn't cause errors
-   [ ] Verify that retention records are updated properly (invoice_id set to null)
