<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <title>Reporte de usuarios </title>
</head>

<body>
    <?php foreach ($sql as $user) { ?>
    <div class="card col-12" >
        
            <div class="col-3">
                <div class="card-body">
                <img src="<?php echo 'users/img/' . $user->fotografia; ?>" width="100px" height="100px" alt="...">
            </div>
            </div>
            <div class="col-6">
                <div class="card-body">
                    <h5 class="card-title"><?php echo Purify::clean($user->nombre) . ' ' . Purify::clean($user->apellido); ?></h5>
                    <p class="card-text"><?php echo Purify::clean($user->telefono); ?></p>
                    <p class="card-text"><?php echo Purify::clean($user->email); ?></p>
                    <p class="card-text"><?php echo Purify::clean($user->permiso); ?></p>
                </div>
            </div>
        
    </div>


    <?php }?>
</body>

</html>
