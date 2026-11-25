# MigraineDiary

A Laravel module for recording migraine attacks and related symptoms, triggers, and medication. It includes user-facing pages and a versioned API, reporting tools, and admin dictionary management.

## Features

- Create, view, update, delete, and end a user's migraine attacks.
- Dashboard, active-attack, and statistics endpoints.
- Read symptom, trigger, and medication dictionaries and translations.
- Export reports as PDF or Excel, or send a report by email.
- Admin API to list, create, view, update, and delete dictionary entries.
- Policy-protected attack access, Livewire calendar, and localized views.

## API

User endpoints are under `/api/v1/migraine-diary` and require `auth:sanctum`.

| Method | Endpoint | Purpose | Access |
| --- | --- | --- | --- |
| `GET` | `/api/v1/migraine-diary/dashboard` | Dashboard data | `auth:sanctum` |
| `GET` | `/api/v1/migraine-diary/attacks/active` | Active attack | `auth:sanctum` |
| `GET`, `POST` | `/api/v1/migraine-diary/attacks` | List or create attacks | `auth:sanctum` |
| `GET`, `PUT/PATCH`, `DELETE` | `/api/v1/migraine-diary/attacks/{attack}` | Read, update, or delete an attack | `auth:sanctum` |
| `POST` | `/api/v1/migraine-diary/attacks/{attack}/end` | End an attack | `auth:sanctum` |
| `GET` | `/api/v1/migraine-diary/dictionaries` | User dictionary data | `auth:sanctum` |
| `GET` | `/api/v1/migraine-diary/statistics` | Statistics | `auth:sanctum` |
| `POST` | `/api/v1/migraine-diary/reports/email` | Send a report by email | `auth:sanctum` |
| `POST` | `/api/v1/migraine-diary/reports/excel` | Download an Excel report | `auth:sanctum` |
| `POST` | `/api/v1/migraine-diary/reports/pdf` | Download a PDF report | `auth:sanctum` |
| `GET` | `/api/v1/migraine-diary/translations` | UI translations | `auth:sanctum` |
| `GET` | `/api/v1/admin/migraine-diary/dictionaries` | List all admin dictionaries | `auth:sanctum`, `is_admin` |
| `GET`, `POST` | `/api/v1/admin/migraine-diary/dictionaries/{type}` | List or create dictionary entries (`symptoms`, `triggers`, `meds`) | `auth:sanctum`, `is_admin` |
| `GET`, `PATCH`, `DELETE` | `/api/v1/admin/migraine-diary/dictionaries/{type}/{id}` | Read, update, or delete an entry | `auth:sanctum`, `is_admin` |

The route service provider adds the `/api` prefix shown above. The host may add further prefixes; check its route list for the final URL.

## Installation in a host application

This repository is intended to live at `Modules/MigraineDiary` in a host application using `nwidart/laravel-modules`. The host must provide Laravel, Sanctum, Livewire, Laravel UI, `maatwebsite/excel`, and `google/apiclient` as applicable. Run dependency installation from the host project root so its Composer merge-plugin and lockfile are used.

```sh
git clone https://github.com/mikitosina1/migraine_diary.git Modules/MigraineDiary
php artisan module:enable MigraineDiary
php artisan migrate
```

The service provider loads this module's migrations automatically when the module is registered. If your host does not load the provider or module migrations automatically, check `php artisan module:migrate MigraineDiary` and the module package configuration before running manual migration commands.

Build front-end assets from the host root, where the host Vite configuration discovers module assets:

```sh
npm install
npm run build
```

## Requirements and compatibility

The module's current `composer.json` declares PHP `^8.1`, Laravel `^10.10`, Laravel UI `^4.5`, `nwidart/laravel-modules` `^10.0`, Excel `^3.1`, and Google API Client `^2.18`. The module is also maintained inside a newer host application; compatibility with a different host version depends on the host's dependency resolution and should be checked there. See `composer.json`, `package.json`, and `module.json` for current metadata.

## Development

Feature tests are under `Tests/Feature`. Run them from the host project so the module uses the host's test bootstrap and database configuration.
