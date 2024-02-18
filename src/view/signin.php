<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Cloud - Sign in</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            border-bottom: 1px solid white;
        }
        .login-form {
            margin-top: 30px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .alert {
            cursor: pointer;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../public/">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" style="font-weight: 900; color: white;" href="index.php">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="color: white;" href="signup.php">Sign up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="login-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <?php if (isset($error_msg) && $error_msg != ""): ?>
                        <div class="alert alert-danger" onclick="this.remove()" role="alert"><?= $error_msg ?></div>
                    <?php endif; ?>

                    <?php if (isset($success_msg) && $success_msg != ""): ?>
                        <div class="alert alert-success" onclick="this.remove()" role="alert"><?= $success_msg ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">Sign in</div>
                        <div class="card-body">
                            <form id="signin_form">
                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-8">
                                        <input name="email" type="email" id="email" class="form-control" placeholder="user@example.com" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="pwd" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <div class="col-md-8">
                                        <input name="pwd" type="password" id="pwd" class="form-control" placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="recover" class="btn btn-link">Forgot Your Password?</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    <script src="js/pages/signin.js"></script>
    <script src="js/url.js"></script>
</body>

<script>
    const DOMAIN = '<?php echo $_ENV['DOMAIN']; ?>';
</script>

</html>

