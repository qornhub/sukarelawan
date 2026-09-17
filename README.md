# SukaRelawan: A Web-Based Volunteer Management Platform 🤝

[![Laravel Framework](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![React Native](https://img.shields.io/badge/React_Native-20232A?style=for-the-badge&logo=react&logoColor=61DAFB)](https://reactnative.dev/)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev/)
[![Expo](https://img.shields.io/badge/Expo-000000?style=for-the-badge&logo=expo&logoColor=white)](https://expo.dev/)

**SukaRelawan** is an end-to-end web and mobile volunteer management platform designed to streamline coordination between volunteers and Non-Governmental Organizations (NGOs) in Malaysia. Developed using an **Agile Prototyping** methodology, SukaRelawan addresses administrative bottlenecks, inefficient recruitment, and poor volunteer retention by integrating real-time attendance tracking, gamified rewards, and role-based task delegation into a unified ecosystem.

---

## 🌟 Key Features

*   **Role-Based Access Control (RBAC):** Customized features and dashboards tailored for Volunteers, NGOs, and Platform Administrators.
*   **Event Discovery & Filtering:** Advanced search capabilities allowing volunteers to filter events by skill requirements, UN Sustainable Development Goals (SDGs), dates, and locations.
*   **Automated QR Attendance Tracking:** Seamless event check-in powered by a dedicated mobile scanner application that instantly updates attendance records and grants reward points.
*   **Gamified Reward System:** Automatically awards points upon attendance, allowing volunteers to unlock tiered rank/skill badges and monitor progress via a global leaderboard.
*   **NGO Task Delegation Module:** Enables organizers to assign specific, structured roles and tasks to confirmed volunteers directly from the NGO dashboard.
*   **Community Blog & Feedback System:** Multi-directional communication module featuring event reviews, post-event feedback, and community story sharing.
*   **Real-time Notifications:** Driven by WebSockets (Pusher) to send instant operational updates and attendance logs.

---

## 🏗️ System Architecture & Tech Stack

### Web Platform (Frontend & Backend)
*   **Framework:** Laravel (PHP)[cite: 1]
*   **Build Tool & Styling:** Vite, Bootstrap Framework[cite: 1]
*   **Rich Text Editor:** TinyMCE[cite: 1]
*   **Real-Time Service:** Pusher[cite: 1]

### Mobile Attendance Scanner App
*   **Framework:** React Native with Expo Go[cite: 1]

### Database & Infrastructure
*   **Database:** MySQL (XAMPP Environment)[cite: 1]
*   **Deployment:** Cloud-hosted via Railway[cite: 1]
*   **Version Control:** Git & GitHub[cite: 1]

---

## 🚀 Getting Started

Follow these instructions to set up the project locally for development and testing.

### Prerequisites

Ensure you have the following installed on your local machine:
*   [PHP](https://www.php.net/) (>= 8.1)
*   [Composer](https://getcomposer.org/)
*   [Node.js](https://nodejs.org/) & NPM
*   [XAMPP](https://www.apachefriends.org/) (for MySQL)
*   [Expo Go App](https://expo.dev/) (on your mobile device for testing the scanner)

---

### Installation & Local Setup

#### 1. Web Application (Laravel Backend & Frontend)

```bash
# Clone the repository
git clone [https://github.com/your-username/sukarelawan.git](https://github.com/your-username/sukarelawan.git)
cd sukarelawan

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment Configuration
cp .env.example .env
