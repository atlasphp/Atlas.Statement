# Change Log

## 1.1.0

- Adds new method getTable() to Insert, Update, and Delete for getting the table being modified.
- Implicitly nullable params are now explicitly nullable (PHP 8.4 compat).
- Use ::class instead of ::CLASS.
- Upgrade PHPStan to 1.0, add annotations as needed to soothe stan.
- Add Github workflow.
- Documentation fixes and updates.

## 1.0.1

- Select::whereEquals() now handles empty array values
- UPDATE and DELETE now honor LIMIT and OFFSET
- updated docs and tests

## 1.0.0

Initial release.
