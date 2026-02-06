# univoice

# 1. Project Title

**UniVoice – Formalized Student Complaint Management System**

# 2. Project Description

**UniVoice** is a web-based complaint management system created specifically for **UiTM Cawangan Kelantan**. The platform serves as a professional communication channel, ensuring that student concerns regarding campus facilities, academics, or welfare reach administrative leadership efficiently. It operates under the guiding principle: *"Integrity in Service, Excellence in Action."*

---

# 3. Features

### **User Authentication**

* **Secure Access:** Registration, Login, and Logout system.
* **Separate Roles:** Dedicated dashboards for **Admin** and **Students**.

### **User (Student) Features**

* **Submit Complaints:** Digital form to report issues directly to the administration.
* **Browse Portal:** Access to the official student portal landing page.
* **Personal Dashboard:** View status updates on submitted concerns.

### **Admin Features**

* **Dashboard Overview:** Centralized view of total, pending, and resolved complaints.
* **Manage Submissions:** Add, edit, delete, or update the status of student reports.
* **System Analytics:** View complaint trends and statistics via interactive charts.

### **Other Features**

* **Responsive UI:** Optimized for both desktop and mobile viewing.
* **Clean Navigation:** Role-based navigation bar for seamless user experience.
* **Smooth UX:** Features popup notifications and fluid UI animations.

---

# 4. Test Login (Demo Accounts)

| Role | Email / Username | Password |
| --- | --- | --- |
| **Admin Account** - ADMIN001 | `admin@uitm.edu.my` | `admin123` |
                    - ADMIN002  | `kelantan.admin@uitm.edu.my` | `uitm2024` |
                    
| **User Account** | `any account can`|

---

# 5. Framework / Libraries Used

* **HTML & CSS:** Structure and custom university-themed styling.
* **JavaScript:** Frontend logic and interactivity.
* **PHP:** Server-side processing and database management.
* **Chart.js:** For administrative data visualization.
* **Font Awesome:** For professional iconography.
* **Google Fonts:** Clean and modern typography.

---

# 6. How to Setup

Follow these steps to get the project running on your local machine:

### **Prerequisites**

* Download and install **XAMPP** or **WAMP**.
* Web Browser (Chrome, Edge, etc.).

### **Installation Steps**

1. **Clone/Download:** Place the project folder inside your `C:/xampp/htdocs/` directory.
2. **Database Setup:**
* Open XAMPP and start **Apache** and **MySQL**.
* Go to `http://localhost/phpmyadmin/`.
* Create a new database named `univoice`.
* Import the `univoice.sql` file (found in the project folder).


3. **Configuration:**
* Open `config.php` or your connection file and ensure the database credentials match your local settings.


4. **Run the Project:**
* In your browser, go to: `http://localhost/univoice/index.php`.




