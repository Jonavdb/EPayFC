<?php require_once "utils/GoBack.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <base href="<?= $web_root ?>">
    <meta name="author" content="HABOULA_Ali">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <main class="container" style="padding-top: 80px; padding-bottom: 100px;">
        <!-- HEADER -->
        <?php
        $page_title = "Edit Profile";
        include 'view/view_header.php';
        ?>

        <?php if (count($errors) != 0): ?>
            <div class="errors mb-4">
                <p>Please correct the following error(s) :</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="user/edit_profile" method="post">
            <div class="card mb-4 bg-dark border-secondary">
                <div class="card-header border-secondary text-white fw-semibold bg-secondary bg-opacity-10">
                    Personal Information
                </div>

                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="full_name" class="form-label text-white fw-bold">Full Name *</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="full_name" name="full_name"
                            value="<?= htmlspecialchars($user->get_full_name()) ?>" required>
                        <div class="form-text text-secondary">Your complete name as it should appear on your profile</div>
                    </div>

                    <div class="mb-2">
                        <label for="pseudo" class="form-label text-white fw-bold">Username *</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="pseudo" name="pseudo"
                            value="<?= htmlspecialchars($user->get_pseudo()) ?>" required>
                        <div class="form-text text-secondary">This will be your public display name on the platform</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 bg-dark border-secondary">
                <div class="card-header border-secondary text-white fw-semibold bg-secondary bg-opacity-10">
                    Contact & Payment
                </div>

                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="email" class="form-label text-white fw-bold">Email Address *</label>
                        <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email"
                            value="<?= htmlspecialchars($user->get_email()) ?>" required>
                        <div class="form-text text-secondary">We'll use this email to send you notifications about your sales and purchases</div>
                    </div>

                    <div class="mb-2">
                        <label for="iban" class="form-label text-white fw-bold">IBAN <span class="text-secondary fw-normal">(Optional)</span></label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="iban" name="iban"
                            value="<?= isset($user) ? htmlspecialchars($user->get_iban() ?? '') : '' ?>"
                            placeholder="BE99 9999 9999 9999">
                        <div class="form-text text-secondary">For receiving payments from your sales. Leave empty if you don't want to receive payments directly.</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                </button>
            </div>

        </form>

    </main>

    <!-- FOOTER -->
    <footer class="fixed-bottom">
        <?php include 'view/view_footer.php'; ?>
    </footer>
</body>

</html>