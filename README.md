# 8D Problem Solving Platform – MVP

This project is a **Full Stack MVP** that digitizes key steps of the **8D Problem Solving Methodology**, focusing on:

* **Problem Definition (D1–D2)**
* **Root Cause Analysis & Permanent Action (D4–D5)**

The purpose of this case study is to demonstrate:

* Recursive / tree data modeling in a relational database
* RESTful API design with native PHP
* Frontend rendering of hierarchical data structures
* Proper usage of **Siemens iX Design System** components

---

## Tech Stack

### Frontend

* React
* Siemens iX Design System
* AG Grid (problem list)
* REST API integration via `fetch`

### Backend

* PHP (Native, no framework)
* RESTful JSON API
* PDO for database access

### Database

* MySQL
* Relational schema using `parent_id` for tree structures

---

## Application Structure

The application consists of two main sections:

---

## A. Dashboard – Problem Definition (D1–D2)

### Problem List

* Lists all problems in a tabular format
* Implemented using **AG Grid**

**Columns:**

* ID
* Title
* Responsible Team
* Status (Open / Closed)
* Created At

### Create New Problem

* Triggered by a **“Create New Problem”** button
* Opens a **Siemens iX Modal**
* Input fields:

  * Title
  * Detailed Description (D2)
  * Responsible Team (D1)

---

## B. Root Cause Analysis & Action (D4–D5)

When a problem is selected, the user is redirected to the **Problem Detail Page**.

### 1. Root Cause Tree (5-Why Analysis)

* Root causes are modeled as a **recursive tree structure**
* Each node represents a "Why?" question
* Unlimited nesting depth is supported

**Example:**

Machine Stopped
└── Fuse Blown
└── Overload

### Frontend Representation

* Displayed as a hierarchical (indented) structure
* Implemented using recursive React components
* Siemens iX components are used for layout, inputs, and actions

---

### 2. Root Cause & Permanent Action

* Any node in the tree can be marked as a **Root Cause**
* When marked:

  * An input field appears
  * A **Permanent Action (D5)** can be defined

---

## Data Model

### Problems Table (Simplified)

* `id`
* `title`
* `description`
* `crew_id`
* `status`
* `created_at`

### Root Causes Tree Table

* `id`
* `problem_id`
* `parent_id` (nullable)
* `description`
* `is_root_cause`
* `action`
* `created_at`

This structure enables efficient recursive tree building using `parent_id` relationships.

---

## API Endpoints

The backend exposes a REST-style JSON API under the `/8d` prefix.

### Crew

* `GET /8d/crew/health`
* `GET /8d/crew`

### User

* `GET /8d/user/health`
* `GET /8d/user/{id}`
* `GET /8d/me`

### Problems

* `GET /8d/problems/health`
* `GET /8d/problems`
* `GET /8d/problems/{id}`
* `POST /8d/problems`

### Solutions (D5)

* `GET /8d/solutions/health`
* `GET /8d/solutions/{id}`
* `GET /8d/problems/{id}/solutions`
* `POST /8d/solutions`

### Root Causes Tree (D4)

* `GET /8d/rootcauses/health`
* `GET /8d/rootcauses/{id}`
* `GET /8d/rootcauses/{problem_id}/tree`
* `POST /8d/rootcauses`
* `PATCH /8d/rootcauses/{id}/is_root_cause`

All endpoints return **JSON** responses.

---

## Local Development Setup

Local development is fully managed via **Docker** and **Makefile**.

### Available Commands

```bash
make info
```

Displays all available commands.

### Build the System

```bash
make build
```

Builds all Docker images using `docker compose`.

### Run the Application

```bash
make run
```

* Builds the images (if not already built)
* Starts all containers in detached mode

### Clean Environment

```bash
make clean
```

Stops and removes all containers, volumes, and orphan resources.

### Rebuild from Scratch

```bash
make rebuild
```

Cleans the environment and rebuilds the entire system.

### Run Tests

```bash
make test
```

Runs available tests inside the application container (if any).

---

## Constraints & Dependencies

Before running the project locally, ensure the following requirements are met:

### System Requirements

* **Docker** and **Docker Compose** must be installed
* **Make** must be available on the system
* **Port 80 must be free** (used by the reverse proxy / frontend container)

  * If port 80 is already in use, the containers will fail to start
  * You may stop the conflicting service or adjust port mappings in `docker-compose.yml`

### Runtime Dependencies

* The application relies on Docker networking for internal service communication
* Backend and frontend containers must be running together
* Environment variables are loaded via Docker configuration

### Known Limitations

* This is an **MVP / case study**, not a production-hardened system
* No authentication or authorization layer is implemented
* Error handling and validation are intentionally minimal

---

## Notes

* This project is an **MVP / case study**, not a production-ready system
* Focus is on data modeling, architecture, and clarity
* Siemens iX Design System is used consistently across the UI
