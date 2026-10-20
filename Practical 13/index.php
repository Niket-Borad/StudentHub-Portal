<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 850px; margin: 30px auto; padding: 0 16px; color: #222; }
        h1 { color: #174ea6; }
        form { display: grid; gap: 10px; margin-bottom: 24px; }
        input, button { padding: 10px; font-size: 16px; }
        button { cursor: pointer; }
        .actions { display: flex; gap: 8px; }
        #message { min-height: 20px; color: #b00020; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #edf2fa; }
    </style>
</head>
<body>
    <h1>Student Management</h1>

    <form id="studentForm">
        <input type="text" id="name" placeholder="Student name" maxlength="100" required>
        <input type="email" id="email" placeholder="Email address" maxlength="100" required>
        <input type="text" id="mobile" placeholder="Mobile number" maxlength="15" required>
        <select id="gender">
            <option value="">Gender (optional)</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
            <option value="Other">Other</option>
        </select>
        <input type="text" id="course" placeholder="Course" maxlength="50" required>
        <input type="number" id="year" placeholder="Year of study" min="1" required>
        <div class="actions">
            <button type="submit" id="saveButton">Add Student</button>
            <button type="button" id="cancelButton" hidden>Cancel Edit</button>
        </div>
    </form>

    <p id="message" role="status"></p>
    <table>
        <thead>
            <tr><th>Name</th><th>Email</th><th>Mobile</th><th>Gender</th><th>Course</th><th>Year</th><th>Actions</th></tr>
        </thead>
        <tbody id="studentRows"></tbody>
    </table>

    <script>
        const apiUrl = "api.php";
        const form = document.getElementById("studentForm");
        const message = document.getElementById("message");
        const rows = document.getElementById("studentRows");
        const saveButton = document.getElementById("saveButton");
        const cancelButton = document.getElementById("cancelButton");
        let editingId = null;
        let students = [];

        async function request(method, data = null) {
            const options = { method, headers: {} };
            if (data !== null) {
                options.headers["Content-Type"] = "application/json";
                options.body = JSON.stringify(data);
            }

            const response = await fetch(apiUrl, options);
            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || "The request failed.");
            }
            return result;
        }

        async function loadStudents() {
            try {
                const result = await request("GET");
                students = result.students;
                rows.replaceChildren();

                students.forEach(student => {
                    const row = document.createElement("tr");
                    [student.name, student.email, student.mobile, student.gender || "-", student.course, student.year].forEach(value => {
                        const cell = document.createElement("td");
                        cell.textContent = value;
                        row.appendChild(cell);
                    });

                    const actions = document.createElement("td");
                    const editButton = document.createElement("button");
                    editButton.type = "button";
                    editButton.textContent = "Edit";
                    editButton.addEventListener("click", () => editStudent(student.id));

                    const deleteButton = document.createElement("button");
                    deleteButton.type = "button";
                    deleteButton.textContent = "Delete";
                    deleteButton.addEventListener("click", () => deleteStudent(student.id));

                    actions.append(editButton, deleteButton);
                    row.appendChild(actions);
                    rows.appendChild(row);
                });
            } catch (error) {
                message.textContent = error.message;
            }
        }

        function editStudent(id) {
            const student = students.find(item => Number(item.id) === Number(id));
            if (!student) return;

            document.getElementById("name").value = student.name;
            document.getElementById("email").value = student.email;
            document.getElementById("mobile").value = student.mobile;
            document.getElementById("gender").value = student.gender || "";
            document.getElementById("course").value = student.course;
            document.getElementById("year").value = student.year;
            editingId = student.id;
            saveButton.textContent = "Update Student";
            cancelButton.hidden = false;
            message.textContent = "";
        }

        function resetForm() {
            form.reset();
            editingId = null;
            saveButton.textContent = "Add Student";
            cancelButton.hidden = true;
        }

        form.addEventListener("submit", async event => {
            event.preventDefault();
            const student = {
                name: document.getElementById("name").value.trim(),
                email: document.getElementById("email").value.trim(),
                mobile: document.getElementById("mobile").value.trim(),
                gender: document.getElementById("gender").value,
                course: document.getElementById("course").value.trim(),
                year: document.getElementById("year").value
            };

            if (!student.name || !student.email || !student.mobile || !student.course || !student.year) {
                message.textContent = "Please fill in all fields.";
                return;
            }

            try {
                const method = editingId === null ? "POST" : "PUT";
                const data = editingId === null ? student : { id: editingId, ...student };
                const result = await request(method, data);
                message.textContent = result.message;
                resetForm();
                await loadStudents();
            } catch (error) {
                message.textContent = error.message;
            }
        });

        async function deleteStudent(id) {
            if (!confirm("Delete this student?")) return;

            try {
                const result = await request("DELETE", { id });
                message.textContent = result.message;
                if (Number(editingId) === Number(id)) resetForm();
                await loadStudents();
            } catch (error) {
                message.textContent = error.message;
            }
        }

        cancelButton.addEventListener("click", resetForm);
        loadStudents();
    </script>
</body>
</html>
