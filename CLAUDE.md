CLAUDE.md
1. Project Overview

This project is a web-based Calendar of Events system developed using Laravel and Blade templates.

The application allows authorized users to manage, organize, view, and maintain events through a centralized calendar interface.

The system has two main user roles:

Superadmin — Has elevated administrative privileges for managing the system and overseeing administrative users and events, according to the application's existing permission rules.
Admin — Manages events and related calendar information within the scope of the permissions assigned to them.

Follow the existing project implementation whenever the exact behavior of a feature or role is not specified.

2. Technology Stack
Backend: Laravel and PHP
Frontend: Blade templates, HTML, CSS, and JavaScript
Database: Use the database configured in the project
Authentication: Follow the existing Laravel authentication implementation
Version Control: Git and GitHub or the team's configured repository
Calendar Interface: Reuse the calendar library already installed in the project, if applicable

Do not introduce a new framework, calendar library, or major dependency without a clear reason.

3. General Development Rules
Inspect the existing code before implementing changes.
Follow the project's existing directory structure and naming conventions.
Prefer small, focused changes over unnecessary rewrites.
Reuse existing models, controllers, services, middleware, Blade components, and layouts.
Avoid duplicating existing functionality.
Do not modify unrelated files.
Do not assume database columns, routes, or model relationships exist without checking.
Preserve existing working features unless the requested task requires changing them.
4. Laravel Backend Guidelines
Follow Laravel conventions and best practices.
Keep controllers organized and focused.
Use Eloquent models and relationships where appropriate.
Use Form Request classes or the existing validation approach for validating input.
Use migrations for database schema changes.
Use named routes where appropriate.
Use middleware, policies, gates, or the existing authorization system to enforce permissions.
Use CSRF protection for state-changing web requests.
Handle errors and exceptions appropriately.
Avoid mass-assignment vulnerabilities.
Use database transactions when multiple related database operations must succeed or fail together.
Do not expose sensitive information in responses or logs.
5. User Roles and Permissions

The application has Superadmin and Admin roles.

Superadmin
May access system-wide administrative features according to existing requirements.
May manage administrative accounts if this functionality is part of the application.
May oversee and manage events according to the established permission rules.
May access restricted management pages only through authorized routes and actions.
Admin
May access event management and calendar features permitted by their assigned role.
May create, view, update, or delete events only when authorized.
Must not gain Superadmin privileges by manipulating URLs, form fields, or requests.
Must not access restricted administrative features without permission.
Authorization Rules
Always verify permissions on the server.
Do not rely on hiding buttons or navigation links as the only access control.
Protect routes and individual actions using the project's existing middleware or authorization mechanisms.
Validate role assignments on the server.
Do not allow users to change their own role through ordinary profile or event forms.
Preserve the existing role hierarchy and permission behavior unless explicitly instructed to change it.
6. Calendar and Event Management

When working on event-related features:

Follow the existing event model, database schema, and calendar implementation.
Maintain consistent event data between the database, calendar interface, and event details pages.
Validate required fields such as event title, date, time, and description according to the existing requirements.
Validate date and time ranges, including ensuring that an event's end does not precede its start.
Handle all-day events and multi-day events if supported by the application.
Preserve existing event statuses, categories, locations, and other event fields.
Use the project's established rules for overlapping events, recurring events, and time zones.
Ensure that calendar navigation, filtering, and event details remain functional.
Use appropriate validation and authorization for event creation, editing, and deletion.
Ask for clarification before introducing new calendar behavior that changes established requirements.
7. Blade Templates and Frontend
Use Laravel Blade templates for server-rendered pages.
Reuse existing layouts, sections, components, and partials.
Use Blade directives such as @extends, @section, @include, @auth, and @can when appropriate.
Use {{ }} to escape ordinary dynamic output.
Avoid using {!! !!} with untrusted or user-provided content.
Use the existing CSS framework and JavaScript libraries.
Keep markup readable and avoid unnecessarily large Blade templates.
Separate reusable UI elements into Blade components or partials when appropriate.
Preserve the current visual design unless a redesign is requested.
Ensure calendar pages and administrative dashboards remain responsive and accessible.
Show validation errors and success or failure messages using the project's existing notification pattern.
Do not put secrets or sensitive configuration values in frontend code.
8. Routes and Navigation
Follow the existing route naming conventions.
Group routes by middleware or role when appropriate.
Apply authentication and authorization to protected pages and actions.
Use named routes in Blade templates when available.
Do not expose restricted Superadmin pages through Admin navigation.
Remember that hiding a navigation link does not secure its destination.
Check for duplicate or conflicting routes before adding new ones.
9. Database and Migrations
Inspect existing migrations and model relationships before changing the schema.
Use migrations for database structure changes.
Update related models, validation rules, and application logic when necessary.
Avoid deleting or renaming columns without checking their usage.
Do not reset, truncate, or drop production data.
Do not run destructive database commands without explicit authorization.
Preserve existing event records and administrative accounts unless the task requires a data change.
10. Security and Sensitive Files

Never commit:

.env files containing local configuration or secrets.
Real passwords, API keys, application secrets, or authentication tokens.
Laravel session data from storage/framework/sessions/.
Runtime logs from storage/logs/.
Generated cache files and temporary development files that should remain local.

Follow the repository's existing .gitignore rules.

Additional rules:

Do not disable authentication, authorization, or CSRF protection to bypass an error.
Do not expose session identifiers or security tokens.
Validate all user-submitted data on the server.
Protect administrative actions against unauthorized requests.
Do not delete required Laravel directories just because their generated contents should not be committed.
If a sensitive value is exposed, explain the risk and recommend appropriate rotation or invalidation.
11. Git and Team Collaboration
Inspect git status before making changes when appropriate.
Preserve existing uncommitted changes made by team members.
Never discard user changes without permission.
Do not run git commit, git push, git reset --hard, or other destructive Git commands unless explicitly requested.
Do not commit session files, local environment files, runtime data, or unrelated editor files.
Review the final diff for accidental changes and sensitive information.
Keep commits focused on related application changes.
Commit CLAUDE.md because it contains shared instructions for the project.
Do not modify .gitignore broadly without checking the consequences for the team.
12. Testing and Verification

After implementing a feature:

Run relevant automated tests when available.
Verify the affected routes and controller behavior.
Check validation and authorization for both Superadmin and Admin.
Test unauthorized access to restricted pages and actions.
Verify event creation, editing, viewing, and deletion when affected.
Check that calendar displays and date filters work as expected.
Verify relevant Blade views render correctly.
Check for regressions in existing features.
Do not claim tests passed unless they were actually run.
If testing is unavailable, state what remains unverified.

Use the project's existing testing tools and conventions.

13. Recommended Workflow for Every Task
Read the relevant files and understand the current implementation.
Identify the affected routes, controllers, models, views, and migrations.
Determine whether the task affects Superadmin permissions, Admin permissions, or both.
Implement the smallest safe change.
Review validation, authorization, security, and UI behavior.
Run relevant tests or checks.
Review the Git diff and ensure no unrelated or sensitive files are included.
Summarize the changes and any remaining work.
14. Final Response Requirements

After completing a task, provide:

Summary: What was implemented or fixed.
Files Changed: Which files were added, modified, or removed.
Permissions: Any role or authorization changes.
Testing: Checks performed and their actual results.
Follow-up: Any required migration, configuration, or manual testing.

Keep explanations clear, concise, and appropriate for a collaborative development team.

15. Important Instructions
Prioritize security, correctness, maintainability, and consistency.
Do not invent features or requirements.
Do not rewrite working functionality unnecessarily.
Do not make assumptions about the existing calendar library or database schema.
Ask for clarification before making significant changes to role permissions or data structures.
Explain potentially destructive operations before performing them.
Keep the application compatible with its existing Laravel version and dependencies.