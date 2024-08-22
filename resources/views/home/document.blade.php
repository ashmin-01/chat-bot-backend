@extends('layouts.base')

@section('title', 'File Dashboard')

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
          <!-- File Upload Form -->
          <form action="{{ route('document.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="active">Active:</label>
              <input type="checkbox" id="active" name="active" value="1">
            </div>
            <div class="form-group">
              <label for="date">Date:</label>
              <input type="date" id="date" name="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="file">File:</label>
              <input type="file" id="file" name="file" class="form-control" accept=".html" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Document</button>
          </form>

          <!-- List of Files -->
          <div class="file-list" id="fileList">
            <!-- Dynamically generated file list items will go here -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const fileList = document.getElementById('fileList');
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
</script>
@endsection

@section('javascripts')
@endsection
