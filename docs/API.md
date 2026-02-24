# ConnectMind REST API

Base URL: `/api`. All endpoints require authentication (Bearer token via Laravel Sanctum).

## Authentication

- **Register (web):** `POST /register` (name, email, password, password_confirmation)
- **Login (web):** `POST /login` (email, password)
- **API token:** `POST /api/auth/token` with body `email`, `password`. Returns `{ "token": "...", "token_type": "Bearer" }`. Send header: `Authorization: Bearer {token}`

After login, the tenant is set from the authenticated user; all data is scoped to that tenant.

---

## Contacts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/contacts` | List contacts (paginated). Query: `search`, `tag_id`, `per_page` |
| POST | `/api/contacts` | Create contact. Body: `name`, `email?`, `phone?`, `notes?`, `tags?` (array of tag ids) |
| GET | `/api/contacts/{id}` | Get contact (with tags, interactions) |
| PUT/PATCH | `/api/contacts/{id}` | Update contact |
| DELETE | `/api/contacts/{id}` | Delete contact |

---

## Interactions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/contacts/{contact}/interactions` | List interactions for a contact (paginated) |
| POST | `/api/interactions` | Create interaction. Body: `contact_id`, `type` (meeting\|call\|email), `title?`, `notes?`, `occurred_at` |
| GET | `/api/interactions/{id}` | Get interaction |
| PUT/PATCH | `/api/interactions/{id}` | Update interaction |
| DELETE | `/api/interactions/{id}` | Delete interaction |

Creating an interaction with `notes` dispatches a job to generate an AI summary (if OpenAI is configured).

---

## Tags

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tags` | List all tags |
| POST | `/api/tags` | Create tag. Body: `name`, `color?` |
| PUT/PATCH | `/api/tags/{id}` | Update tag |
| DELETE | `/api/tags/{id}` | Delete tag |

---

## Reminders

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reminders` | List reminders. Query: `upcoming=1`, `per_page` |
| POST | `/api/reminders/{contact}` | Create reminder for contact. Body: `remind_at`, `type?`, `message?` |

---

## AI

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/ai/stay-in-touch` | Generate “stay in touch” message. Body: `contact_id`, `context?` |
| POST | `/api/ai/suggest-reminders` | Get follow-up reminder suggestions. Body: `contact_id` |

These endpoints are protected by the OpenAI cost guard (daily token limit per tenant).

---

## Response format

- Success: JSON with resource or collection; create returns `201`.
- Validation errors: `422` with `message` and `errors` object.
- Not found: `404` with `message`.
- Cost limit (AI): `429` with `message`.
