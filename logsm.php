<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link rel="stylesheet" href="content/style/stylelogsm.css">
        <link rel="stylesheet" href="content/style/header.css">
</head>
<body>
    <header class="header">
        <div class="headerkiri">
        <div class="logo-container">
            <img src="content/stripheader.png" alt="Logo" class="logo-clickable">
            <div class="dropdown-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="gudangInv.php">Ruang Inventaris</a>
                <a href="logsm.php">Data Log</a>
            </div>
        </div>
            <div class="title">Info Ruangan</div>
        </div>
        <div class="headertengah"> 
            <img src="content/LogoWareHouse.png" alt="Logo">
            <div class="title">O - Warehouse</div>
        </div>
        <div class="headerkanan">
            <img src="content/header-powerbutton.png" alt="Logo" style="width:65px;height:65px;">
        </div>
    </header>
</body>

<script>
        document.addEventListener('DOMContentLoaded', function() {
        const logo = document.querySelector('.logo-clickable');
        const dropdown = document.querySelector('.dropdown-menu');
        
        logo.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        
        // Menutup dropdown ketika klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.logo-container')) {
                dropdown.classList.remove('show');
            }
        });
    });
</script>
</html>