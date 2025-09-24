# OMX Sync

OMX Sync is a Laravel-based employee task and grading system.  
It provides a dashboard to track tasks, attendance, and automatically calculate performance grades for employees.

## Features
- 📊 Dashboard with charts and statistics  
- ✅ Task management (pending, in process, completed, delayed)  
- 🕒 Attendance tracking with punctuality points  
- 🏅 Automatic employee grading system based on performance & attendance  

## Tech Stack
- [Laravel](https://laravel.com/) 10+  
- [MySQL](https://www.mysql.com/)  
- [Bootstrap](https://getbootstrap.com/) for UI  
- [Chart.js](https://www.chartjs.org/) for graphs  

## API route is currently in wep.php, with the json data as requested.
## Did not created a seperate employee table and used the existing user table with roles (admin and employee)

## Installation

1. Clone the repository:
   git clone https://github.com/RAJ181126/omxSync.git
   cd omxSync
   
3. Install dependencies:
   composer install
   npm install && npm run dev
   
4. Migration and Seeder
   php artisan migrate --seed

5. Admin User and Password
    Email: admin@omx.com
    Password: password

7. Employee with Data
       Email: employee1@omx.com
       Password: password
       
