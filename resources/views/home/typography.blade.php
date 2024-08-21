@extends('layouts.base')

@section('title', 'File Dashboard')

<!-- Specific Page CSS goes HERE  -->
@section('stylesheets')
<style>
  .file-card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .file-list {
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .file-list a {
    color: #007bff;
    text-decoration: none;
  }
  .file-list a:hover {
    text-decoration: underline;
  }
  .delete-button {
    color: #dc3545;
    cursor: pointer;
    font-weight: bold;
  }
  .search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }
  .search-form input {
    padding: 5px;
  }
  .search-form .warning {
    color: red;
    font-size: 12px;
    display: none;
  }
</style>
@endsection

@section('content')
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Manage Your Files</h3>
        </div>
        <div class="card-body">
          <!-- Search Form -->
          <div class="search-form">
            <input type="text" id="searchProperty" placeholder="Property (e.g., name)">
            <input type="text" id="searchValue" placeholder="Value">
            <button class="btn btn-secondary" onclick="applySearch()">Search</button>
            <button class="btn btn-secondary" onclick="resetSearch()">Reset</button>
            <span class="warning" id="warningMessage">Please fill in both fields.</span>
          </div>

          <!-- List of Files -->
          <div class="file-list" id="fileList">
            <!-- Dynamically generated file list items will go here -->
          </div>

          <!-- Add File Button -->
          <button class="btn btn-primary" onclick="document.getElementById('fileInput').click();">
            Add File
          </button>
          <input type="file" id="fileInput" style="display: none;" onchange="handleFileUpload(event)" accept=".json, .html">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const fileList = document.getElementById('fileList');
  const searchPropertyInput = document.getElementById('searchProperty');
  const searchValueInput = document.getElementById('searchValue');
  const warningMessage = document.getElementById('warningMessage');
  let files = [];

  function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
      files.push(file);
      displayFile(file);
    }
  }

  function displayFile(file) {
    const fileCard = document.createElement('div');
    fileCard.className = 'file-card';

    const fileLink = document.createElement('a');
    fileLink.href = '#';
    fileLink.textContent = file.name;
    fileLink.onclick = () => openFile(file);

    const deleteButton = document.createElement('span');
    deleteButton.className = 'delete-button';
    deleteButton.textContent = 'Delete';
    deleteButton.onclick = () => deleteFile(file, fileCard);

    fileCard.appendChild(fileLink);
    fileCard.appendChild(deleteButton);
    fileCard.dataset.name = file.name; // Storing file name as a dataset property
    fileList.appendChild(fileCard);
  }

  function openFile(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const newWindow = window.open('', '_blank');
      newWindow.document.write(e.target.result);
      newWindow.document.close();
    };
    reader.readAsText(file);
  }

  function deleteFile(file, fileCard) {
    files = files.filter(f => f !== file);
    fileList.removeChild(fileCard);
  }

  function applySearch() {
    const property = searchPropertyInput.value.trim().toLowerCase();
    const value = searchValueInput.value.trim().toLowerCase();

    if (!property || !value) {
      warningMessage.style.display = 'inline';
      return;
    }

    warningMessage.style.display = 'none';

    const fileCards = fileList.getElementsByClassName('file-card');

    for (let card of fileCards) {
      const fileProperty = card.dataset[property];
      if (fileProperty && fileProperty.toLowerCase().includes(value)) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    }
  }

  function resetSearch() {
    searchPropertyInput.value = '';
    searchValueInput.value = '';
    warningMessage.style.display = 'none';

    const fileCards = fileList.getElementsByClassName('file-card');

    for (let card of fileCards) {
      card.style.display = 'flex';
    }
  }
</script>
@endsection

<!-- Specific Page JS goes HERE  -->
@section('javascripts')
@endsection
