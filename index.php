<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>jobsAPI</title>

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
</head>
<body>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">jobsAPI</a>
            </div>
        </div>
    </nav>

    <div>
        <?php

        require 'Api/Feeder.php';
        require 'Monitoring/Monitor.php';

        use Api\Feeder;
        use Monitoring\Monitor;

        $feeder  = new Feeder('localhost', 9200);
        $monitor = new Monitor($feeder); 
        $errors  = $monitor->getAllErrors();

        if (empty($errors) === FALSE) {
            foreach ($errors['hits']['hits'] as $error) {
                echo '<div class="alert alert-danger">Erreur de traitement :' . '<br />' .
                    '<b>' . $error['_source']['date'] . '</b><br /><br />' .

                    '<ul>';

                foreach ($error['_source']['exception'][0] as $category => $value) {
                    echo '<li>' . $category . ' : ' . $value . '</li>';
                }

                echo '</ul>' .
                '</div>';
            }
        }
        else {
            echo '<div class="alert alert-success">Pas d\'erreurs pour le moment</div>';
        }

        ?>
    </div>

    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</body>
</html>
