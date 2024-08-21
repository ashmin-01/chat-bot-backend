@extends('layouts.base')

@section('title', 'Prompt Template Editor')

@section('stylesheets')
<style>
  :root {
  --primary: #c30010;
  --secondary: #f4f5f7;
  --danger: #fd5d93;
  --light: #adb5bd;
  --dark: #212529;
  --white: #ffffff;
  --background-dark: #1e1e2f;
  --text-light: #ffffff;
  --card-border-dark: #4d4d4d;
  --card-border-light: #e0e0e0;
  --font-family-sans-serif: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
}

/* Light Mode Styles */
.light-mode {
    --background-color: #ffffff;
    --text-color: #000000;
    --textarea-bg-color: #f8f9fa;
    --textarea-border-color: #ced4da;
    --button-bg-color: #007bff;
    --button-text-color: #ffffff;
    --edit-btn-bg-color: transparent;
    --edit-btn-text-color: #ffffff;
    --save-btn-bg-color: #dc3545;
}

/* Dark Mode Styles */
.dark-mode {
    --background-color: #1e1e2f;
    --text-color: #ffffff;
    --textarea-bg-color: #1a2633;
    --textarea-border-color: #ccc;
    --button-bg-color: #007bff;
    --button-text-color: #ffffff;
    --edit-btn-bg-color: transparent;
    --edit-btn-text-color: #ffffff;
    --save-btn-bg-color: #dc3545;
}

body {
    background-color: var(--background-color);
    color: var(--text-color);
    font-size: 18px;
}

.textarea-content {
    width: 100%;
    height: 300px;
    border: 1px solid var(--textarea-border-color);
    border-radius: 4px;
    padding: 10px;
    font-family: inherit;
    font-size: 18px;
    resize: none;
    background-color: var(--textarea-bg-color);
    color: var(--text-color);
}
.textarea-content[readonly] {
    cursor: not-allowed;
}

.edit-save-buttons {
    margin-top: 10px;
}
.edit-save-buttons button {
    margin-right: 5px;
    font-weight: bold;
    color: var(--button-text-color);
}
#edit-btn {
    background-color: var(--edit-btn-bg-color);
    color: var(--edit-btn-text-color);
    border: 1px solid var(--edit-btn-text-color);
}
#edit-btn.btn-fill {
    background-color: var(--button-bg-color);
    color: var(--button-text-color);
}
#save-btn {
    background-color: var(--save-btn-bg-color);
    color: var(--button-text-color);
}
</style>
@endsection

@section('content')
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Prompt Template</h5>
        </div>
        <div class="card-body">
          <div class="textarea-container">
            <textarea id="prompt-template" class="textarea-content" readonly>
تصرف كأنك موظف خدمة عملاء.
أجب باللغة العربية فقط.
قم بفهم السياقات التالية ثم بقم بالاجابة على الأسئلة.
إذا كنت لا تعرف الإجابة، فقط قل إنك لا تعرف، لا تحاول تصنيع إجابة.
حافظ على إجابتك شاملة وصحيحة قدر الإمكان.
إضافة مقدمة بسيطة متناسبة مع الجواب الذي ستقوم بتقديمه.
في حال السؤال عن باقات يرجى ذكر جميع الباقات المناسبة للسياق إلا إذا تم ذكر عدد معين من الباقات في السؤال فتكتفي بالعدد المطلوب أو أقل في حال كان عدد الباقات أقل.
اقتراح أسئلة من المعلومات في حال كان السؤال قريب من المعلومات لكنه خاطئ.
كن لبقا في إجاباتك.
\n السياق: {context}
\n السؤال: {question}
\n الإجابة المفيدة:
            </textarea>
          </div>
        </div>
        <div class="card-footer">
          <button id="edit-btn" class="btn btn-outline-primary">Edit</button>
          <button id="save-btn" class="btn btn-danger" disabled>Save</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascripts')
<script>
  // Function to toggle between light and dark mode
  function toggleMode() {
    const body = document.body;
    if (body.classList.contains('light-mode')) {
      body.classList.remove('light-mode');
      body.classList.add('dark-mode');
    } else {
      body.classList.remove('dark-mode');
      body.classList.add('light-mode');
    }
  }

  // Event listeners for buttons
  document.getElementById('edit-btn').addEventListener('click', function() {
    const textarea = document.getElementById('prompt-template');
    textarea.removeAttribute('readonly');
    textarea.focus();
    document.getElementById('edit-btn').disabled = true;
    document.getElementById('save-btn').disabled = false;
    document.getElementById('edit-btn').classList.add('btn-fill');
  });

  document.getElementById('save-btn').addEventListener('click', function() {
    const textarea = document.getElementById('prompt-template');
    textarea.setAttribute('readonly', true);
    document.getElementById('edit-btn').disabled = false;
    document.getElementById('save-btn').disabled = true;
    document.getElementById('edit-btn').classList.remove('btn-fill');

    // Example: Send the updatedContent to the server using an AJAX call
    const updatedContent = textarea.value;
    console.log('Saving content:', updatedContent);
  });
</script>
@endsection
