<?php
    if(!isset($_SESSION['admin']) && !isset($_SESSION['comite']) && !isset($_SESSION['aceptado']) ){
        header('Location:../index.php');
    }else
    {
require('header.php')

?>
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3 class="text-center mb-3">CALENDARIO ACADÉMICO</h3>

            <div class="card bg-secondary shadow p-5 m-5">
                
                <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=2&bgcolor=%23ffffff&ctz=America%2FSantiago&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=0&src=OWR2ZWtkbDBkaGRlN3ZiMTg4ZWc4Mjcxb2dAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&color=%23EF6C00" style="border-width:0" width="800" height="600" frameborder="0" scrolling="no" class="table"></iframe>      </div>
            </div>
            
  </div>
<?php
    require('footer.php')

?>
<?php
}

?>
</html>
<?php
ob_end_flush();
?>