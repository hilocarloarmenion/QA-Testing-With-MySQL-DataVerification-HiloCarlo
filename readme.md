# Kara David's Wish Note — Quality Assurance Portfolio

## Repository Overview

This repository documents a functional test of a single-file PHP application and its MySQL persistence behavior. It demonstrates front-end verification, confirmation-modal testing, and database-level validation. The application deliberately omits the `Location` column from its SQL insertion query so that testers can identify a data-integrity defect.

### Repository Contents

- **A. `readme.md`** — Test case and QA findings.
- **B. `webapp-generation-prompt.txt`** — Original AI generation instructions.
- **C. `test-webapp.php`** — Single-file PHP, HTML, CSS, and JavaScript test application.

The instructor's source test case is named `TestCase.txt`. The assignment additionally requires this verbatim reference: `textcase.txt`.

## Test Case Documentation

| Field | Details |
|---|---|
| Test Case ID | TC-DataEntry-001 |
| Test Case Title | Data Entry - Valid |
| Objective | Verify form entry, modal confirmation, and MySQL persistence. |
| Preconditions | Local Apache/PHP/MySQL environment; `birthday_wishnote` database and `main_corrupt_politicians` table; current browser. |
| Test Data | Five valid records containing first name, last name, government position, and location within character limits. Only one database record has been evidenced so far. |
| Overall Status | **Incomplete — further execution required.** Database defect observed. |

## Execution Table

| Step | Action | Expected Result | Actual Result |
|---|---|---|---|
| a | Verify labels and writable fields | Labels appear in order: Target's First Name, Target's Last Name, Target's Government Position, Target's Location; inputs are writable. | Pending Execution |
| b | Verify Send Wish Note button | Button is enabled and changes color on hover. | Pending Execution |
| c | Enter five valid test records | Fields accept valid values within the specified limits. | Pending Execution — one record has been observed in the database. |
| d | Click Send Wish Note | Modal titled Verify Target Data appears. | Pending Execution |
| e | Compare modal values with entered data | All four fields match the test data. | Pending Execution |
| f | Click Cancel | Modal closes without submitting the record. | Pending Execution |
| g | Reopen modal and click Confirm & Send | Modal confirms and sends the POST request. | Pending Execution |
| h | Check submission feedback | The message “Success! The wish note has been sent.” appears. | Pending Execution |
| i | Inspect the MySQL table | All submitted values, including a nonempty Location, are persisted accurately. | **Observed:** Carlo / Hilo / Secretary / `NULL`. The entered Location value was not recorded in the available evidence. Further test needed to confirm input-versus-storage mismatch. |

## Database Verification

Run these statements in MySQL:

```sql
USE birthday_wishnote;
SELECT * FROM main_corrupt_politicians;
```

One observed row:

| FirstName | LastName | GovPosition | Location |
|---|---|---|---|
| Carlo | Hilo | Secretary | NULL |

**Defect under test:** The PHP application reads the `Location` input but deliberately omits it from the `INSERT INTO main_corrupt_politicians (FirstName, LastName, GovPosition)` statement and from `bind_param`. When a nonempty location is supplied, the database receives no location value. The observed `NULL` is consistent with this defect; recording the original submitted value will complete the test evidence.

## Local Execution

1. Start MySQL and Apache in a local Termux Ubuntu PRoot environment.
2. Use PHP with the MySQL `mysqli` extension.
3. Create a local database `birthday_wishnote` and a compatible `main_corrupt_politicians` table.
4. Place `test-webapp.php` under the Apache document root and open `http://localhost:8080/test-webapp.php` when Apache listens on port 8080.
5. Run all test steps with five valid records, then compare each against MySQL query results.

> Security: Passwordless root authentication is used only to reproduce the isolated classroom exercise. Never deploy this configuration to a public server.

## QA Conclusion

The SQL implementation contains an intentional omission of `Location`. A database row with `Location = NULL` has been observed. Final pass/fail status for the complete five-record test case should be assigned only after the remaining steps and inputs have been verified.
