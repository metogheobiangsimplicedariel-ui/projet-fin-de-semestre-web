<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Studify' ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        // On lie le nom 'sans' à la police importée 'Nunito'
                        sans: ['Nunito', 'sans-serif'],
                    },
                    colors: {
                        studify: {
                            blue: '#9ACBEF',
                            bg: '#F2F9FB',
                            primary: '#5584B0',
                            primaryHover: '#436a8e',
                            red: '#FF5F5F',
                            input: '#F8F9FA'
                        }
                    }
                }
            }
        }
    </script>
</head>