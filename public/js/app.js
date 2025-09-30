const API_ROOT = '/api'; 

function apiPost(path, data, token) {
  return $.ajax({
    url: API_ROOT + '/' + path,
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify(data || {}),
    headers: token ? { 'Authorization': 'Bearer ' + token } : {}
  });
}

function apiGet(path, token) {
  return $.ajax({
    url: API_ROOT + '/' + path,
    method: 'GET',
    headers: token ? { 'Authorization': 'Bearer ' + token } : {}
  });
}

// Register handler: binds to form submit
function handleRegister(formSelector, onSuccess) {
  $(formSelector).on('submit', function(e) {
    e.preventDefault();
    const $form = $(this);
    const email = $form.find('[name=email]').val().trim();
    const password = $form.find('[name=password]').val();
    const full_name = $form.find('[name=full_name]').val().trim();

    if (!email || !password) { alert('Email and password required'); return; }

    apiPost('register.php', { email, password, full_name })
      .done(function(resp) {
        if (resp.success) onSuccess && onSuccess(resp);
        else alert(resp.error || 'Registration failed');
      })
      .fail(function(xhr) {
        alert(xhr.responseJSON?.error || 'Registration error');
      });
  });
}

// Login handler: binds to form submit
function handleLogin(formSelector, onSuccess) {
  $(formSelector).on('submit', function(e) {
    e.preventDefault();
    const $form = $(this);
    const email = $form.find('[name=email]').val().trim();
    const password = $form.find('[name=password]').val();

    if (!email || !password) { alert('Email and password required'); return; }

    apiPost('login.php', { email, password })
      .done(function(resp) {
        if (resp.success && resp.token) {
          localStorage.setItem('session_token', resp.token);
          onSuccess && onSuccess(resp);
        } else {
          alert(resp.error || 'Login failed');
        }
      })
      .fail(function(xhr) {
        alert(xhr.responseJSON?.error || 'Login error');
      });
  });
}

// Load profile, expects Authorization: Bearer <token>
function loadProfile(renderFn) {
  const token = localStorage.getItem('session_token');
  if (!token) { window.location.href = 'index.html'; return; }

  $.ajax({
    url: API_ROOT + '/get_profile.php',
    method: 'GET',
    headers: { 'Authorization': 'Bearer ' + token }
  })
  .done(function(resp) {
    if (resp.success) {
      renderFn && renderFn(resp.profile);
    } else {
      alert(resp.error || 'Failed to load profile');
      localStorage.removeItem('session_token');
      window.location.href = 'index.html';
    }
  })
  .fail(function() {
    localStorage.removeItem('session_token');
    window.location.href = 'index.html';
  });
}

// Submit profile update (binds to update form)
/**
 * Attaches a submit handler to a form for updating user profile information via AJAX.
 *
 * @param {string} formSelector - jQuery selector for the form to attach the submit handler to.
 * @param {function(Object):void} [onSuccess] - Optional callback to execute if the profile update is successful. Receives the response object as an argument.
 *
 * @example
 * submitProfileUpdate('#profileForm', function(response) {
 *   alert('Profile updated!');
 * });
 */
function submitProfileUpdate(formSelector, onSuccess) {
  $(formSelector).on('submit', function(e) {
    e.preventDefault();
    const token = localStorage.getItem('session_token');
    if (!token) { window.location.href = 'index.html'; return; }

    const data = {};
    $(this).serializeArray().forEach(item => { data[item.name] = item.value; });

    $.ajax({
      url: API_ROOT + '/update_profile.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(data),
      headers: { 'Authorization': 'Bearer ' + token }
    })
    .done(function(resp){
      if (resp.success) onSuccess && onSuccess(resp);
      else alert(resp.error || 'Update failed');
    })
    .fail(function(xhr) {
      alert(xhr.responseJSON?.error || 'Update error');
    });
  });
}

// Logout: call backend to delete token in redis, then clear localStorage and redirect
function logout() {
  const token = localStorage.getItem('session_token');
  if (!token) { localStorage.removeItem('session_token'); window.location.href = 'index.html'; return; }

  $.ajax({
    url: API_ROOT + '/logout.php',
    method: 'POST',
    headers: { 'Authorization': 'Bearer ' + token }
  }).always(function(){
    localStorage.removeItem('session_token');
    window.location.href = 'index.html';
  });
}