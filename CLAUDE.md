CLAUDE.md
Project Overview

This project is a web application built with Laravel. Follow the existing project architecture, coding conventions, and team workflow when making changes.

Before modifying code, inspect the existing implementation and understand how the relevant components work together.

Technology Stack
Backend: Laravel / PHP
Frontend: Follow the frontend framework and libraries already installed in the project.
Database: Use the database configured in the project's environment.
Version Control: Git
Development Environment: Follow the existing project setup.

Do not introduce new frameworks, libraries, or dependencies without a clear need.

Development Guidelines
1. Understand Before Changing
Inspect relevant files before making changes.
Follow existing naming conventions and project structure.
Reuse existing controllers, models, services, middleware, and components when appropriate.
Avoid duplicating existing functionality.
Make the smallest changes necessary to complete the task.
Do not modify unrelated files.
2. Laravel Best Practices
Follow Laravel conventions and best practices.
Keep controllers focused on handling HTTP requests and responses.
Use Eloquent models and relationships appropriately.
Use Form Requests for validation when consistent with the existing codebase.
Use middleware and policies for authentication and authorization.
Use migrations for database schema changes.
Use named routes where appropriate.
Use Blade templates or the existing frontend framework instead of introducing a new rendering approach.
Handle errors and exceptions appropriately.
Avoid raw SQL when Eloquent or the query builder is suitable.
Prevent mass-assignment vulnerabilities and validate user input.
3. Security
Never expose passwords, API keys, application secrets, session identifiers, or authentication tokens.
Never commit the .env file or other files containing real secrets.
Follow Laravel's authentication, authorization, CSRF protection, and validation mechanisms.
Do not bypass authentication or authorization checks to make a feature work.
Avoid logging sensitive user information.
Do not disable security protections without explicit justification.
4. Git and Team Collaboration
Do not run git commit, git push, git reset --hard, or destructive Git commands unless explicitly requested.
Do not overwrite or discard existing user changes.
Check git status before modifying files when appropriate.
Keep changes focused on the requested task.
Do not commit generated files, temporary files, local configuration, or runtime data unless the project explicitly requires them.
Follow the existing .gitignore rules.
Do not remove files simply because they appear unused without checking their purpose.
5. Laravel Files That Should Usually Not Be Committed

Do not commit local or generated files such as:

.env
Runtime session files under storage/framework/sessions/
Compiled Blade views under storage/framework/views/
Runtime cache files under storage/framework/cache/
Application logs under storage/logs/
Local editor settings or temporary files that are not part of the team's workflow

Follow the repository's existing .gitignore file and preserve any project-specific exceptions.

Do not delete required directories or tracked files without verifying their purpose.

6. Database Changes
Review existing migrations and database relationships before modifying the schema.
Create new migrations for schema changes instead of manually changing the database structure.
Do not run destructive database commands against production.
Never reset, truncate, or drop tables without explicit authorization.
Preserve existing data unless the task specifically requires a data change.
Update models, validation, and related application code when necessary.
7. Frontend and UI
Follow the existing design system and component conventions.
Reuse existing layouts, components, styles, and assets.
Maintain responsive behavior and accessibility.
Avoid unnecessary UI redesigns when implementing backend functionality.
Keep the interface consistent with the rest of the application.
8. Testing and Verification

After making changes:

Run relevant tests when available.
Check for syntax errors and obvious runtime issues.
Verify that routes, validation, authorization, and database interactions work as expected.
Report tests that were run and whether they passed.
If tests cannot be run, explain why.
Never claim a change has been tested if it has not been tested.

Use the project's existing test framework and commands.

Working Process

For each task:

Understand the requested change.
Inspect the relevant files and existing implementation.
Identify the smallest appropriate solution.
Implement the change while preserving existing functionality.
Review the resulting diff for unrelated changes or sensitive data.
Run relevant tests or checks.
Summarize the files changed, the implementation, and any remaining concerns.
Important Rules
Do not invent files, routes, models, database columns, or application behavior without checking the project.
Do not rewrite large parts of the application when a focused change is sufficient.
Ask for clarification when a requirement is ambiguous and guessing could cause significant problems.
Explain important trade-offs before making potentially destructive changes.
Prioritize correctness, security, maintainability, and compatibility with the existing codebase.
Final Response Format

After completing a task, provide:

Summary: What was changed.
Files Modified: Which files were added, changed, or removed.
Verification: Tests or checks performed and their results.
Notes: Any setup steps, risks, or follow-up work needed.

Keep explanations concise, clear, and suitable for a team development workflow.