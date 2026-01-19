<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <main class="container" style="padding-top: 80px; padding-bottom: 100px;">
        <!-- HEADER -->
        <header class="content-box">
            <nav class="navbar fixed-top navbar-dark bg-dark border-bottom shadow-lg">
                <div class="container-fluid p-2">
                    <div class="flex-grow-1 text-start" style="max-width: 5%;">
                        <a href="user/profile" class="btn-icon">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>

                    <div class="mx-auto">
                        <div class="fw-bold fs-2 text-primary">
                            Edit Profile <i class="fa fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <form action="user/edit_profile" method="post">
            <div class="card mb-4 bg-dark border-secondary">
                <div class="card-header border-secondary text-white fw-semibold bg-secondary bg-opacity-10">
                    Personal Information
                </div>

                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="full_name" class="form-label text-white fw-bold">Full Name *</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="full_name" name="full_name"
                            value="<?= htmlspecialchars($user->full_name) ?>" required>
                        <div class="form-text text-secondary">Your complete name as it should appear on your profile</div>
                    </div>

                    <div class="mb-2">
                        <label for="pseudo" class="form-label text-white fw-bold">Username *</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="pseudo" name="pseudo"
                            value="<?= htmlspecialchars($user->pseudo) ?>" required>
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
                            value="<?= htmlspecialchars($user->email) ?>" required>
                        <div class="form-text text-secondary">We'll use this email to send you notifications about your sales and purchases</div>
                    </div>

                    <div class="mb-2">
                        <label for="iban" class="form-label text-white fw-bold">IBAN <span class="text-secondary fw-normal">(Optional)</span></label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="iban" name="iban"
                            value="<?= isset($user->iban) ? htmlspecialchars($user->iban) : '' ?>"
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
    <footer class="content-box">
        <nav class="navbar fixed-bottom navbar-dark bg-dark border-top shadow-lg">
            <div class="container-fluid">

                <div class="d-flex justify-content-around w-100 py-3">

                    <a class="nav-link text-center text-secondary" href="item/browse">
                        <i class="fa-solid fa-magnifying-glass-plus fs-4 mb-1 d-block"></i>
                        <span>Browse</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="item/list">
                        <i class="fas fa-home fs-4 mb-1 d-block"></i>
                        <span>My Items</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="member/add_offer">
                        <i class="fa-solid fa-plus fs-4 mb-1 d-block"></i>
                        <span>Add Offer</span>
                    </a>

                    <a class="nav-link text-center text-secondary" href="profile/index">
                        <i class="fa-solid fa-gear fs-4 mb-1 d-block"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </div>
        </nav>
    </footer>
</body>

</html>