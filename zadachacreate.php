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
    <title>NG-CRM - Новая заявка</title>
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
          <div class="header-top">
            <h1>Новая задача</h1>
            <button id="themeToggle" class="theme-toggle">
              <i class="fas fa-moon"></i>
            </button>
          </div>
        </header>

        <div class="info-container">
          <!-- Левый столбец -->
          <div class="info-column">
            <div class="info-item">
              <span class="info-label">Название:</span>
              <input type="text" class="info-edit" id="statusEdit" value="">
            </div>
            <div class="info-item">
              <span class="info-label">Инициатор:</span>
              <input type="text" class="info-edit" id="responsibleEdit" value="">
            </div>
            <div class="info-item">
              <span class="info-label">Исполнитель:</span>
              <input type="text" class="info-edit" id="departmentEdit" value="">
            </div>
            <div class="info-item">
              <span class="info-label">Дата назначения:</span>
              <input type="text" class="info-edit" id="departmentEdit" value="">
            </div>
            <div class="info-item">
              <span class="info-label">Дата дедлайна:</span>
              <input type="text" class="info-edit" id="departmentEdit" value="">
            </div>
          </div>



          <!-- Правый столбец -->
          <div class="info-column">
            <div class="info-item">
              <span class="info-label"></span>
            </div>
            <div class="info-item">
              <span class="info-label"></span>
            </div>
          </div>
        </div>
        <div class="comment-container">
          <div class="info-item">
            <span class="info-label">Комментарий:</span>
          </div>
          <textarea class="info-edit" id="commentEdit"></textarea>
        </div>

        <div class="file-upload-container">
          <h3>Загрузить файлы</h3>
          <input type="file" class="filepond" multiple />
        </div>
        <div class="save-button">
          <button onclick="createTask()" style="
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
            Создать задачу
          </button>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/filepond@4.24.0/dist/filepond.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.24.0/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.0/dist/filepond-plugin-file-validate-type.min.js"></script>
        
        <script>
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
              url: '/upload.php',
              process: {
                url: '/',
                method: 'POST',
                withCredentials: false,
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
              revert: '/revert.php',
            }
          });

          function createTask() {
            const status = document.getElementById('statusEdit').value;
            const responsible = document.getElementById('responsibleEdit').value;
            const department = document.getElementById('departmentEdit').value;
            const source = document.getElementById('sourceEdit').value;
            const content = document.getElementById('contentEdit').value;
            const clientName = document.getElementById('clientNameEdit').value;
            const clientOrg = document.getElementById('clientOrgEdit').value;
            const clientPhone = document.getElementById('clientPhoneEdit').value;
            const clientEmail = document.getElementById('clientEmailEdit').value;
            const clientAddress = document.getElementById('clientAddressEdit').value;
            const comment = document.getElementById('commentEdit').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/createTask.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
              if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Задача успешно создана');
                window.location.href = "zadachi";
              }
            };
            const data = `status=${encodeURIComponent(status)}&responsible=${encodeURIComponent(responsible)}&department=${encodeURIComponent(department)}&source=${encodeURIComponent(source)}&content=${encodeURIComponent(content)}&clientName=${encodeURIComponent(clientName)}&clientOrg=${encodeURIComponent(clientOrg)}&clientPhone=${encodeURIComponent(clientPhone)}&clientEmail=${encodeURIComponent(clientEmail)}&clientAddress=${encodeURIComponent(clientAddress)}&comment=${encodeURIComponent(comment)}`;
            xhr.send(data);
          }
        </script>
      </div>
    </div>
  </body>
</html>