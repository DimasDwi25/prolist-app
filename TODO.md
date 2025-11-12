# SUC Dashboard Controller Implementation

## Tasks

-   [x] Create SUCDashboardController.php with single index method for all dashboard data
-   [x] Add single route for the dashboard endpoint in routes/api.php
-   [x] Test the API endpoint to ensure it returns correct data

## Details

-   **MR Outstanding**: Material Requests with status 'On Progress' or 'Waiting Approval'
-   **MR Overdue**: Outstanding MR where target_date < current date
-   **PL Outstanding**: All Packing Lists, with optional filter by type
