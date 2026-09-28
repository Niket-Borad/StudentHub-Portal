/*-------------- Login Page ----------------*/

function loginsuccess() {
    const username = document.getElementById("username");
    const password = document.getElementById("password");

    if (!username || !password) {
        return false;
    }

    const usernameValue = username.value.trim();
    const passwordValue = password.value.trim();

    username.style.border = "1px solid #ccc";
    password.style.border = "1px solid #ccc";

    const nameRegex = /^[A-Za-z ]+$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/;

    if (usernameValue === "" || !nameRegex.test(usernameValue) || passwordValue === "" || !passwordRegex.test(passwordValue)) {
        if (usernameValue === "" || !nameRegex.test(usernameValue)) {
            username.style.border = "2px solid red";
        }

        if (passwordValue === "" || !passwordRegex.test(passwordValue)) {
            password.style.border = "2px solid red";
        }

        showNotification("Please enter both username and password.");
        return false;
    }

    showNotification("Successfully Logged In!");

    setTimeout(function() {
        window.location.href = "dashboard.html";
    }, 1000);

    return false;
}


/*-------------- Registration Page ----------------*/

function register(event) {
    if (event) {
        event.preventDefault();
    }

    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const mobile = document.getElementById("mobile");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const course = document.getElementById("course");
    const year = document.getElementById("year");
    const genderSelected = document.querySelector('input[name="gender"]:checked');
    const terms = document.getElementById("terms");

    const formFields = [name, email, mobile, password, confirmPassword, course, year];

    formFields.forEach(function(field) {
        if (field) {
            field.style.border = "1px solid #ccc";
        }
    });

    const nameRegex = /^[A-Za-z][A-Za-z\s.'-]{1,49}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const mobileRegex = /^(?:\+91|91)?[6-9]\d{9}$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

    let isValid = true;

    if (!name || !nameRegex.test(name.value.trim())) {
        if (name) name.style.border = "2px solid red";
        isValid = false;
    }

    if (!email || !emailRegex.test(email.value.trim())) {
        if (email) email.style.border = "2px solid red";
        isValid = false;
    }

    if (!mobile || !mobileRegex.test(mobile.value.trim())) {
        if (mobile) mobile.style.border = "2px solid red";
        isValid = false;
    }

    if (!password || !passwordRegex.test(password.value)) {
        if (password) password.style.border = "2px solid red";
        isValid = false;
    }

    if (!confirmPassword || !confirmPassword.value.trim() || confirmPassword.value !== password.value) {
        if (confirmPassword) confirmPassword.style.border = "2px solid red";
        isValid = false;
    }

    if (!course || course.value === "" || course.value === "Select Course") {
        if (course) course.style.border = "2px solid red";
        isValid = false;
    }

    if (!year || year.value === "" || year.value === "Select Year") {
        if (year) year.style.border = "2px solid red";
        isValid = false;
    }

    if (!genderSelected) {
        isValid = false;
    }

    if (!terms || !terms.checked) {
        isValid = false;
    }

    if (!isValid) {
        showNotification("Please correct the highlighted fields and accept the terms.");
        return false;
    }

    showNotification("Successfully registered!");

    const form = document.querySelector("form");
    if (form) {
        form.reset();
    }

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

    if (nameValue === " /^[A-Za-z][A-Za-z\s.'-]{1,49}$/;" || messageValue === "") {

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

    if (!navbar || !menuButton) {
        return;
    }

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
window.addEventListener("DOMContentLoaded", function() {
    initTheme();

    if (document.getElementById("dataList")) {
        initDataPortal();
    }
});

/*-------------- Data Portal ----------------*/

let allData = {};
let currentData = [];
let currentPage = 1;
const itemsPerPage = 3;

/* Load JSON data using Fetch API */
function initDataPortal() {
    fetch("../data/data.json")
        .then(function(response) {
            if (!response.ok) {
                throw new Error("Unable to load JSON file");
            }
            return response.json();
        })
        .then(function(data) {
            allData = data || {};
            loadSelectedData();
        })
        .catch(function(error) {
            console.log(error);
            const dataList = document.getElementById("dataList");
            if (dataList) {
                dataList.innerHTML = "<p>Unable to load data.</p>";
            }
        });
}

/* Select Events, Students, Notices or FAQs */
function loadSelectedData() {
    const dataType = document.getElementById("dataType");
    if (!dataType || !allData) {
        return;
    }

    currentPage = 1;
    currentData = Array.isArray(allData[dataType.value]) ? allData[dataType.value] : [];
    displayData();
}

/* Display data */
function displayData() {
const dataList =
    document.getElementById("dataList");
if (!dataList) {
    return;
}

const searchBox =document.getElementById("searchBox");
const searchText =searchBox.value.toLowerCase().trim();

/* Search */
let filteredData =
    currentData.filter(function(item) {
        return JSON.stringify(item)
            .toLowerCase()
            .includes(searchText); });

/* Sorting */
const sortOrder = document.getElementById("sortOrder");

if (sortOrder && sortOrder.value === "asc") {
    filteredData.sort(function(a, b) {
        return getTitle(a)
            .localeCompare(getTitle(b));
    });
} else if (sortOrder) {
    filteredData.sort(function(a, b) {
        return getTitle(b)
            .localeCompare(getTitle(a));
    });
}
/* Save filtered data */
window.filteredPortalData = filteredData;

/* No data */
if (filteredData.length === 0) {
    dataList.innerHTML = "<p>No data found.</p>";
    document.getElementById("pagination").innerHTML = "";
   return;
}
/* Pagination */

const start = (currentPage - 1) * itemsPerPage;
const end =start + itemsPerPage;
const pageData = filteredData.slice(start, end);
dataList.innerHTML = "";
/* Create cards */

pageData.forEach(function(item) {
    const card = document.createElement("div");
    card.className = "data-card";

    /* Events */
    if (item.title && item.date && item.location) {
        card.innerHTML = `
            <h3>${item.title}</h3>
            <p><strong>Category:</strong> ${item.category || "General"}</p>
            <p><strong>Date:</strong> ${item.date}</p>
            <p><strong>Location:</strong> ${item.location}</p>
            <p><strong>Description:</strong> ${item.description || "No description available."}</p>
        `;
    }

    /* Students */
    else if (item.name && item.course) {
        card.innerHTML = `
            <h3>${item.name}</h3>
            <p><strong>Student ID:</strong> ${item.id || "N/A"}</p>
            <p><strong>Course:</strong> ${item.course}</p>
            <p><strong>Year:</strong> ${item.year}</p>
            <p><strong>Batch:</strong> ${item.batch || "N/A"}</p>
            <p><strong>Email:</strong> ${item.email || "N/A"}</p>
            <p><strong>Status:</strong> ${item.status || "N/A"}</p>
        `;
    }

    /* Notices */
    else if (item.title && item.description) {
        card.innerHTML = `
            <h3>${item.title}</h3>
            <p><strong>Category:</strong> ${item.category || "General"}</p>
            <p><strong>Priority:</strong> ${item.priority || "Normal"}</p>
            <p><strong>Date:</strong> ${item.date}</p>
            <p><strong>Description:</strong> ${item.description}</p>
        `;
    }

    /* FAQs */
    else if (item.question && item.answer) {
        card.innerHTML = `
            <h3>${item.question}</h3>
            <p><strong>Category:</strong> ${item.category || "General"}</p>
            <p><strong>Answer:</strong> ${item.answer}</p>
        `;
    }

    dataList.appendChild(card);
});
createPagination(filteredData.length);
}

/* Get title for sorting */
function getTitle(item) {
if (item.title) {
    return item.title;
}
if (item.name) {
    return item.name;
}
if (item.question) {
    return item.question;
}
return "";
}

/* Search data */
function filterData() {
currentPage = 1;
displayData();
}

/* Sort data */
function sortData() {
currentPage = 1;
displayData();
}

/* Create pagination buttons */
function createPagination(totalItems) {
const pagination =document.getElementById("pagination");
if (!pagination) {
    return;
}
const totalPages = Math.ceil(totalItems / itemsPerPage);
pagination.innerHTML = "";
for (let i = 1; i <= totalPages; i++) {
    const button =  document.createElement("button");
    button.textContent = i;

    if (i === currentPage) {
        button.classList.add("active-page");
    }

    button.onclick = function() {
        currentPage = i;
        displayData();

    };
    pagination.appendChild(button);
}
}