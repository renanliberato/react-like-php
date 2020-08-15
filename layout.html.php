<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Material Design for Bootstrap fonts and icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">

    <!-- Material Design for Bootstrap CSS -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css" integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">
    <style rel="stylesheet">
        body {
            padding-top: 20px;
        }

        button {
            margin-bottom: 0!important;
        }

        form {
            margin-bottom: 0;
            margin-right: 0!important;
        }

        div {
            display: flex;
            flex-direction: column;
        }
    </style>
    <script src="https://unpkg.com/turbolinks@5.2.0/dist/turbolinks.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
    <script>
        Turbolinks.start()
        document.addEventListener("turbolinks:load", function() {
            if (window.scrollBeforeRedirect) {
                document.documentElement.scrollTop = document.body.scrollTop = window.scrollBeforeRedirect;
                window.scrollBeforeRedirect = null;
            }
            $('body').bootstrapMaterialDesign();
            $('input[type=checkbox].react-like-submittable').on('change', (e) => {
                $('#'+$(e.target).data('form-id')).submit();
                $('input[type=checkbox].react-like-submittable').off('change');
            })
            $('form.react-like-action').on('submit', function(e) {
                window.scrollBeforeRedirect = document.documentElement.scrollTop || document.body.scrollTop;
                const form = $(e.target);
                if (form.data('submitted')) {
                    return;
                }

                form.data('submitted', true);
                e.preventDefault();

                fetch(form.attr('action'), {
                    method: form.attr('method'),
                    body: new FormData(form[0])
                })
                .then((res) => res.text())
                .then(function(t) {
                    console.log(t);
                    // window.location.reload(true);
                    Turbolinks.visit("");
                }).catch(function(err) {
                    console.error(err);
                });
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <?= $app ?>
        <hr />
        <div style="flex-direction: row; align-self: center; margin-bottom: 20px;">
            Created by
            &nbsp;<a href="https://renanliberato.com.br/en">Renan Liberato</a>
            &nbsp;|&nbsp;<a href="https://github.com/renanliberato/react-like-php">Github repo</a>
        </div>
    </div>
</body>

</html>