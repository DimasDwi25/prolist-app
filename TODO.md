# Update Overdue Project Logic in EngineerDashboardApiController

## Tasks

-   [x] Update getProjectsByCriteria method to exclude projects with status 'Engineering Work Completed' or 'Project Finished' instead of checking engineering_finish_date
-   [x] Update top5Overdue query in getProjectLists to exclude projects with status 'Engineering Work Completed' or 'Project Finished'
-   [x] Update projectOnTrackList query in getProjectLists to exclude projects with status 'Engineering Work Completed' or 'Project Finished'
-   [ ] Test the changes to ensure overdue projects are correctly filtered
