
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studify Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
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
                    },
                    borderRadius: {
                        'giant': '100px',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        .tab-active {
            border-bottom: 3px solid #5584B0;
            color: #333;
        }
        .tab-inactive {
            color: #A0AEC0;
            border-bottom: 3px solid transparent;
        }
    </style>
</head>