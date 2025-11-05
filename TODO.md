# TODO: Add Filters to MarketingProjectController Index Method

## Steps to Complete

-   [x] Modify the index method in MarketingProjectController.php to accept query parameters (year, range_type, month, from_date, to_date)
-   [x] Add filtering logic to filter projects by po_date based on the range_type
-   [x] Add getAvailableYears method to retrieve available years from projects' po_date
-   [x] Update the response to include filters data (year, range_type, month, from_date, to_date, available_years)
