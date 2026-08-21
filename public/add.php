<?php

require_once __DIR__ . '/../src/bootstrap.php';

use CT275\Labs\Contact;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $avatar = '';

  if (
    isset($_FILES['avatar']) &&
    $_FILES['avatar']['error'] === UPLOAD_ERR_OK
  ) {
    $filename = uniqid('avatar_') . '_' . basename($_FILES['avatar']['name']);

    $uploadPath = __DIR__ . '/uploads/' . $filename;

    move_uploaded_file(
      $_FILES['avatar']['tmp_name'],
      $uploadPath
    );

    $avatar = '/uploads/' . $filename;
  }

  $contactData = [
    'name' => $_POST['name'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'notes' => $_POST['notes'] ?? '',
    'avatar' => $avatar,
  ];

  $contact = new Contact($PDO);

  $errors = $contact->validate($contactData);

  if (empty($errors)) {
    $contact->fill($contactData);
    $contact->save() && redirect('/');
  }
}

include_once __DIR__ . '/../src/partials/header.php';
?>

<body>
  <?php include_once __DIR__ . '/../src/partials/navbar.php' ?>

  <!-- Main Page Content -->
  <div class="container">

    <?php
    $subtitle = 'Add your contacts here.';
    include_once __DIR__ . '/../src/partials/heading.php';
    ?>

    <div class="row">
      <div class="col-12">

        <form method="post" class="col-md-6 offset-md-3" enctype="multipart/form-data">

          <!-- Name -->
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text"
              name="name"
              class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
              maxlength="255"
              id="name"
              placeholder="Enter Name"
              value="<?= isset($_POST['name']) ? html_escape($_POST['name']) : '' ?>" />

            <?php if (isset($errors['name'])) : ?>
              <span class="invalid-feedback">
                <strong><?= $errors['name'] ?></strong>
              </span>
            <?php endif ?>
          </div>

          <!-- Phone -->
          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text"
              name="phone"
              class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>"
              maxlength="255"
              id="phone"
              placeholder="Enter Phone"
              value="<?= isset($_POST['phone']) ? html_escape($_POST['phone']) : '' ?>" />

            <?php if (isset($errors['phone'])) : ?>
              <span class="invalid-feedback">
                <strong><?= $errors['phone'] ?></strong>
              </span>
            <?php endif ?>
          </div>

          <!-- Notes -->
          <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes"
              id="notes"
              class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>"
              placeholder="Enter notes (maximum character limit: 255)"><?= isset($_POST['notes']) ? html_escape($_POST['notes']) : '' ?></textarea>

            <?php if (isset($errors['notes'])) : ?>
              <span class="invalid-feedback">
                <strong><?= $errors['notes'] ?></strong>
              </span>
            <?php endif ?>
          </div>
          <div class="mb-3">
            <label for="avatar" class="form-label">Avatar</label>
            <input type="file"
              name="avatar"
              id="avatar"
              class="form-control"
              accept="image/*">
            <div class="mb-3">
              <label class="form-label">Avatar</label>

              <input
                type="file"
                name="avatar"
                id="avatar"
                class="form-control"
                accept="image/*">

              <img
                id="preview"
                src=""
                style="max-width:150px;display:none;margin-top:10px;">
            </div>
          </div>
          <!-- Submit -->
          <button type="submit" name="submit" class="btn btn-primary">
            Add Contact
          </button>

        </form>

      </div>
    </div>

  </div>

  <?php include_once __DIR__ . '/../src/partials/footer.php' ?>
</body>
<script>
  document.getElementById('avatar')
    .addEventListener('change', function() {

      const file = this.files[0];

      if (!file) return;

      const preview = document.getElementById('preview');

      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    });
</script>

</html>