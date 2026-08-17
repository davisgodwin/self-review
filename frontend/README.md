# Self Review (SR)

> **One hour with God. One year of transformation.**

Self Review (SR) is a faith-centered digital platform designed to help young people build a consistent relationship with God through **Bible study, prayer, self-reflection, accountability, community, and personal growth**.

SR combines spiritual discipline with meaningful social connection, creating a space where users can study, reflect, grow, encourage others, and track their journey throughout the year.

---

## ✨ Vision

Self Review exists to help people intentionally dedicate time to God every day while developing discipline and becoming better versions of themselves.

The platform is built around a simple principle:

> **Spend one intentional hour with God every day and document the journey.**

---

## 🎯 Core Objectives

SR allows users to:

* 📖 Study the Bible consistently
* 🙏 Build a consistent prayer life
* 📝 Reflect on their day and personal growth
* 🎯 Set and track personal goals
* 👥 Connect with like-minded people
* 🤝 Join accountability and study groups
* 💬 Encourage others through meaningful conversations
* 🎨 Discover and showcase talents
* 📊 Track their yearly growth
* 🌱 Build sustainable habits and discipline

---

## 🚀 Core Features

### Authentication

* User registration
* User login
* Secure authentication
* Password protection
* Protected routes
* User sessions

### Onboarding

Users personalize their SR experience by selecting:

* Spiritual goals
* Preferred study time
* Interests
* Talents
* Hobbies
* Daily goals
* Personal preferences

### SR Hour

The central experience of Self Review.

Users can dedicate one hour to:

* Bible study
* Prayer
* Personal reflection
* Journaling
* Spiritual growth

### Reflection

Users can record daily reflections such as:

* What did I learn?
* What challenged me?
* What victory did I experience?
* What am I praying about?
* What will I apply tomorrow?

### Community

Users will eventually be able to:

* Share testimonies
* Share prayer requests
* Share lessons
* Encourage others
* Comment
* React
* Follow users
* Discover meaningful content

### Groups

Users can create or join:

* Bible study groups
* Prayer groups
* Accountability groups
* Student groups
* Entrepreneur groups
* Creative groups
* Technology groups
* Fitness groups

### Messaging

Planned communication features include:

* Private messaging
* Group conversations
* Voice notes
* Prayer requests
* Media sharing

### Midnight Room

A dedicated space for users during the SR late-night period.

Possible room types:

* Silent study
* Prayer
* Worship
* Group discussion

### Growth System

SR will use meaningful progress mechanics to help users visualize consistency.

Possible features:

* Streaks
* Progress
* Achievements
* Growth levels
* Personal milestones
* Virtual growth environment

The goal is not to turn faith into a game, but to help users **see their consistency and progress**.

### Talent Network

Users will eventually be able to showcase skills such as:

* Programming
* Graphic design
* Photography
* Music
* Writing
* Business
* Video production
* Public speaking
* Art

### Yearly Review

At the end of each year, users receive a personal growth report containing statistics such as:

* Bible study hours
* Days completed
* Reflections written
* Prayer activity
* Community participation
* Groups joined
* Skills developed
* Personal growth milestones

---

# 🏗️ System Architecture

```text
                         SELF REVIEW
                              │
                              ▼
                    ┌───────────────────┐
                    │  React + Vite     │
                    │     Frontend      │
                    └─────────┬─────────┘
                              │
                         REST API
                              │
                              ▼
                    ┌───────────────────┐
                    │   PHP Backend     │
                    │      API          │
                    └─────────┬─────────┘
                              │
                              ▼
                    ┌───────────────────┐
                    │ Aiven PostgreSQL  │
                    │     Database      │
                    └─────────┬─────────┘
                              ▲
                              │
                         ┌────┴────┐
                         │ DBeaver │
                         └─────────┘
```

### Deployment

```text
GitHub
   │
   ├── Frontend
   │      ↓
   │   Render
   │
   └── Backend
          ↓
       Render
          │
          ↓
       Aiven
     PostgreSQL
```

---

# 🛠️ Technology Stack

## Frontend

* React
* Vite
* React Router
* Tailwind CSS
* Axios

## Backend

* PHP
* REST API
* PostgreSQL

## Database

* PostgreSQL
* Aiven
* DBeaver

## Deployment

* GitHub
* Render

---

# 📁 Project Structure

```text
self-review/
│
├── frontend/
│   ├── public/
│   └── src/
│
├── backend/
│   ├── public/
│   ├── config/
│   ├── routes/
│   ├── controllers/
│   ├── models/
│   ├── middleware/
│   ├── services/
│   └── helpers/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema/
│
├── docs/
│
├── .gitignore
└── README.md
```

---

# 🗺️ Development Roadmap

## Phase 1 — Foundation

* [ ] Project setup
* [ ] React + Vite setup
* [ ] PHP backend setup
* [ ] PostgreSQL database setup
* [ ] GitHub repository
* [ ] Render deployment
* [ ] Aiven database connection
* [ ] DBeaver configuration
* [ ] Authentication
* [ ] Onboarding
* [ ] Initial dashboard

## Phase 2 — SR Core

* [ ] SR Hour
* [ ] Bible study
* [ ] Study timer
* [ ] Study notes
* [ ] Verse highlighting
* [ ] Prayer
* [ ] Reflection journal
* [ ] Study history

## Phase 3 — Growth

* [ ] Daily progress
* [ ] Streaks
* [ ] Personal goals
* [ ] Growth statistics

## Phase 4 — Community

* [ ] Community feed
* [ ] Posts
* [ ] Comments
* [ ] Reactions
* [ ] Prayer requests
* [ ] Testimonies
* [ ] User discovery

## Phase 5 — Groups

* [ ] Create groups
* [ ] Join groups
* [ ] Group profiles
* [ ] Group posts
* [ ] Group study plans
* [ ] Group activities

## Phase 6 — Messaging

* [ ] Private conversations
* [ ] Group messaging
* [ ] Read status
* [ ] Voice notes
* [ ] Media sharing

## Phase 7 — Midnight Rooms

* [ ] Study rooms
* [ ] Prayer rooms
* [ ] Worship rooms
* [ ] Group discussions
* [ ] Live participant count
* [ ] Room moderation

## Phase 8 — Growth System

* [ ] Progress system
* [ ] Achievements
* [ ] Streak system
* [ ] Growth levels
* [ ] Personal virtual world

## Phase 9 — Talent Network

* [ ] Talent profiles
* [ ] Portfolios
* [ ] Skill discovery
* [ ] Mentorship opportunities

## Phase 10 — Yearly Review

* [ ] Annual statistics
* [ ] Personal growth report
* [ ] Journey summary
* [ ] Sponsorship applications

## Phase 11 — Administration

* [ ] Admin dashboard
* [ ] User management
* [ ] Content moderation
* [ ] Group management
* [ ] Reports
* [ ] Sponsorship management
* [ ] Analytics

---

# 🔐 Security

Security is a core requirement of SR.

The platform will implement:

* Secure password hashing
* Authentication middleware
* Protected API endpoints
* Input validation
* SQL injection protection
* Authorization controls
* Rate limiting
* Content reporting
* User blocking
* Administrative moderation
* Secure environment variables

Sensitive credentials must never be committed to GitHub.

---

# 🌍 Deployment

The project is designed to use:

**GitHub**

Source-code management and version control.

**Render**

Frontend and PHP backend deployment.

**Aiven**

PostgreSQL database hosting.

**DBeaver**

Database development and administration.

---

# 📌 Project Status

**Current Status:** 🚧 In Development

Self Review is currently being developed as a real-world product with a phased development approach.

The initial focus is on establishing the technical foundation, authentication, onboarding, and core user experience before expanding into community, messaging, groups, live rooms, and the growth ecosystem.

---

# 🤝 Contribution

Self Review is currently being developed by the founding team.

Contribution guidelines will be added as the project moves toward broader development.

---

# 📄 License

License information will be added when the project's ownership and distribution policy are finalized.

---

## Self Review

**One hour with God. One year of transformation.**
