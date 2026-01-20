<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      href="https://unpkg.com/filepond/dist/filepond.css"
      rel="stylesheet"
    />
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <link
      href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
      rel="stylesheet"
    />
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <link rel="stylesheet" href="Styles\applicationstyle.css" />
    <script src="Scripts\script.js"></script>
    <title>NG-CRM - Заявки</title>
  </head>

  <body>
    <div class="container">
      <aside class="sidebar">
        <div class="logo-container">
          <img src="images\nglogo_light.png" alt="Logo" />
        </div>
        <nav>
          <ul>
            <li>
              <a href="employees">
                <i class="fa-solid fa-user-tie"></i>
              </a>
            </li>
            <li>
                <a href="clients">
                <i class="fa-solid fa-users"></i>
                </a>
              </li>
			  <li class="active"><i class="fa-solid fa-file"></i></li>
			 <li>
                <a href="zadachi">
                <i class="fa-solid fa-id-card"></i>
                </a>
              </li>
			 <li>
			<a href="kalend">
			<i class="fa-solid fa-calendar"></i>
				</a>
		  </li>
			 <li>
                <a href="analyt">
                <i class="fa-solid fa-line-chart"></i>
                </a>
              </li>
            
          </ul>
        </nav>
        <div class="exit" onclick="logout()">
          <i class="fa-solid fa-right-from-bracket"></i>
        </div>
        <script>
          function logout() {
            // Перенаправляем на logout.php в папке php
            window.location.href = "php/logout.php";
          }
        </script>
      </aside>
      <div class="overlay"></div>
      <div class="sidebar-toggle" id="sidebarToggle">
        <i class="fa-regular fa-square-caret-right"></i>
      </div>
      <div class="main-content">
        <header class="header">
          <!-- Первая строка -->
          <div class="header-top">
            <h1>Заявка №1 от 01.01.01</h1>
            <button id="themeToggle" class="theme-toggle">
              <i class="fas fa-moon"></i>
            </button>
            <button id="editToggle" class="edit-btn" onclick="toggleEdit()">
              <i class="fas fa-edit"></i>
            </button>
          </div>
        </header>

        <div class="info-container">
          <!-- Левый столбец -->
          <div class="info-column">
            <div class="info-item">
              <span class="info-label">Статус:</span>
              <span class="info-value" id="status">Тексттекстекст</span>
              <input type="text" class="info-edit" id="statusEdit" value="Тексттекстекст" style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Ответственный:</span>
              <span class="info-value" id="responsible">Тексттекстекст</span>
              <input type="text" class="info-edit" id="responsibleEdit" value="Тексттекстекст" style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Отдел:</span>
              <span class="info-value" id="department">Тексттекстекст</span>
              <input type="text" class="info-edit" id="departmentEdit" value="Тексттекстекст" style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Источник:</span>
              <span class="info-value" id="source">Тексттекстекст</span>
              <input type="text" class="info-edit" id="sourceEdit" value="Тексттекстекст" style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Содержание:</span>
              <span class="info-value" id="content">Тексттекстекст</span>
              <input type="text" class="info-edit" id="contentEdit" value="Тексттекстекст" style="display: none;">
            </div>
          </div>

          <!-- Градиентная полоса -->
          <div class="divider"></div>

          <!-- Правый столбец -->
          <div class="info-column">
            <div class="info-item">
              <span class="info-label">ФИО Клиента:</span>
              <span class="info-value" id="clientName">Тексттекстекст.</span>
              <input type="text" class="info-edit" id="clientNameEdit" value="Тексттекстекст." style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Организация клиента:</span>
              <span class="info-value" id="clientOrg">Тексттекстекст</span>
              <input type="text" class="info-edit" id="clientOrgEdit" value="Тексттекстекст" style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Телеграм клиента:</span>
              <span class="info-value" id="telegramName">Тексттекстекст.</span>
              <input type="text" class="info-edit" id="clientNameEdit" value="Тексттекстекст." style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Почта клиента:</span>
              <span class="info-value" id="mailName">Тексттекстекст.</span>
              <input type="text" class="info-edit" id="clientNameEdit" value="Тексттекстекст." style="display: none;">
            </div>
            <div class="info-item">
              <span class="info-label">Телефон клиента:</span>
              <span class="info-value" id="phoneName">Тексттекстекст.</span>
              <input type="text" class="info-edit" id="clientNameEdit" value="Тексттекстекст." style="display: none;">
            </div>
          </div>
        </div>
        <div class="comment-container">
          <div class="info-item">
            <span class="info-label">Комментарий:</span>
          </div>
          <div class="info-value" id="comment">Огромный длинный текст</div>
          <textarea class="info-edit" id="commentEdit" style="display: none;">Огромный длинный текст</textarea>
        </div>

        <div class="file-upload-container">
          <h3>Загрузить файлы</h3>
          <input type="file" class="filepond" multiple />
        </div>
        <div class="save-button" id="saveButton" style="display: none;">
          <button onclick="saveChanges()" style="
            background-color: #02af75;
            color: white;
            border: none;
            border-radius: 32px;
            cursor: pointer;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            position: static;
            height: 70px;
            box-shadow: 0 0 15px rgba(2, 175, 117, 0.7);
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
            opacity: 1;
            transition: opacity 0.3s ease;">
            Сохранить изменения
          </button>
        </div>
        

        <script src="https://cdn.jsdelivr.net/npm/filepond@4.24.0/dist/filepond.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.24.0/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.0/dist/filepond-plugin-file-validate-type.min.js"></script>
        
        <script>
          // Инициализация FilePond
          FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateType
          );

          const inputElement = document.querySelector('input[type="file"].filepond');
          const pond = FilePond.create(inputElement, {
            allowMultiple: true,
            acceptedFileTypes: ["image/*", "application/pdf", "text/*"],
            labelIdle: 'Нажмите или перетащите для загрузки файлов',
            server: {
              url: '/upload.php',  // Здесь указываете путь к вашему PHP-обработчику
              process: {
                url: '/',  // Отправка на тот же URL для обработки
                method: 'POST',
                withCredentials: false,  // Если не требуется отправка куки или аутентификации
                headers: {
                  'X-Custom-Header': 'FilePond'
                },
                onload: (response) => {
                  console.log('Файл успешно загружен:', response);
                },
                onerror: (error) => {
                  console.error('Ошибка загрузки файла:', error);
                }
              },
              revert: '/revert.php', // Если нужно вернуть файл
            }
          });

          let isEditMode = false;

          function toggleEdit() {
            isEditMode = !isEditMode;
            const editFields = document.querySelectorAll('.info-edit');
            const viewFields = document.querySelectorAll('.info-value');
            const saveButton = document.getElementById('saveButton');

            if (isEditMode) {
              editFields.forEach(field => field.style.display = 'inline-block');
              viewFields.forEach(field => field.style.display = 'none');
              saveButton.style.display = 'block';
            } else {
              editFields.forEach(field => field.style.display = 'none');
              viewFields.forEach(field => field.style.display = 'inline-block');
              saveButton.style.display = 'none';
            }
          }

          function saveChanges() {
            const status = document.getElementById('statusEdit').value;
            const responsible = document.getElementById('responsibleEdit').value;
            const department = document.getElementById('departmentEdit').value;
            const source = document.getElementById('sourceEdit').value;
            const content = document.getElementById('contentEdit').value;
            const clientName = document.getElementById('clientNameEdit').value;
            const clientOrg = document.getElementById('clientOrgEdit').value;
            const comment = document.getElementById('commentEdit').value;

            // Обновляем значения в режиме просмотра
            document.getElementById('status').innerText = status;
            document.getElementById('responsible').innerText = responsible;
            document.getElementById('department').innerText = department;
            document.getElementById('source').innerText = source;
            document.getElementById('content').innerText = content;
            document.getElementById('clientName').innerText = clientName;
            document.getElementById('clientOrg').innerText = clientOrg;
            document.getElementById('comment').innerText = comment;

            // Отправляем данные на сервер через AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/saveApplication.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
              if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Данные успешно сохранены');
              }
            };
            const data = `status=${encodeURIComponent(status)}&responsible=${encodeURIComponent(responsible)}&department=${encodeURIComponent(department)}&source=${encodeURIComponent(source)}&content=${encodeURIComponent(content)}&clientName=${encodeURIComponent(clientName)}&clientOrg=${encodeURIComponent(clientOrg)}&comment=${encodeURIComponent(comment)}`;
            xhr.send(data);

            toggleEdit(); // Возвращаемся в режим просмотра
          }
        </script>
      </div>
    </div>
  </body>
</html>