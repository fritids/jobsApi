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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <?php

                require 'Api/Feeder.php';
                require 'Monitoring/Monitor.php';

                use Api\Feeder;
                use Monitoring\Monitor;

                $feeder       = new Feeder('localhost', 9200);
                $monitor      = new Monitor($feeder); 
                $errors       = $monitor->getAllErrors();
                $errorCounter = 0;

                if (empty($errors) === FALSE) {
                    foreach ($errors['hits']['hits'] as $error) {
                        $errorCounter++;

                ?>
                        <div class="alert alert-danger" data-job="<?php echo $error['_source']['jobId']; ?>">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-1">
                                        <?php echo $errorCounter; ?>
                                    </div>

                                    <div class="col-md-3">
                                        <b><?php echo $error['_source']['websiteName']; ?> </b><br />
                                        Erreur de traitement (<?php echo $error['_source']['date']; ?>)
                                    </div>

                                    <div class="col-md-6">
                                        <ul>
                                            <?php

                                            foreach ($error['_source']['exception'][0] as $category => $value) {
                                                echo '<li>' . $category . ' : ' . $value . '</li>';
                                            }

                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php

                    }
                }
                else {
                    echo '<div class="alert alert-success">Pas d\'erreurs pour le moment</div>';
                }

                ?>
            </div>
        </div>
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</body>
</html>
