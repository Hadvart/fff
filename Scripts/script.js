document.addEventListener("DOMContentLoaded", () => {
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");
  const themeToggle = document.getElementById("themeToggle");
  const body = document.body;

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", () => {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      if (type === "password") {
        togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
      } else {
        togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const themeToggle = document.getElementById("themeToggle");
  const body = document.body;
  const logo = document.getElementById("logo");

  // Проверяем сохранённую тему сразу, ещё до взаимодействия
const savedTheme = localStorage.getItem("theme");
if (savedTheme === "dark") {
    body.classList.add("dark-theme");
    if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
} else {
    body.classList.remove("dark-theme");
    if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
}

// Обработчик переключения темы
if (themeToggle) {
    themeToggle.addEventListener("click", () => {
        const isDarkTheme = body.classList.toggle("dark-theme");
        if (themeToggle) themeToggle.innerHTML = isDarkTheme
            ? '<i class="fas fa-sun"></i>'
            : '<i class="fas fa-moon"></i>';

        // Записываем в localStorage
        localStorage.setItem("theme", isDarkTheme ? "dark" : "light");
    });
}
});
 /*
document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("toggleBtn").addEventListener("click", function () {
      let formContainer = document.getElementById("formContainer");
      let form = document.getElementById("employeeForm");

      if (form.style.display === "none") {
          form.style.display = "flex";
          
          if (window.innerWidth <= 768) {
              formContainer.style.width = "100%";
              formContainer.style.height = "900px"; // Устанавливаем 600px на мобильных
          } else {
              formContainer.style.width = "45%";
              formContainer.style.height = "350px"; // Оставляем 350px для десктопа
          }

          form.classList.remove("hidden");
      } else {
          form.style.display = "none";
          formContainer.classList.add("collapsed");
          formContainer.classList.remove("expanded");
          formContainer.style.height = "50px";
          formContainer.style.width = "150px";
          form.classList.add("hidden");
      }
  });
});
*/


document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("dataTable");
    const rows = table.getElementsByTagName("tr");

    // Функция для выполнения поиска
    function searchTable() {
      const searchTerm = searchInput.value.toLowerCase();

      // Перебираем все строки таблицы (пропускаем заголовок)
      for (let i = 1; i < rows.length; i++) {
        let row = rows[i];
        let cells = row.getElementsByTagName("td");
        let rowText = "";

        // Собираем текст из всех ячеек строки
        for (let j = 0; j < cells.length; j++) {
          rowText += cells[j].textContent.toLowerCase();
        }

        // Если строка содержит искомое слово, показываем её, иначе скрываем
        if (rowText.includes(searchTerm)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      }
    }

    // Обработчик события input на поле поиска
    searchInput.addEventListener("input", searchTable);
  });

  document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");
    const submitBtn = document.getElementById("submit-btn");
    const errorMessage = document.getElementById("error-message");

    if (!password || !confirmPassword || !submitBtn || !errorMessage) {
        console.error("Один из элементов не найден! Проверьте ID в HTML.");
        return;
    }

    function validatePasswords() {
        if (password.value === confirmPassword.value && password.value !== "") {
            submitBtn.removeAttribute("disabled"); // Активируем кнопку
            submitBtn.style.backgroundColor = "#02af75";
            errorMessage.style.display = "none";
        } else {
            submitBtn.setAttribute("disabled", "true"); // Делаем кнопку неактивной
            submitBtn.style.backgroundColor = "#ccc"; // Серый цвет
            errorMessage.style.display = "block";
        }
    }

    password.addEventListener("input", validatePasswords);
    confirmPassword.addEventListener("input", validatePasswords);
});



document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector(".sidebar");
  const sidebarToggle = document.getElementById("sidebarToggle");
  const overlay = document.querySelector('.overlay');

  // Обработчик клика по кнопке
  sidebarToggle.addEventListener("click", function () {
    sidebar.classList.toggle("active"); // Добавляем/убираем класс active
    if (sidebar.classList.contains("active")) {
      overlay.style.display = 'block';  // Показываем затемняющий слой
    } else {
      overlay.style.display = 'none';   // Скрываем затемняющий слой
    }
  });

  // Закрытие боковой панели при клике на оверлей
  overlay.addEventListener("click", function () {
    sidebar.classList.remove("active");
    overlay.style.display = 'none'; // Скрываем затемняющий слой
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const table = document.getElementById("dataTable");
  const headers = table.querySelectorAll("th");
  let sortOrder = {}; // Хранит текущий порядок сортировки для каждого столбца

  headers.forEach((header, columnIndex) => {
    if (header.innerText.trim()) { // Пропускаем пустой <th>
      header.style.cursor = "pointer"; // Делаем заголовок кликабельным
      header.addEventListener("click", function () {
        sortTable(columnIndex);
      });
    }
  });

  function sortTable(columnIndex) {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const isNumeric = !isNaN(rows[0]?.cells[columnIndex]?.innerText.trim());

    // Определяем порядок сортировки
    sortOrder[columnIndex] = !sortOrder[columnIndex];

    rows.sort((rowA, rowB) => {
      let cellA = rowA.cells[columnIndex].innerText.trim();
      let cellB = rowB.cells[columnIndex].innerText.trim();

      if (isNumeric) {
        cellA = parseFloat(cellA) || 0;
        cellB = parseFloat(cellB) || 0;
      }

      return sortOrder[columnIndex] ? (cellA > cellB ? 1 : -1) : (cellA < cellB ? 1 : -1);
    });

    // Перерисовываем отсортированные строки
    tbody.innerHTML = "";
    rows.forEach(row => tbody.appendChild(row));
  }
});