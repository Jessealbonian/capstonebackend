# AthleTrack Backend

This repository contains the **backend and API of AthleTrack**, a digital routine reporting and coach-athlete interaction system developed as part of a capstone research project.

The backend is responsible for handling the application's **business logic, API requests, authentication, data processing, file uploads, routine management, report submissions, coach feedback, and MySQL database operations**.

AthleTrack was developed to provide a centralized digital platform for coaches and student-athletes. The system was initially implemented within the track and field program of Gordon College and was evaluated as part of a descriptive-developmental research study. The study reported positive user acceptance and practical value in supporting routine monitoring, accountability, communication, and organization.

---

## 🎯 Purpose

The backend serves as the central processing layer of AthleTrack.

It connects the Angular frontend with the MySQL database and handles the operations required to maintain the application's data and workflows.

```text
             ┌──────────────────────┐
             │   Angular Frontend   │
             │ TypeScript / PWA     │
             └──────────┬───────────┘
                        │
                   HTTP / API
                        │
                        ▼
             ┌──────────────────────┐
             │    PHP Backend      │
             │                     │
             │ Authentication      │
             │ Business Logic      │
             │ Validation          │
             │ File Handling       │
             │ API Responses       │
             └──────────┬───────────┘
                        │
                        ▼
             ┌──────────────────────┐
             │    MySQL Database    │
             │                     │
             │ Users               │
             │ Routines            │
             │ Reports             │
             │ Feedback            │
             │ History             │
             └──────────────────────┘
```

---

## 🧠 Backend Responsibilities

The backend handles the application's core operations, including:

* User authentication
* User authorization
* User management
* Routine creation
* Routine assignment
* Routine retrieval
* Routine status management
* Report submission
* Image uploads
* Report verification
* Coach comments
* Routine history
* Data validation
* Database transactions
* API responses
* Error handling

The research identified routine assignment, report submission with image uploads, routine history, and coach feedback as core system features.

---

## 👥 User Roles

### Student-Athlete

The backend provides operations allowing student-athletes to:

* View assigned routines
* Retrieve routine details
* Submit routine reports
* Upload image-based confirmation
* View previous submissions
* View report status
* Receive coach feedback

### Coach

Coaches can:

* Create routines
* Assign routines
* View assigned athletes
* Review submitted reports
* Provide comments
* Monitor routine compliance
* View routine history

The coach serves as the primary administrator and beneficiary within the initial implementation described in the research.

---

## 🔐 Authentication

The backend manages authentication and user access.

Authentication operations may include:

```text
POST /api/login
POST /api/logout
GET  /api/user
```

The backend verifies user credentials and returns the appropriate authentication information required by the frontend.

Authorization should ensure that users can only perform actions permitted by their assigned role.

For example:

```text
Student-Athlete
    │
    ├── View Own Routines
    ├── Submit Reports
    └── View Own History

Coach
    │
    ├── Create Routines
    ├── Assign Routines
    ├── Review Reports
    └── Provide Feedback
```

---

## 🏃 Routine Management

The backend manages the lifecycle of training routines.

A routine may contain:

* Routine ID
* Coach ID
* Athlete ID or assigned group
* Title
* Description
* Instructions
* Schedule
* Date
* Status
* Creation timestamp
* Update timestamp

Basic operations include:

```text
Create Routine
      ↓
Assign Routine
      ↓
Athlete Completes Routine
      ↓
Submit Report
      ↓
Coach Reviews
      ↓
Feedback
```

This reflects the conceptual framework of the research, where coach-assigned routines become inputs, athlete execution and reporting form the process, and improved communication, accountability, compliance, and organization are expected outputs.

---

## 📤 Report Submission

The backend processes reports submitted by student-athletes.

A report can contain:

* Routine ID
* Athlete ID
* Report description
* Image evidence
* Submission date
* Status
* Coach feedback

Example request:

```text
POST /api/reports
```

The backend validates the submission, processes uploaded images when applicable, stores the report information, and associates it with the appropriate routine and athlete.

Image-based confirmation was an explicit feature identified in the research questions and system implementation.

---

## 🖼️ Image Uploads

AthleTrack supports image-based confirmation of routine participation.

The backend is responsible for:

1. Receiving the uploaded image.
2. Validating the file.
3. Generating or assigning a safe storage path.
4. Saving the associated record.
5. Returning the image reference to the frontend.

A simplified workflow:

```text
Angular Frontend
       │
       │ Image Upload
       ▼
PHP API
       │
       ├── Validate File
       │
       ├── Store Image
       │
       └── Save Reference
              │
              ▼
          MySQL
```

---

## 💬 Coach Feedback

Coaches can provide comments on submitted reports.

Example:

```text
POST /api/reports/{id}/feedback
```

Feedback can be associated with the specific report so that the athlete can retrieve it through the frontend.

This creates a structured communication record rather than relying entirely on informal messaging.

The research identified coach comments as a mechanism for providing timely and structured feedback and improving coach-athlete communication.

---

## 📊 Routine History

The backend maintains records of routine assignments and submitted reports.

This allows the system to retrieve historical information such as:

* Previous routines
* Completed routines
* Pending reports
* Submitted reports
* Review status
* Coach feedback
* Submission dates

This centralized history supports the research objective of making routine participation more organized, transparent, and traceable.

---

## 🗄️ Database

AthleTrack uses **MySQL** as its primary relational database.

A conceptual database structure may include:

```text
users
  │
  ├──────────────┐
  │              │
  ▼              ▼
routines       reports
  │              │
  │              └──────► report_images
  │
  └──────────────► routine_assignments
                         │
                         ▼
                       users

reports
   │
   ▼
feedback
```

Possible tables include:

* `users`
* `roles`
* `routines`
* `routine_assignments`
* `reports`
* `report_images`
* `feedback`

The exact database structure depends on the implementation of the project.

---

## 🌐 API Structure

The backend exposes API endpoints that can be consumed by the Angular frontend.

Example endpoint structure:

```text
/api
│
├── /auth
│   ├── login
│   └── logout
│
├── /users
│
├── /routines
│   ├── GET
│   ├── POST
│   ├── PUT
│   └── DELETE
│
├── /reports
│   ├── GET
│   ├── POST
│   └── PUT
│
├── /feedback
│
└── /uploads
```

The actual endpoint names and HTTP methods should follow the implementation of the project.

---

## 🔄 Example API Workflow

### Creating a Routine

```text
Coach
  │
  ▼
Angular Frontend
  │
  │ POST /api/routines
  ▼
PHP Backend
  │
  ├── Validate Request
  ├── Validate Coach
  ├── Process Data
  └── Save Routine
          │
          ▼
       MySQL
          │
          ▼
     API Response
          │
          ▼
      Angular App
```

### Submitting a Report

```text
Athlete
  │
  ▼
Complete Routine
  │
  ▼
Upload Report + Image
  │
  ▼
Angular Frontend
  │
  ▼
PHP API
  │
  ├── Validate Submission
  ├── Process Image
  ├── Save Report
  └── Update Status
          │
          ▼
       MySQL
```

---

## 🛠️ Technology Stack

| Technology       | Purpose                                    |
| ---------------- | ------------------------------------------ |
| **PHP**          | Backend/server-side development            |
| **MySQL**        | Relational database                        |
| **REST API**     | Communication between frontend and backend |
| **Angular**      | Frontend client                            |
| **TypeScript**   | Frontend application logic                 |
| **Tailwind CSS** | Frontend styling                           |
| **PWA**          | Frontend application delivery              |

---

## 📁 Suggested Backend Structure

```text
athletrack-backend/
│
├── api/
│   ├── auth/
│   ├── users/
│   ├── routines/
│   ├── reports/
│   └── feedback/
│
├── config/
│   └── database.php
│
├── controllers/
│
├── models/
│
├── middleware/
│
├── uploads/
│   └── reports/
│
├── routes/
│
├── database/
│   └── schema.sql
│
└── README.md
```

The actual structure may vary depending on the PHP architecture used in the implementation.

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone <backend-repository-url>
cd athletrack-backend
```

### 2. Configure PHP

Ensure that PHP and a compatible web server are installed and configured.

### 3. Configure MySQL

Create a database for AthleTrack.

Example:

```sql
CREATE DATABASE athletrack;
```

Import the project's database schema if one is provided.

### 4. Configure Database Credentials

Update the backend configuration with the appropriate database information.

Example:

```php
<?php

$host = "localhost";
$dbname = "athletrack";
$username = "root";
$password = "";

$conn = new PDO(
    "mysql:host=$host;dbname=$dbname",
    $username,
    $password
);
```

The exact configuration should follow the project's implementation.

### 5. Configure Upload Storage

Ensure that the backend has permission to store uploaded report images.

Example:

```text
uploads/
└── reports/
```

### 6. Start the Backend

The backend can be served using the configured PHP/web server environment.

The frontend should then be configured to communicate with the backend's API URL.

---

## 🧪 Testing

Backend testing should cover the main system workflows.

### Authentication

* Valid login
* Invalid login
* Unauthorized requests
* Role-based access

### Routines

* Create routine
* Update routine
* Assign routine
* Retrieve routine
* Delete routine

### Reports

* Submit report
* Upload image
* Validate required information
* Retrieve report
* Update report status

### Feedback

* Add coach feedback
* Retrieve feedback
* Associate feedback with the correct report

### Database

* Correct record creation
* Correct relationships
* Data validation
* Error handling

---

## 🔒 Security Considerations

Because the backend handles user accounts, athlete information, reports, and uploaded images, appropriate security practices should be applied.

Recommended measures include:

* Password hashing
* Input validation
* Prepared SQL statements
* Role-based authorization
* Secure file upload validation
* Restricted upload directories
* API authentication
* Protection against unauthorized record access
* Error handling without exposing sensitive database information

---

## 🎓 Research Validation

AthleTrack is not only a conceptual software project. It was developed and evaluated as part of a capstone research study.

The study used a descriptive-developmental research design and involved active Gordon College track and field student-athletes and their coach.

The reported findings showed:

* Frequent challenges in balancing academic and athletic responsibilities.
* Interest in a structured digital solution.
* Positive willingness to use a web-based platform.
* Positive readiness toward adopting new technology.
* Positive perception of AthleTrack's potential benefits.

The overall weighted mean reported in the study was **3.32, described as "Agree,"** indicating a generally positive perception of the system among respondents.

The study concluded that AthleTrack supports structured routine assignment, image-based routine reporting, coach feedback, accountability, organization, and transparency. It also reported positive acceptance in terms of perceived usefulness, perceived ease of use, and behavioral intention to use.

---

## 🌱 Scalability

Although the initial implementation focused on track and field athletes, the backend is intended to provide a foundation that can potentially support other athletic programs.

Possible future implementations include:

```text
AthleTrack
    │
    ├── Track & Field
    ├── Basketball
    ├── Volleyball
    ├── Football
    ├── Other Physical Sports
    ├── Board Games
    └── Intellectual Competitions
```

The research specifically identifies future expansion to other sports disciplines and the possibility of integrating academic workload tracking and enhanced analytics.

---

## 📌 Project Information

| Category                   | Details                        |
| -------------------------- | ------------------------------ |
| **Project**                | AthleTrack                     |
| **Repository**             | Backend                        |
| **Backend Language**       | PHP                            |
| **Database**               | MySQL                          |
| **API**                    | REST-based                     |
| **Frontend**               | Angular / TypeScript           |
| **Application Type**       | PWA-enabled Web Application    |
| **Initial Implementation** | Gordon College Track and Field |
| **Project Type**           | Capstone Research Project      |

---

## 📄 Related Repository

This repository contains the **backend/API** of AthleTrack.

The frontend is maintained separately:

```text
AthleTrack
├── athletrack-frontend
└── athletrack-backend
```

The frontend repository contains the Angular, TypeScript, HTML, CSS, and Tailwind implementation, while this repository contains the PHP backend and MySQL data layer.

---

## 📚 Research Reference

**Albonian, J. A. B., Espinorio, H. T., & Punzalan, D. L. B.**
*Enhancing Coach–Athlete Interaction Through a PWA-Enabled Angular Web Application for Routine Reporting of Track and Field Athletes at Gordon College.*

---

**AthleTrack Backend**

*Powering structured routine reporting, monitoring, and coach-athlete communication.*
