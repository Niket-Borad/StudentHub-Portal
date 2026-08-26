/*-------------- Login Page ----------------*/

function loginsuccess() {
    const username = document.getElementById("username");
    const password = document.getElementById("password");

    const usernameValue = username.value.trim();
    const passwordValue = password.value.trim();

    username.style.border = "1px solid #ccc";
    password.style.border = "1px solid #ccc";

    if (usernameValue === "" || passwordValue === "") {

        if (usernameValue === "") {
            username.style.border = "2px solid red";
        }

        if (passwordValue === "") {
            password.style.border = "2px solid red";
        }

        showNotification("Please enter both username and password.");
        return false;
    }

    if (usernameValue === "25DCE011" &&
        passwordValue === "niket9953") {

        showNotification("Successfully Logged In!");

        setTimeout(function() {
            window.location.href = "dashboard.html";
        }, 1000);

        return false;
    }

    showNotification("Invalid login credentials.");
    return false;
}


/*-------------- Registration Page ----------------*/

function register() {

    const name = document.getElementById("name");
    const student = document.getElementById("student");
    const date = document.getElementById("date");
    const mobile = document.getElementById("mobile");
    const college = document.getElementById("college");
    const address = document.getElementById("add");
    const course = document.getElementById("course");
    const division = document.getElementById("divison");
    const year = document.getElementById("year");

    const genderSelected =
        document.querySelector('input[name="gender"]:checked');

    const nameValue = name.value.trim();
    const studentValue = student.value.trim();
    const dateValue = date.value.trim();
    const mobileValue = mobile.value.trim();
    const collegeValue = college.value.trim();
    const addressValue = address.value.trim();
    const courseValue = course.value.trim();
    const divisionValue = division.value.trim();
    const yearValue = year.value.trim();

    name.style.border = "1px solid #ccc";
    student.style.border = "1px solid #ccc";
    date.style.border = "1px solid #ccc";
    mobile.style.border = "1px solid #ccc";
    college.style.border = "1px solid #ccc";
    address.style.border = "1px solid #ccc";
    course.style.border = "1px solid #ccc";
    division.style.border = "1px solid #ccc";
    year.style.border = "1px solid #ccc";

    let isValid = true;

    if (nameValue === "") {
        name.style.border = "2px solid red";
        isValid = false;
    }

    if (studentValue === "") {
        student.style.border = "2px solid red";
        isValid = false;
    }

    if (dateValue === "") {
        date.style.border = "2px solid red";
        isValid = false;
    }

    if (!genderSelected) {
        isValid = false;
    }

    if (mobileValue === "") {
        mobile.style.border = "2px solid red";
        isValid = false;
    }

    if (collegeValue === "" || !collegeValue.includes("@")) {
        college.style.border = "2px solid red";
        isValid = false;
    }

    if (addressValue === "") {
        address.style.border = "2px solid red";
        isValid = false;
    }

    if (courseValue === "") {
        course.style.border = "2px solid red";
        isValid = false;
    }

    if (divisionValue === "") {
        division.style.border = "2px solid red";
        isValid = false;
    }

    if (yearValue === "") {
        year.style.border = "2px solid red";
        isValid = false;
    }

    if (!isValid) {
        showNotification("Please fill in all required fields.");
        return false;
    }

    showNotification("Successfully registered!");

    document.querySelector("form").reset();

    return false;
}


/*-------------- Result Page ----------------*/

function result(event) {

    if (event) {
        event.preventDefault();
    }

    const name = document.getElementById("name");
    const degree = document.getElementById("degree");
    const sem = document.getElementById("sem");
    const exam = document.getElementById("exam");
    const id = document.getElementById("ID");

    const nameValue = name.value.trim();
    const degreeValue = degree.value.trim();
    const semValue = sem.value.trim();
    const examValue = exam.value.trim();
    const idValue = id.value.trim();

    name.style.border = "1px solid #ccc";
    degree.style.border = "1px solid #ccc";
    sem.style.border = "1px solid #ccc";
    exam.style.border = "1px solid #ccc";
    id.style.border = "1px solid #ccc";

    let isValid = true;

    if (nameValue === "" || nameValue === "Select Institute") {
        name.style.border = "2px solid red";
        isValid = false;
    }

    if (degreeValue === "" || degreeValue === "Select Degree") {
        degree.style.border = "2px solid red";
        isValid = false;
    }

    if (semValue === "" || semValue === "Select Semester") {
        sem.style.border = "2px solid red";
        isValid = false;
    }

    if (examValue === "" || examValue === "Select Month") {
        exam.style.border = "2px solid red";
        isValid = false;
    }

    if (idValue === "") {
        id.style.border = "2px solid red";
        isValid = false;
    }

    if (!isValid) {
        showNotification("Please fill in all required fields.");
        return false;
    }

    showNotification("Result retrieved successfully!");

    setTimeout(function() {
        window.location.href = "resultpage.html";
    }, 1000);

    return false;
}


/*-------------- Clear Form ----------------*/

function clearForm() {
    const form = document.querySelector("form");

    if (form) {
        form.reset();

        const inputs =
            form.querySelectorAll("input, select, textarea");

        inputs.forEach(function(input) {
            input.style.border = "1px solid #ccc";
        });

        showNotification("Form cleared successfully!");
    }
}


/*-------------- Contact Page ----------------*/

function contact() {

    const name = document.getElementById("name");
    const message = document.getElementById("message");

    const nameValue = name.value.trim();
    const messageValue = message.value.trim();

    name.style.border = "1px solid #ccc";
    message.style.border = "1px solid #ccc";

    if (nameValue === "" || messageValue === "") {

        if (nameValue === "") {
            name.style.border = "2px solid red";
        }

        if (messageValue === "") {
            message.style.border = "2px solid red";
        }

        showNotification("Please enter both name and message.");
        return false;
    }

    showNotification("Message sent successfully!");

    name.value = "";
    message.value = "";

    return false;
}
/*-------------- Notification Banner ----------------*/

function showNotification(message) {
    const notification = document.getElementById("notification");
    const notificationMessage =
        document.getElementById("notificationMessage");

    if (!notification || !notificationMessage) {
        return;
    }

    notificationMessage.textContent = message;

    notification.classList.add("show");

    setTimeout(function() {
        notification.classList.remove("show");
    }, 3000);
}

/*----------------Hamburge Menu----------------------*/
function toggleMenu() {
    const navbar = document.getElementById("navbar");
    const menuButton = document.getElementById("menuButton");

    navbar.classList.toggle("active");

    if (navbar.classList.contains("active")) {
        menuButton.textContent = "✕";
    } else {
        menuButton.textContent = "☰";
    }
}
/*---------Dark/Light Theme Toggle----------*/
// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem("theme") || "dark";
    const body = document.body;
    const themeButton = document.getElementById("themeButton");

    if (savedTheme === "light") {
        body.classList.add("light-theme");
        if (themeButton) themeButton.textContent = "☀️";
    } else {
        body.classList.remove("light-theme");
        if (themeButton) themeButton.textContent = "🌙";
    }
}

// Toggle between dark and light theme
function toggleTheme() {
    const body = document.body;
    const themeButton = document.getElementById("themeButton");

    body.classList.toggle("light-theme");

    if (body.classList.contains("light-theme")) {
        localStorage.setItem("theme", "light");
        if (themeButton) themeButton.textContent = "☀️";
    } else {
        localStorage.setItem("theme", "dark");
        if (themeButton) themeButton.textContent = "🌙";
    }
}


// Initialize theme when page loads
window.addEventListener("DOMContentLoaded", initTheme);
